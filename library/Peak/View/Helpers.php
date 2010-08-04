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
    
    /**
	 * Check if helper file exists
	 *
	 * @param  string $helper_name
	 * @return bool
	 */
	public function exists($helper_name)
	{
		$helper_file = VIEWS_HELPERS_ABSPATH.'/'.$helper_name.'.php';
		if(!file_exists($helper_file)) {
			$helper_file = LIBRARY_ABSPATH.'/Peak/View/Helper/'.$helper_name.'.php';
			return (file_exists($helper_file)) ? true : false;
		}
		else return true;
	}
    
}