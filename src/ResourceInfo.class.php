<?php

/**
 * This class is a wrapper to be used by the Container to help store information
 * about each of the classes.
 *
 * @author Michael Irwin
 * @package container
 */
class ResourceInfo {

  /**
   * @var string
   */
  private $className;

  /**
   * @var string
   */
  private $reference;

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
  
}
