<?php

/**
 * Peak SPL autoload
 * 
 * @author   Francois Lajoie
 * @version  $Id$
 */

//nullify any existing autoloads
spl_autoload_register(null, false);

//specify extensions that may be loaded
spl_autoload_extensions('.php');


function _autoloadPeak($cn)
{ 
    $strtopath = str_replace('_',DIRECTORY_SEPARATOR,$cn).'.php';

    $file = LIBRARY_ABSPATH.'/'.$strtopath;
    if(!file_exists($file)) return false;
    include ($file);
}

function _autoloadZend($cn)
{
    $strtopath = str_replace('_',DIRECTORY_SEPARATOR,$cn).'.php';
    
    //check external zend lib path
    if(file_exists(ZEND_LIB_ABSPATH.'/'.$strtopath)) {
        include(ZEND_LIB_ABSPATH.'/'.$strtopath);
    }
    else return false;
}

function _autoloadZendInternal($cn)
{
    $strtopath = str_replace('_',DIRECTORY_SEPARATOR,$cn).'.php';
    $file = LIBS_ABSPATH.'/'.$strtopath;
    
    //check internal zend lib (they have priority over external ZEND_LIB_ABSPATH)
    if(!file_exists($file)) return false;
    include($file);
}

function _autoloadAppCtrl($cn)
{
     $file = CONTROLLERS_ABSPATH .'/'.$cn.'.php';
     if (!file_exists($file)) {
     	$file = CONTROLLERS_ABSPATH .'/'.$cn.'Controller.php';
     	if (!file_exists($file)) { return false; }
     }
     include($file);
}

function _autoloadAppMod($cn)
{
     $file = MODULES_ABSPATH .'/'.$cn.'/'.$cn.'.php';
     if (!file_exists($file)) return false;
     include($file);
}

spl_autoload_register('_autoloadPeak');
spl_autoload_register('_autoloadAppCtrl');
spl_autoload_register('_autoloadAppMod');
spl_autoload_register('_autoloadZendInternal');
if(defined('ZEND_LIB_ABSPATH')) spl_autoload_register('_autoloadZend');
 


function _autoload_err($cn,$file)
{
    set_error_handler('catch_autoload_err');
    trigger_error('Class name '.$cn.' not found in '.dirname($file), E_USER_WARNING);
    exit();
}


/**
 * ERROR handler for __autoload
 *
 * @param integer $errno
 * @param string  $errstr
 * @param string  $errfile
 * @param integer $errline
 */
function catch_autoload_err($errno, $errstr, $errfile, $errline)
{
    $traceinfos = array('Message' => $errstr,
    'Code' => $errno,
    'Line' => $errline,
    'File' => $errfile);

    $trace = debug_backtrace();

    $err_propagation = array();
    foreach($trace as $i => $v) {
        if(isset($v['file']) && isset($v['line'])) $err_propagation[$v['line']] = $v['file'];
    }

    //echo $errstr.' - '.basename($errfile).' ('.$errline.')<br />Trace:<br />';
    echo '<div>'.$errstr.'</div><br />Trace:<br />';
    foreach($err_propagation as $line => $file) echo '- '.$file.' (Line: '.$line.')<br />';

    if((defined('DEV_MODE')) && (DEV_MODE)) {
        echo '<h3>Trace dump</h3><pre>';
        print_r($traceinfos);
        print_r($trace);
        echo '</pre>';
    }
}