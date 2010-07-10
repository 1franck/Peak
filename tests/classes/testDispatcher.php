<?php

$file_to_test = realpath('./../library/Peak/Dispatcher.php');
include($file_to_test);
echo 'Tested file: '.$file_to_test.'<br />';

class TestOfDispatcher extends UnitTestCase
{
    public $_d;
    
    function testOfInstanciate()
    {
    	$this->_d = new dispatcherExample();
    	
    	$this->assertTrue(is_a($this->_d,'Peak_Dispatcher') ,'$_d is not an object of Peak_Dispatcher');
    	        
    }
    
    function testOfProperties()
    {    	
    	$this->assertTrue(is_array($this->_d->getAcceptedGlobals()),'$_accepted_globals is not a valid array');
    	$actions = $this->_d->getActions();
    	$this->assertTrue(is_array($actions),'$_actions is not a valid array');   	
    	$this->assertTrue(is_bool($this->_d->getRecursivity()),'$_recursivity is not a boolean');   
    }
    
    function testOfSetRecursivity()
    {
    	$this->_d->setRecursivity(true,3);
    }
    

}


class dispatcherExample extends Peak_Dispatcher 
{
	
	public function __GET_test1()
	{
		
	}
}