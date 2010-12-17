<?php
/**
 * TestHelper for PHPUnit
 * @version $Id$
 */

/**
 * PHPunit framework autoload
 */
require_once 'PHPUnit/Autoload.php';

/**
 * Add Peak to include path
 */
set_include_path(implode(PATH_SEPARATOR, array( dirname(__FILE__).'/../library', get_include_path())));