<?php
$file_to_test = realpath('./../library/Peak/Config.php');
echo 'Tested file: '.$file_to_test.'<br />';
include($file_to_test);

class TestOfConfig extends UnitTestCase
{
    
    function testOfInstanciate()
    {
       $this->config = new Peak_Config();
       $this->assertTrue($this->config instanceof Peak_Config ,'config is not an object of Peak_Config');    
    }
    
    function testOfSetIsset() 
    {
    	$this->config->option1 = 'value1';
    	
    	$this->assertTrue(isset($this->config->option1) ,'config->option1 variable should return true'); 
    	
    	$this->assertTrue(isset($this->config->option1),'isset(config->option1) should return true');
    	
    	$this->assertFalse(isset($this->config->optionA),'isset(config->optionA) should return false');
    	
    	$this->assertNull($this->config->optionA,'config->optionA variable should return null');
    	
    	$this->assertIdentical($this->config->option1,'value1','config->option1 variable should return "value1"');
    	$this->assertNotIdentical($this->config->option1,'value2','config->option1 variable should return "value1"');
    }
    
    function testOfUnset()
    {
    	$this->assertTrue(isset($this->config->option1),'isset(config->option1) should return true');
    	
    	unset($this->config->option1);
    	
    	$this->assertFalse(isset($this->config->option1),'isset(config->option1) should return false');    	
    }
    
    function testOfCount()
    {
    	$this->assertTrue((count($this->config) == 0),'count(config) should return 0');
    	$this->config->option1 = 'value1';
    	$this->config->option2 = 'value2';
    	$this->assertTrue((count($this->config) == 2),'count(config) should return 2');    	
    }
    
    function testOfIterator()
    {   	    	
    	$iterator = $this->config->getIterator();
    	
    	$this->assertTrue($iterator instanceof ArrayIterator ,'getIterator() must be an instance of ArrayIterator');
    	
    	$this->assertTrue((count($iterator) == 2),'count($iterator) should return 2');
    	
    	//try to loop directly without getIterator
    	$i = 0;
    	foreach($this->config as $k => $v) {
    		++$i;
    	}
    	
    	$this->assertTrue(($i == 2),'foreach $this->config should count 2 item');
    	
    	$this->config->test = 'test';
    	$this->assertTrue((count($this->config) == 3),'count($this->config) should return 3');
    	
    }
      
  
}