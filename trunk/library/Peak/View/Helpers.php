<?php

/**
 * Peak View Helpers Object containers
 *  
 * @author   Francois Lajoie 
 * @version  $Id$
 */
class Peak_View_Helpers extends Peak_Helpers
{
    
    public function __construct()
    {
    	$this->_prefix    = 'View_Helper_';
    	
    	$this->_paths     = array(VIEWS_HELPERS_ABSPATH,
    			                  LIBRARY_ABSPATH.'/Peak/View/Helper');
    			                  
    	$this->_exception = 'ERR_VIEW_HELPER_NOT_FOUND';
    }
    
    public function __get($name)
    {
    	$helper = parent::__get($name);
    	$helper->view = Peak_Registry::obj()->view;
    	return $helper;
    }
    
}