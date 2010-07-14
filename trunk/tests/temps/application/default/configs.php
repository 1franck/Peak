<?php

# --- APP GENERAL --- #

// your project name
define('PROJECT_NAME', 'testapp');  

// your project description                             
define('PROJECT_DESCR', 'tests application');                               

# --- APP SETTINGS --- #

// language preferences
define('APP_LANG','en');

// interface theme folder name (based in /application/appname/views/themes/)                                     
define('APP_THEME','default');

// controller by default
define('APP_DEFAULT_CTRL','homeController');

// enable error reporting / set to false on production
define('DEV_MODE', false);

# --- Server URL --- #

// server url ex: http://www.example.com
define('SVR_URL', 'http://');

# --- PATHS --- #

// public application relative folder
define('ROOT', 'peakdev/public_html');

// relative path to library folder of where is located Peak
define('LIBRARY_ROOT', 'peakframework/library');

// application relative path 
define('APPLICATION_ROOT', 'peakframework/tests/temps/application/default');

// zend framework library folder  of where is located Zend
define('ZEND_LIB_ROOT', '');

# --- PEAK --- #
define('ENABLE_PEAK_CONTROLLERS', false);