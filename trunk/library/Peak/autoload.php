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

//register spl functions
spl_autoload_register('_autoloadPeak');
spl_autoload_register('_autoloadAppCtrl');
spl_autoload_register('_autoloadZendInternal');
spl_autoload_register('_autoloadAppModules');
spl_autoload_register('_autoloadAppCustom');

if(defined('ZEND_LIB_ABSPATH')) spl_autoload_register('_autoloadZend');

function _autoloadPeak($cn)
{ 
    $strtopath = str_replace('_','/',$cn).'.php';

    $file = LIBRARY_ABSPATH.'/'.$strtopath;
    if(!file_exists($file)) return false;
    include ($file);
}

function _autoloadZend($cn)
{
    $strtopath = str_replace('_','/',$cn).'.php';
    
    //check external zend lib path
    if(file_exists(ZEND_LIB_ABSPATH.'/'.$strtopath)) {
        include(ZEND_LIB_ABSPATH.'/'.$strtopath);
    }
    else return false;
}

function _autoloadZendInternal($cn)
{
    $strtopath = str_replace('_','/',$cn).'.php';

    $file = Peak_Core::getPath('libs').'/'.$strtopath;
    
    //check internal zend lib (they have priority over external ZEND_LIB_ABSPATH)
    if(!file_exists($file)) return false;
    include($file);
}

function _autoloadAppCtrl($cn)
{
	$file = Peak_Core::getPath('controllers') .'/'.$cn.'.php';	
	if (!file_exists($file)) { return false; }
	include($file);
}

function _autoloadAppModules($cn)
{
	$strtopath = str_replace('_','/',$cn).'.php';
	$file = Peak_Core::getPath('modules') .'/'.$strtopath;
	
	if (!file_exists($file)) {
		$temp = explode('_',$cn);
		$name = array_shift($temp);
		$strtopath = implode('/',$temp); 
		$file = Peak_Core::getPath('modules') .'/'.$name.'/controllers/'.$strtopath.'.php';
		if (!file_exists($file)) return false;
	}
	include($file);
}

function _autoloadAppCustom($cn)
{
	$strtopath = str_replace('_','/',$cn).'.php';
	$strtopath = str_ireplace('application/','',$strtopath);
    $file = Peak_Core::getPath('application').'/'.$strtopath;

    if(!file_exists($file)) return false;
    include($file);
}