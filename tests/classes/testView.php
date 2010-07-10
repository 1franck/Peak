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
    	$this->assertTrue(is_array($vars),'$vars is not a valid array');
    	$this->assertTrue(empty($vars),'$vars is not empty');
    	
    	/*$actions = $this->_d->getActions();
    	$this->assertTrue(is_array($actions),'$_actions is not a valid array');   	
    	$this->assertTrue(is_bool($this->_d->getRecursivity()),'$_recursivity is not a boolean');   
    	$this->assertTrue(is_integer($this->_d->getRecursivityDepth()),'$_recursivity_depth is not a integer');
    	$this->assertTrue(($this->_d->getActionsTriggered() === 0),'$_action_triggered should be 0'); */
    }
    
   
}