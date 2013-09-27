<?php

/**
 * An Exception to be used when a request to the Container is made for an 
 * object that is not found or being managed.
 *
 * @author Michael Irwin
 * @package container
 */
class NoSuchObjectException extends Exception {

  
  private $requestedObject;
  
  public function __construct($message, $requestedObject, $code = 0, Exception $previous = null) {
    parent::__construct($message, $code, $previous);
  
    $this->requestedObject = $requestedObject;
  }
  
  public function getRequestedObject() {
    return $this->requestedObject;
  }
}
