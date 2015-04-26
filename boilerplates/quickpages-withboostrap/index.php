<?php
/**
 * YOUR APP
 * 
 * @version $Id$
 */

/**
 * where we are
 */
$path    = str_replace(array($_SERVER['DOCUMENT_ROOT']),'',str_replace('\\','/',dirname(__FILE__)));
$libpath = '../../';

/**
 * Start the session
 */
@session_start('yourapp');

/**
 * Environment
 */
define('APPLICATION_ENV',  'development');

/**
 * Required constants
 */
define('PUBLIC_ROOT',        $path);
define('LIBRARY_ROOT',       $path.$libpath);
define('APPLICATION_ROOT',   $path.'/app');
define('APPLICATION_CONFIG', 'app.ini');


/**
 * Load and start chrono
 */
include $libpath.'/library/Peak/Chrono.php';
Peak_Chrono::start();

/**
 * Load framework core
 */
include $libpath.'/library/Peak/Core.php';

/**
 * Launch Application
 */
try {
    $app = Peak_Core::init(5);

    $app->run()
        ->render();   
}
/**
 * Catch exception
 */
catch (Exception $e) {

    $app->front->errorDispatch($e)
               ->render();
}