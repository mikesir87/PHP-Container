<?php

/**
 * Holds information about a property that is to be autowired.  When a property
 * in a class has the Autowired attribute, it's setup is stored in the
 * Container using this object.
 *
 * @author Michael Irwin
 * @package container
 */
class AutowireInfo {
  
  /**
   * The name of the property in the Class.
   * @var string
   */
  private $propertyName;
  
  /**
   * The name of its dependency to be found in the Container
   * @var string
   */
  private $reference;
  
  /**
   * Will this property be injected with an object or a resource object?
   * @var int From AutowireTypes
   */
  private $autowireType;
  
  
  public function getPropertyName() {
    return $this->propertyName;
  }

  public function setPropertyName($propertyName) {
    $this->propertyName = $propertyName;
  }

  public function getReference() {
    return $this->reference;
  }

  public function setReference($reference) {
    $this->reference = $reference;
  }

  public function getAutowireType() {
    return $this->autowireType;
  }

  public function setAutowireType($autowireType) {
    if (AutowireTypes::isDefined($autowireType))
      $this->autowireType = $autowireType;
    else
      throw new InvalidArgumentException("Invalid AutowireType.");
  }

}

/**
 * Defines constants to be used in the AutowireInfo class.
 */
final class AutowireTypes {
    const OBJECT = 1;
    const RESOURCE = 2;
    
    private static $constants;
    
    public static function isDefined($value) {
      if (self::$constants == null) {
        $class = new ReflectionClass("AutowireTypes");
        self::$constants = $class->getConstants();
      }
      return in_array($value, self::$constants);
    }
}
