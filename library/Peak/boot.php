<?php

/**
 * Application boot preparations
 * Next file to include after your application configs.php 
 * 
 * @desc This file will add importants constants, load Peak_Core and call setPath(), call set_include_path 
 *       and finally include autoload.php
 * 
 * @author   Francois Lajoie
 * @version  $Id$
 */

// include Peak_Core and set system and application path if not already done
if(!defined('_VERSION_')) {
    include(realpath(dirname(__FILE__)).'\Core.php');
    
    define('SVR_ABSPATH', str_replace('\\','/',realpath($_SERVER['DOCUMENT_ROOT'])));
    define('ROOT_ABSPATH',SVR_ABSPATH.'/'.ROOT);
    define('LIBRARY_ABSPATH',SVR_ABSPATH.'/'.LIBRARY_ROOT);
    define('APPLICATION_ABSPATH',SVR_ABSPATH.'/'.APPLICATION_ROOT);
    if(defined('ZEND_LIB_ROOT')) define('ZEND_LIB_ABSPATH',SVR_ABSPATH.'/'.ZEND_LIB_ROOT);
    
    Peak_Core::setPath(APPLICATION_ABSPATH, LIBRARY_ABSPATH);
}

//add LIBS_ABSPATH to include path
set_include_path(implode(PATH_SEPARATOR, array(LIBS_ABSPATH, get_include_path())));

//if ZEND_LIB_ABSPATH is specified, we add it to include path
if(defined('ZEND_LIB_ABSPATH')) {   
    set_include_path(implode(PATH_SEPARATOR, array(get_include_path(), ZEND_LIB_ABSPATH)));
}

//*optionnal
//just load immediately files that anyway will be loaded at each execution of an application
//by doing this we save some autoload magic function calls and reduce lightly execution time 
include(LIBRARY_ABSPATH.'/Peak/Registry.php');
include(LIBRARY_ABSPATH.'/Peak/Router.php');
include(LIBRARY_ABSPATH.'/Peak/Application.php');
include(LIBRARY_ABSPATH.'/Peak/Controller.php');
include(LIBRARY_ABSPATH.'/Peak/View.php');
include(LIBRARY_ABSPATH.'/Peak/Lang.php');
include(LIBRARY_ABSPATH.'/Peak/Chrono.php');

//load wyn autoloader
include(LIBRARY_ABSPATH.'/Peak/autoload.php');

//include application bootstrap if exists
if(file_exists(APPLICATION_ABSPATH.'/bootstrap.php')) {
    include(LIBRARY_ABSPATH.'/Peak/Bootstrap.php');
    include(APPLICATION_ABSPATH.'/bootstrap.php');
}