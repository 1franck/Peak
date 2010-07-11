<?php
$file_to_test = realpath('./../library/Peak/View.php');
include($file_to_test);
echo 'Tested file: '.$file_to_test.'<br />';

class TestOfView extends UnitTestCase
{
    public $view;
    
    
    
    function testOfInstanciate()
    {  	
    	 $this->view = new Peak_View();
    	 $this->assertTrue(is_a($this->view,'Peak_View') ,'$view is not an object of Peak_View');
    }
    
    function testOfProperties()
    {    	
    	$vars = $this->view->getVars();
    	$engine = $this->view->engine();
    	$this->assertTrue(is_array($vars),'$vars is not a valid array');
    	$this->assertTrue(empty($vars),'$vars is not empty');
    	$this->assertTrue(is_string($engine),'engine should be not set');
    }
    
    function testOfViewVars()
    {    	
    	$this->view->test = 'abc';
    	$this->assertTrue(($this->view->test === 'abc'),'setting var $test failed');
    	
    	unset($this->view->test);
    	$this->assertTrue(!isset($this->view->test),'$test variable should be unset');
    	$this->assertFalse(isset($this->view->test456),'$test456 variable should be not set');

    }
    
    function testOfViewMethod()
    {  	
    	define('DEV_MODE',true);    	
    	//$this->expectError($this->view->unknowmethod(),'unknow method should trigger error with DEV_MODE = true');   	
    	//$this->expectException($this->view->unknowmethod(),'unknow method should trigger error with DEV_MODE = true');   	
    		
    	$c = $this->view->countVars();
    	$this->assertTrue(is_integer($c),'countVars() should return integer');
    	$this->assertTrue(($c == 0),'countVars() should return 0');

    	$this->view->test = 'abc';
    	$c = $this->view->countVars();
    	$this->assertTrue(($c == 1),'countVars() should return 1');
    	
    	$this->view->resetVars();
    	$c = $this->view->countVars();
    	$this->assertTrue(($c == 0),'countVars() should return 0 after calling resetVars()');
    	
    	$vars = $this->view->getVars();
    	
    	

    	
    	
    	
    }
    
   
}