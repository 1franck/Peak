<?php
return "<?php

# --- APP GENERAL --- #

// your project name
define('PROJECT_NAME', '".$data['PROJECT_NAME']."');  

// your project description                             
define('PROJECT_DESCR', '".$data['PROJECT_DESCR']."');                               

# --- APP SETTINGS --- #

// controller by default
define('APP_DEFAULT_CTRL','".$data['APP_DEFAULT_CTRL']."');

// enable error reporting / set to false on production
define('DEV_MODE', ".$data['DEV_MODE'].");

# --- Server URL --- #

// server url ex: http://www.example.com
define('SVR_URL', '".$data['SVR_URL']."');

# --- PATHS --- #

// public application relative folder
define('ROOT', '".$data['ROOT']."');

// relative path to library folder of where is located Peak
define('LIBRARY_ROOT', '".$data['LIBRARY_ROOT']."');

// application relative path 
define('APPLICATION_ROOT', '".$data['APPLICATION_ROOT']."');

// zend framework library folder  of where is located Zend
define('ZEND_LIB_ROOT', '".$data['ZEND_LIB_ROOT']."');

# --- PEAK --- #
define('ENABLE_PEAK_CONTROLLERS', ".$data['ENABLE_PEAK_CONTROLLERS'].");";