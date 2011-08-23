<?php
/**
 * FOR DEMO PURPOSE ONLY
 */
$demo_path = substr(str_replace(array($_SERVER['DOCUMENT_ROOT'],'www'),'',str_replace('\\','/',dirname(__FILE__))), 0, -1);
//echo $demo_path.'<br />';

/**
 * REQUIRED CONSTANTS
 * Hint: *_ROOT constants reflect the relative path from the public folder (the folder where this file is located)
 */

define('PUBLIC_ROOT', $demo_path.'/www');
define('LIBRARY_ROOT', str_replace('demo','library',$demo_path));
define('APPLICATION_ROOT', $demo_path.'/app');
define('APPLICATION_CONFIG', 'app.ini');

/**
 * OPTIONNAL CONSTANTS
 * Hint: This can be setted as well in .htaccess
 */
define('APPLICATION_ENV',  'development');

/**
 * Load core
 */
include './../../library/Peak/Core.php';

/**
 * LANCH App
 */
try {
    $app = Peak_Core::init(5);

    $app->run()
        ->render();   
}
catch (Exception $e) {
    $app->front->errorDispatch($e)
               ->render();
}