<?php
/**
 * DEMO
 * @version $Id$
 */

/**
 * Usefull when app and library or outside of public route
 */
$path = substr(str_replace(array($_SERVER['DOCUMENT_ROOT'],basename(dirname(__FILE__))),'',str_replace('\\','/',dirname(__FILE__))), 0, -1);
@session_start('peak_framework_demo');
/**
 * OPTIONNAL CONSTANTS
 * Hint: This can be setted as well in .htaccess
 */
define('APPLICATION_ENV',  'development');
/**
 * REQUIRED CONSTANTS
 * Hint: *_ROOT constants reflect the RELATIVE path from the public folder root (the folder where this file is located)
 */
define('PUBLIC_ROOT', $path.'/www');
define('LIBRARY_ROOT', str_replace('demo','library',$path));
define('APPLICATION_ROOT', $path.'/app');
define('APPLICATION_CONFIG', 'app.ini');


/**
 * Load and start chrono for demo purpose
 */
include './../../library/Peak/Chrono.php';
Peak_Chrono::start();

/**
 * Load framework core
 */
include './../../library/Peak/Core.php';

/**
 * LANCH Application
 */
try {
    $app = Peak_Core::init(5);

    $app->run()
        ->render();   
}
catch (Peak_Controller_Exception $e) {
    $app->front->errorDispatch($e)
               ->render();
}
catch (Peak_Exception $e) {
    echo $e->getMessage();
    exit();
}