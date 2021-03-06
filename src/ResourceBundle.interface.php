<?php

if (!interface_exists("ResourceBundle")) {

  /**
  * A ResourceBundle is a key => value store of resource properties.  These could
  * include database credentials or more.
  * @author Michael Irwin
  * @package container
  */
  interface ResourceBundle {

    /**
    * Get a resource property stored within this bundle.
    * @param string $variable The property to retreive.
    * @return string The property value.
    */
    public static function get($variable);
  }

}
