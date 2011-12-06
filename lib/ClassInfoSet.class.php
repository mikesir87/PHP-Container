<?php


/**
 * A wrapper/utility to hold all of the ClassInfo objects in the Container.
 *
 * @author Michael Irwin
 */
class ClassInfoSet {
  
  /**
   * The array of all ClassInfo objects.
   * @var ClassInfo[]
   */
  private $classInfoSet;
    
  /**
   * Private constructor.  Initializes the classInfoSet.
   */
  public function ClassInfoSet() {
    $this->classInfoSet = array();
  }
  
  /**
   * Add a class to the set of classes being managed.
   * @param ClassInfo $class The class to add.
   */
  public function addClass(ClassInfo $class) {
    $this->classInfoSet[$class->getReference()] = $class;
  }
  
  /**
   * Get a specific class by its reference.
   * @param string $className The reference for the class.
   * @return ClassInfo
   */
  public function getClass($className) {
    if (isset($this->classInfoSet[$className]))
      return $this->classInfoSet[$className];
    return null;
  }
  
  /**
   * Given either a class name, find the reference.
   * @param type $className
   * @return String The reference to the class.
   */
  public function findClass($className) {
    $temp = $this->getClass($className);
    if ($temp != null) return $className;
    
    foreach ($this->classInfoSet as $reference => $data) {
      if ($data->getClassName() == $className) {
        return $reference;
      }
    }
    return null;
  }
  
  /**
   * Update a class in the set by providing its object.
   * @param string $reference The reference to the class.
   * @param type $object 
   */
  public function assignObjectToClass($reference, $object) {
    if ($this->classInfoSet[$reference] == null)
      throw new InvalidArgumentException ("Cannot assign object to " . 
              $reference . " - no such object exists");
    $this->classInfoSet[$reference]->setObject($object);
  }

  /**
   * Get all of the ClassInfo objects that this Set has.
   * @return ClassInfo[]
   */
  public function getAllClasses() {
    return $this->classInfoSet;
  }
}

