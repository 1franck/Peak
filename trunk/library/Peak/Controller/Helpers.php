<?php

/**
 * Peak Controller Helpers Object containers
 *  
 * @author   Francois Lajoie 
 * @version  $Id$ 
 */
class Peak_Controller_Helpers extends Peak_Helpers
{
    
	/**
	 * Overload helpers properties
	 */
    public function __construct()
    {
    	$this->_prefix    = 'Controller_Helper_';
    	
    	$this->_paths     = array(Peak_Core::getPath('controllers_helpers'),
    			                  LIBRARY_ABSPATH.'/Peak/Controller/Helper');
    			                  
    	$this->_exception = 'ERR_CTRL_HELPER_NOT_FOUND';
    }
    
    /**
     * Unkown method in Controller Helper will try to call current controller __call() method.
     * So you can load another controllers helpers inside helper
     *
     * @param  string $method
     * @param  array $args
     * @return misc
     */
    public function  __call($method, $args = null)
    {
    	$app = Peak_Registry::obj()->app;
        return call_user_func_array(array($app->controller, $method), $args);
    }

    
}