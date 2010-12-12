<?php
/**
 * Application boot preparations and configurations
 * 
 * @author   Francois Lajoie
 * @version  $Id$
 */

//define major abspath constants from root constant
define('SVR_ABSPATH', str_replace('\\','/',realpath($_SERVER['DOCUMENT_ROOT'])));
define('PUBLIC_ABSPATH', SVR_ABSPATH.'/'.PUBLIC_ROOT);
define('LIBRARY_ABSPATH', SVR_ABSPATH.'/'.LIBRARY_ROOT);
define('APPLICATION_ABSPATH', SVR_ABSPATH.'/'.APPLICATION_ROOT);
if(defined('ZEND_LIB_ROOT')) define('ZEND_LIB_ABSPATH',SVR_ABSPATH.'/'.ZEND_LIB_ROOT);

//load peak core and autoloader
include LIBRARY_ABSPATH.'/Peak/Core.php';
include LIBRARY_ABSPATH.'/Peak/autoload.php';

//init app&core configurations
if(defined('CONFIG_FILENAME')) {
	Peak_Core::initConfig(CONFIG_FILENAME);
	Peak_Core::initApp(APPLICATION_ABSPATH, LIBRARY_ABSPATH);
}
else throw new Peak_Exception('ERR_CUSTOM', 'No configuration have been specified!');
//die('No configuration have been specified!');

//add LIBRARY_ABSPATH to include path
set_include_path(implode(PATH_SEPARATOR, array(LIBRARY_ABSPATH, Peak_Core::getPath('libs'), get_include_path())));

//if ZEND_LIB_ABSPATH is specified, we add it to include path
if(defined('ZEND_LIB_ABSPATH')) {   
    set_include_path(implode(PATH_SEPARATOR, array(get_include_path(), ZEND_LIB_ABSPATH)));
}

//include application bootstrap if exists
if(file_exists(APPLICATION_ABSPATH.'/bootstrap.php')) include APPLICATION_ABSPATH.'/bootstrap.php';

//include application front extension if exists
if(file_exists(APPLICATION_ABSPATH.'/front.php')) include APPLICATION_ABSPATH.'/front.php';