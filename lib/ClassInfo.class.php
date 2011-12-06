<?php

/**
 * This class is a wrapper to be used by the Container to help store information
 * about each of the classes.  When a ManagedClass attribute is used, the
 * configuration is stored in the Container using an instance of this class.
 *
 * @author Michael Irwin
 */
class ClassInfo {

  /**
   * The name of the Class that is being managed.
   * @var string
   */
  private $className;

  /**
   * The name that is to be used in the Container.  For example, a UserRepoDB
   * class could be referenced as a UserRepo in the container.
   * @var string
   */
  private $reference;
  
  /**
   * All AutowireInfo objects associated with this class. What dependencies
   * does this class have?
   * @var AutowireInfo[]
   */
  private $autowiringProperties;
  
  /**
   * If this class is a singleton, stores the fully constructed object.
   * @var Class
   */
  private $object;
  
  /**
   * True if the object is a singleton. Otherwise, a new instance is created
   * each time a request is made to the Container. Mark a class as a Singleton
   * by using the SharedClass attribute.
   * @var boolean
   */
  private $isSingleton = false;
  
  /**
   * If true, the Container automatically creates an instance after parsing all
   * classes without waiting for a first request.
   * @var boolean
   */
  private $autoCreate = false;

  /**
   * Get the name of the class.
   * @return string
   */
  public function getClassName() {
    return $this->className;
  }

  /**
   * Set the name of the class.
   * @param string $className The name of the class.
   */
  public function setClassName($className) {
    $this->className = $className;
  }

  /**
   * Get the name of the class.
   * @return array An array of properties to be autowired.
   */
  public function getAutowiringProperties() {
    return $this->autowiringProperties;
  }

  /**
   * Set the properties that should be autowired in this class.
   * @param array $autowiringProperties The properties to autowire.
   */
  public function setAutowiringProperties($autowiringProperties) {
    $this->autowiringProperties = $autowiringProperties;
  }

  /**
   * Get the insantiation of the object stored.
   * @return string
   */
  public function getObject() {
    return $this->object;
  }

  /**
   * Set the object that should be stored (used in the case of a singleton).
   * @param Class $object The object to store.
   */
  public function setObject($object) {
    $this->object = $object;
  }

  /**
   * Is this class supposed to be a singleton?
   * @return boolean True if only one instance should exist.
   */
  public function isSingleton() {
    return $this->isSingleton;
  }

  /**
   * Change whether this class should be a singleton.  Default is false.
   * @param boolean $isSingleton True if only one instance should be stored.
   */
  public function setIsSingleton($isSingleton) {
    $this->isSingleton = $isSingleton;
  }

  /**
   * Get the reference that this class should be referred to by.
   * @return string The reference for this class.
   */
  public function getReference() {
    return $this->reference;
  }

  /**
   * Set the method by which this class should be referenced.
   * @param string $reference The reference for this class (how others should
   * refer to it).
   */
  public function setReference($reference) {
    $this->reference = $reference;
  }
  
  public function getAutoCreate() {
    return $this->autoCreate;
  }

  public function setAutoCreate($autoCreate) {
    $this->autoCreate = $autoCreate;
  }

}
