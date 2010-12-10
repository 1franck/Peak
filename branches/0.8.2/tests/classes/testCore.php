<?php
@session_destroy();
@session_start();
$_SESSION = array();
include(realpath('./classes/initPeakMVC.php'));

$file_to_test = realpath('./../library/Peak/Core.php');
echo 'Tested file: '.$file_to_test.'<br />';

class TestOfCore extends UnitTestCase
{
    
    function testOfInstanciate()
    {
        $this->core = Peak_Registry::obj()->core;
        $this->assertTrue(is_a($this->core,'Peak_Core') ,'$core is not an object of Peak_Core');
        
        
    }
    
    function testOfPaths()
    {
    	$p = $this->core->getPath('library');
    	$this->assertTrue($p === LIBRARY_ABSPATH ,'getPath(\'library\') should be the same as LIBRARY_ABSPATH');
    	
    	$p = Peak_Core::config('library_path');
    	$this->assertTrue($p === LIBRARY_ABSPATH ,'config(\'library_path\') should be the same as LIBRARY_ABSPATH');
    	
    	$p = Peak_Core::config('non_exists_config');
    	$this->assertTrue($p === null ,'config(\'non_exists_config\') should be NULL');   	
    }
    
    function testOfConfigs()
    {
    	Peak_Core::config('test_config', '12345678');
    	$this->assertTrue((Peak_Core::config('test_config') === '12345678') ,'config(\'test_config\') should return 12345678');
    	$this->assertTrue((Peak_Registry::o()->core_config->test_config === '12345678') ,'Peak_Registry::o()->core_config->test_config should return 12345678');
    	
    }
    
    function testOfExt()
    {
    	$ext = $this->core->lang();
    	$this->assertTrue(($ext instanceof Peak_Core_Extension_Lang) ,'$ext is not an object of Peak_Core_Extension_Lang');
    	
    }
    
    
       
    function testOfControllers()
    {
    	$this->assertTrue($this->core->isController('homeController') ,'isController() should return true');
    	$this->assertFalse($this->core->isController('testController') ,'isController() should return false');
    	
    	$this->assertTrue($this->core->isInternalController('pkedit') ,'isInternalController() should return true');
    	$this->assertFalse($this->core->isInternalController('pkadmdsdsdsdin') ,'isInternalController() should return false');

    	$c = $this->core->getControllers();
    	$this->assertTrue(is_array($c) ,'$c should be an array');
    	$this->assertTrue((count($c) == 1) ,'controllers count should return 1');  	
    }
    
    function testOfModules()
    {
    	//$this->assertTrue($this->core->isModule('test') ,'isModule() should return true');
    	//$this->assertFalse($this->core->isModule('unknow') ,'isModule() should return false');
    	
    	/*
    	$m = $this->core->getModules();
    	$this->assertTrue(is_array($m) ,'$m should be an array');
    	$this->assertTrue((count($m) == 1) ,'modules count should return 1');*/
    }
    
    
  

}

