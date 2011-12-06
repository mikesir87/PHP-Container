<?php

/**
 * Provides all of the includes needed for the container to work.
 */

require('ClassInfo.class.php');
require('ClassInfoSet.class.php');
require('Container.class.php');
require('ResourceInfo.class.php');
require('ResourceInfoSet.class.php');
require('AutowireInfo.class.php');
require("ResourceBundle.interface.php");
require("NoSuchObjectException.class.php");

/**
 * Define the lcfirst function in the case that we're running in PHP 5.2
 */
if (!function_exists("lcfirst")) {
  function lcfirst($s) {
    $s{0} = strtolower($s{0});
    return $s;
  }
}
