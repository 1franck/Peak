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
    	$this->assertTrue(is_integer($this->_d->getRecursivityDepth()),'$_recursivity_depth is not a integer');
    	$this->assertTrue(($this->_d->getActionsTriggered() === 0),'$_action_triggered should be 0'); 
    }
    
    function testOfSetRecursivity()
    {	
    	$this->_d->setRecursivity(true,3);
    	$this->assertTrue(($this->_d->getRecursivity()),'$_recursivity is not true');  
    	$this->assertTrue(($this->_d->getRecursivityDepth() == 3),'$_recursivity_depth should be 3');
    }
    
    function testOfStart()
    {
    	$this->_d->start();
    	$this->assertTrue(is_array($this->_d->response),'$response is not a valid array');
    	$this->assertTrue(($this->_d->getActionsTriggered() == 0),'$_action_triggered should be 0'); 
    	
    	$_GET['test1'] = 'abc';
    	
    	//START 1 - NO RECURSIVITY, 1 global
    	$this->_d->setRecursivity(false);
    	$this->_d->start();    	
    	
    	$this->assertTrue(($this->_d->response === 'test1'),'$response value should be a string with "test1" text');
    	$this->assertTrue(($this->_d->getActionsTriggered() == 1),'$_action_triggered should be 1'); 
    	
    	//RESET
    	$this->_d->reset();
    	$this->assertTrue(($this->_d->getActionsTriggered() == 0),'$_action_triggered should be 0');
    	
    	$_POST['test2'] = 'abc';
    	
    	$this->_d->setRecursivity(true);
    	$this->assertTrue(($this->_d->getRecursivityDepth() == 3),'$_recursivity_depth should be 3');
    	
    	//START 2 - RECURSIVITY, default depth(3)
    	$this->_d->start();
    	$this->assertTrue(($this->_d->response === 'test2'),'$response value should be a string with "test2" text');
    	$this->assertTrue(($this->_d->getActionsTriggered() == 2),'$_action_triggered should be 2');
    	
    	//RESET
    	$this->_d->reset();
    	$this->assertTrue(($this->_d->getActionsTriggered() == 0),'$_action_triggered should be 0');
    	
    	//START 3 - NO RECURSIVITY, multiple globals
    	$this->_d->setRecursivity(false);
    	$this->_d->start();
    	$this->assertTrue(($this->_d->response === 'test1'),'$response value should be a string with "test2" text');
    	$this->assertTrue(($this->_d->getActionsTriggered() == 1),'$_action_triggered should be 1'); 
    	
   	
    }

}


class dispatcherExample extends Peak_Dispatcher 
{
	
	public function _GET_test1()
	{
		$this->response = 'test1'; //echo '1';
	}
	
	public function _POST_test2()
	{
		$this->response = 'test2'; //echo '2';
	}
}