<?php
/**
 * Class Helpers Examples
 */
class MyHelpers extends Peak_Helpers
{
	
	public function __construct()
	{
		
		$this->_prefix    = array('MyHelper_', 'Helper_');
    	
    	$this->_paths     = array(dirname(__FILE__).'/helpers');
    			                  
    	$this->_exception = 'ERR_CUSTOM';

	}
}