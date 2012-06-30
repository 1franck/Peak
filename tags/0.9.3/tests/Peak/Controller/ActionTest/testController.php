<?php
/**
 * Controller class test
 */
class testController extends Peak_Controller_Action 
{ 
	
	public function preAction()
	{ 
		$this->view->preactiontest = 'value';	
	}
	
	public function _index()
	{
		$this->view->actiontest = 'default value';
	}
		
	public function _contact()
	{ 
		$this->view->actiontest = 'contact value';
	}
	
	public function postAction()
	{
		$this->view->postactiontest = 'value';
	}

}
