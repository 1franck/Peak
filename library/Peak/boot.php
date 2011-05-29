<?php
/**
 * Application boot preparations and configurations
 * 
 * @author   Francois Lajoie
 * @version  $Id$
 */

//define major abspath constants from root constant

//fix the problem with $_SERVER['DOCUMENT_ROOT'] not ending by / in some case
$svr_path = str_replace('\\','/',realpath($_SERVER['DOCUMENT_ROOT']));
if(substr($svr_path, -1, 1) !== '/') $svr_path .= '/';
define('SVR_ABSPATH', $svr_path); unset($svr_path);

define('PUBLIC_ABSPATH', SVR_ABSPATH . PUBLIC_ROOT);
define('LIBRARY_ABSPATH', realpath(SVR_ABSPATH . LIBRARY_ROOT));
define('APPLICATION_ABSPATH', realpath(SVR_ABSPATH . APPLICATION_ROOT));
if(defined('ZEND_LIB_ROOT')) define('ZEND_LIB_ABSPATH',SVR_ABSPATH.ZEND_LIB_ROOT);

//load peak core and autoloader
include LIBRARY_ABSPATH.'/Peak/Core.php';
include LIBRARY_ABSPATH.'/Peak/autoload.php';

//init app&core configurations
if(defined('APPLICATION_CONFIG')) {
	Peak_Core::initConfig(APPLICATION_CONFIG, APPLICATION_ABSPATH);
}
else throw new Peak_Exception('ERR_CUSTOM', 'No configuration have been specified!');

//add LIBRARY_ABSPATH to include path
set_include_path(implode(PATH_SEPARATOR, array(LIBRARY_ABSPATH, LIBRARY_ABSPATH.'/Peak/Libs', get_include_path())));

//if ZEND_LIB_ABSPATH is specified, we add it to include path
if(defined('ZEND_LIB_ABSPATH')) {   
    set_include_path(implode(PATH_SEPARATOR, array(get_include_path(), ZEND_LIB_ABSPATH)));
}

//include application bootstrap if exists
if(file_exists(APPLICATION_ABSPATH.'/bootstrap.php')) include APPLICATION_ABSPATH.'/bootstrap.php';

//include application front extension if exists
if(file_exists(APPLICATION_ABSPATH.'/front.php')) include APPLICATION_ABSPATH.'/front.php';