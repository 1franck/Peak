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
	if (!file_exists($file)) { return false; }
	include($file);
}

function _autoloadAppMod($cn)
{
     $file = MODULES_ABSPATH .'/'.$cn.'/'.$cn.'.php';
     if (!file_exists($file)) return false;
     include($file);
}

function _autoloadAppCustom($cn)
{
	$strtopath = str_replace('_','/',$cn).'.php';
	$strtopath = str_replace('Application/','',$strtopath);
    $file = APPLICATION_ABSPATH.'/'.$strtopath;

    if(!file_exists($file)) return false;
    include($file);
}

spl_autoload_register('_autoloadPeak');
spl_autoload_register('_autoloadAppCtrl');
spl_autoload_register('_autoloadAppMod');
spl_autoload_register('_autoloadAppCustom');
spl_autoload_register('_autoloadZendInternal');
if(defined('ZEND_LIB_ABSPATH')) spl_autoload_register('_autoloadZend');