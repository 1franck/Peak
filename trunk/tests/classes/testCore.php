<?php
@session_destroy();
@session_start();
$_SESSION = array();
include(realpath('./classes/initPeakMVC.php'));

$file_to_test = realpath('./../library/Peak/Core.php');
//include($file_to_test);
echo 'Tested file: '.$file_to_test.'<br />';

class TestOfCore extends UnitTestCase
{
    
    function testOfInstanciate()
    {
        $this->core = Peak_Registry::obj()->core;
        $this->assertTrue(is_a($this->core,'Peak_Core') ,'$core is not an object of Peak_Core');
    }
       
    function testOfControllers()
    {
    	$this->assertTrue($this->core->isController('homeController') ,'isController() should return true');
    	$this->assertFalse($this->core->isController('testController') ,'isController() should return false');
    	
    	$c = $this->core->getControllers();
    	$this->assertTrue(is_array($c) ,'$c should be an array');
    	$this->assertTrue((count($c) == 1) ,'controllers count should return 1');
    }
    
    function testOfModules()
    {
    	$this->assertTrue($this->core->isModule('test') ,'isModule() should return true');
    	$this->assertFalse($this->core->isModule('unknow') ,'isModule() should return false');
    	
    	$m = $this->core->getModules();
    	$this->assertTrue(is_array($m) ,'$m should be an array');
    	$this->assertTrue((count($m) == 1) ,'modules count should return 1');
    }
    
    function testOfPaths()
    {
    	$p = $this->core->getPaths();
    	$this->assertTrue(is_array($p) ,'$p should be an array');
    	
    	$p = Peak_Core::getPaths();
    	$this->assertTrue(is_array($p) ,'$p should be an array');
    }
    
    function testOfExt()
    {
    	$ext = $this->core->ext('lang');
    	$this->assertTrue(is_a($ext,'Peak_Core_Lang') ,'$ext is not an object of Peak_Core_Lang');
    }
  

}

