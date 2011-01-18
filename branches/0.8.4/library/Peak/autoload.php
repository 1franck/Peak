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
    $file = LIBRARY_ABSPATH.'/'._autoloadClass2File($cn);
    if(!file_exists($file)) return false;
    include $file;
}

function _autoloadAppCtrl($cn)
{
	$file = Peak_Core::getPath('controllers') .'/'.$cn.'.php';	
	if(!file_exists($file)) return false;
	include $file;
}
   
//check internal zend lib (they have priority over external ZEND_LIB_ABSPATH)
function _autoloadZendInternal($cn)
{
    //$file = Peak_Core::getPath('libs').'/'._autoloadClass2File($cn);
    $file = LIBRARY_ABSPATH.'/Peak/Libs/'._autoloadClass2File($cn);
    if(!file_exists($file)) return false;
    include $file;
}

function _autoloadAppModules($cn)
{
	$file = Peak_Core::getPath('modules') .'/'._autoloadClass2File($cn);
	
	if (!file_exists($file)) {
		$temp = explode('_',$cn);
		$name = array_shift($temp);
		$strtopath = implode('/',$temp); 
		$file = Peak_Core::getPath('modules') .'/'.$name.'/controllers/'.$strtopath.'.php';
		if (!file_exists($file)) return false;
	}
	include $file;
}

function _autoloadAppCustom($cn)
{
	$strtopath = str_ireplace('app/','',_autoloadClass2File($cn));
    $file = Peak_Core::getPath('application').'/'.$strtopath;

    if(!file_exists($file)) return false;
    include $file;
}

//check external zend lib path
function _autoloadZend($cn)
{
    $file = ZEND_LIB_ABSPATH.'/'._autoloadClass2File($cn);
    if(!file_exists($file)) return false;
    include $file;
}

function _autoloadClass2File($cn)
{
	return str_replace('_','/',$cn).'.php';
}