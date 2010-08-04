<?php

/**
 * Peak Controller Helpers Object containers
 *  
 * @author   Francois Lajoie 
 * @version  $Id$ 
 */
class Peak_Controller_Helpers extends Peak_Helpers
{
    
    public function __construct()
    {
    	$this->_prefix    = 'Controller_Helper_';
    	
    	$this->_paths     = array(CONTROLLERS_HELPERS_ABSPATH,
    			                  LIBRARY_ABSPATH.'/Peak/Controller/Helper');
    			                  
    	$this->_exception = 'ERR_CTRL_HELPER_NOT_FOUND';
    }
    
}