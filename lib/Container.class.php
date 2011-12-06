<?php

/**
 * Creates a container that allows for dependency injection in the application.
 * In order to work with the container, classes can use the following 
 * annotations:
 *
 *   - Class Level Annotations
 *     - @ManagedClass  - reference defaults to the name of the class
 *     - @ManagedClass("referenceName") - define the reference name
 *     - @SharedClass - defines the class as a Singleton in the Container (only
 *         one instantiation of the object will be performed
 *   - Property level
 *     - NOTE: In order for a class to use Autowiring, the @ManagedClass
 *         annotation must be used.
 *     - @Autowired - signifies that the property is to be autowired, using a
 *         reference with the same name as the property.
 *     - @Autowired("referenceName") - defines the property to be autowired 
 *         using a given referenceName
 *     - @ResourceSetting("class.variable") - defines an autowiring using a 
 *         property from a class.
 * 
 * @author Michael Irwin
 */
class Container {

    /**
     * The ClassInfoSet, which has all information about the classes to be
     * managed.
     * @var ClassInfoSet
     */
    protected $classes;
    
    /**
     * @var ResourceInfoSet
     */
    protected $resources;

    /**
     * The singleton instance of the Container.
     * @var Container
     */
    protected static $instance;
    
    /**
     * Works in a Singleton manner.
     * @return Container The current, and only, instance of Container.
     */
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new Container();
        }
        return self::$instance;
    }
    
    /**
     * Instantiates the Container.  Loads all classes currently in the system 
     * and figures out what needs autowiring, etc.
     */
    private function Container() {
      $this->classes = new ClassInfoSet();
      $this->resources = new ResourceInfoSet();
      $this->loadAllClasses();
      foreach($this->classes->getAllClasses() as $class) {
        /* @var $class ClassInfo */
        if ($class->getAutoCreate()) {
          $this->setupClass($class);
        }
      }
    }
    
    /**
     * Get a wired component from the Container.  If a class is a shared class,
     * it returns the stored copy of that object.
     * 
     * For example, to retrieve a DB, you can call Container::getInstance()->db
     * It will, if needed, create the object and inject any needed dependencies
     * into it.
     * 
     * @param string $className The class to retrieve.
     * @return type 
     */
    public function __get($className) {
      $classReference = $this->classes->findClass($className);
      if ($classReference == null) 
        throw new NoSuchObjectException("Class reference for " . $className 
                . " could not be found.", $className);
      
      $class = $this->classes->getClass($classReference);
      
      if ($class->isSingleton() == true) {
        if ($class->getObject() == null) {
          $obj = $this->setupClass($class);
          $this->classes->assignObjectToClass($classReference, $obj);
          return $obj;
        }
        return $class->getObject();
      }
      else {
        return $this->setupClass($class);
      }
      return null;
    }
    
    /**
     * Function that navigates through all declared classes and determines
     * autowiring needs and ManagedClasses.
     * @return ClassInfoSet Array of ClassInfo, which contains information about
     * the properties of each class.
     */
    private function loadAllClasses() {
      $classes = get_declared_classes();
      foreach ($classes as $class) {
        $this->evaluateClass($class);
      }
    }
    
    /**
     * Evaluates a class and determines if it is managed, has autowiring, etc.
     * @param string $className The name of the class to evaluate.
     */
    private function evaluateClass($className) {
      $class = new ReflectionClass($className);
      $docComment = $class->getDocComment();
      if (preg_match('/\*\s+@ManagedClass(\("(\w+)"\))?/', $docComment, $matches)) {
        $reference = (count($matches) == 1) ? lcfirst($className) : $matches[2];
        $single = (preg_match('/\*\s+@(SharedClass|RequestScoped)/', $docComment, 
                $matches)) ? true : false;
        $autoCreate = (preg_match('/\*\s+@(AutoCreate)/', $docComment, 
                $matches)) ? true : false;
        
        $thisClass = new ClassInfo();
        $thisClass->setReference($reference);
        $thisClass->setClassName($className);
        $thisClass->setAutowiringProperties($this->getManagedProperties($class));
        $thisClass->setObject(null);
        $thisClass->setIsSingleton($single);
        $thisClass->setAutoCreate($autoCreate);
        
        $this->classes->addClass($thisClass);
      }
      else if (preg_match('/\*\s+@ResourceBundle(\("(\w+)"\))?/', $docComment, $matches)) {
        $reference = (count($matches) == 1) ? lcfirst($className) : $matches[2];
        $resource = new ResourceInfo();
        $resource->setClassName($className);
        $resource->setReference($reference);
        $this->resources->addResource($resource);
      }
      return null;
    }

    /**
     * Look at a class and determine what properties are to be autowired.
     * @param ReflectionClass $class The class to evaluate.
     * @return Array An array of the properties that are to be autowired.
     */
    private function getManagedProperties(ReflectionClass $class) {
      $managedProperties = array();
      $properties = $class->getProperties();
      foreach ($properties as $property) {
        $docComment = $property->getDocComment();
        if (preg_match('/\*\s+@Autowired(\("(\w+)"\))?/i', $docComment, $matches)) {
          $reference = (count($matches) == 1) ? lcfirst($property->getName()) : $matches[2];
          $autowire = new AutowireInfo();
          $autowire->setAutowireType(AutowireTypes::OBJECT);
          $autowire->setPropertyName($property->getName());
          $autowire->setReference($reference);
          $managedProperties[] = $autowire;
        }
        else if (preg_match('/\*\s+@ResourceSetting\("(.+)"\)/i', $docComment, $matches)) {
          $reference = $matches[1];
          $autowire = new AutowireInfo();
          $autowire->setAutowireType(AutowireTypes::RESOURCE);
          $autowire->setPropertyName($property->getName());
          $autowire->setReference($reference);
          $managedProperties[] = $autowire;
        }
      }
      return $managedProperties;
    }
    
    /**
     * Creates a new instantation of a class and performs any needed dependency
     * injections on it.
     * @param ClassInfo $class The class to base off of and build with.
     * @return Class An instantiated object of the class represented by $class.
     */
    private function setupClass($class) {
      $className = $class->getClassName();
      $obj = new $className();

      if (count($class->getAutowiringProperties()) == 0)
        return new $obj;
      
      foreach($class->getAutowiringProperties() as $wire) {
        /* @var $wire AutowireInfo */
        if ($wire->getAutowireType() == AutowireTypes::OBJECT) {
          $setVariable = "set" . ucfirst($wire->getPropertyName());
          if (method_exists($obj, $setVariable)) 
            $obj->$setVariable( $this->{$wire->getReference()} );
          else
            throw new InvalidArgumentException ("Function $setVariable doesn't 
                    exist on " . $class->getClassName());
        }
        else if ($wire->getAutowireType() == AutowireTypes::RESOURCE) {
          $setVariable = "set" . ucfirst($wire->getPropertyName());
          if (preg_match_all("/(\w+)/", $wire->getReference(), $matches)) {
            if (count($matches) == 2) {
              $class = $matches[0][0];  $variable = $matches[0][1];
              $resource = $this->resources->getResource($class)->getClassName();
              $value = call_user_func(array($resource, "get"), $variable);
              $obj->$setVariable($value);
            }
          }
          else {
            throw new InvalidArgumentException("Don't know how to interpret " . 
                    $wire->getPropertyName());
          }
        }
      }
      if (method_exists($obj, "postConstruct"))
              $obj->postConstruct();
      return $obj;
    }
}
