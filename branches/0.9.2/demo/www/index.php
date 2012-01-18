<?php
/**
 * DEMO
 * @version $Id$
 */

/**
 * FOR DEMO PURPOSE ONLY
 */
$demo_path = substr(str_replace(array($_SERVER['DOCUMENT_ROOT'],basename(dirname(__FILE__))),'',str_replace('\\','/',dirname(__FILE__))), 0, -1);
@session_start();
/**
 * OPTIONNAL CONSTANTS
 * Hint: This can be setted as well in .htaccess
 */
define('APPLICATION_ENV',  'development');

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
 * REQUIRED CONSTANTS
 * Hint: *_ROOT constants reflect the RELATIVE path from the public folder root (the folder where this file is located)
 */
define('PUBLIC_ROOT', $demo_path.'/www');
define('LIBRARY_ROOT', str_replace('demo','library',$demo_path));
define('APPLICATION_ROOT', $demo_path.'/app');
define('APPLICATION_CONFIG', 'app.ini');

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