<?php

/**
 * Peak View Helpers Object containers
 *  
 * @author   Francois Lajoie 
 * @version  $Id$
 */
class Peak_View_Helpers
{
    
    private $_objects = array();

    /**
     * Retreive objects, try to create object if not already setted
     *
     * @param  string $name
     * @return object helper object
     */
	public function __get($name)
	{
		if(isset($this->_objects[$name])) return $this->_objects[$name];
		else 
		{
			$helper_prefix = 'Peak_View_Helper_';
			$name = trim(stripslashes(strip_tags($name)));
			$helper_file = VIEWS_HELPERS_ABSPATH.'/'.$name.'.php';
			$helper_class_name = $helper_prefix.$name;

			//if application views helpers file doesn't exists, we check internal Peak/View/Helpers/ folder
			if(!file_exists($helper_file)) {
				$helper_file = LIBRARY_ABSPATH.'/Peak/View/Helper/'.$name.'.php';
				$helper_class_name = $helper_prefix.$name;
			}

			if(file_exists($helper_file)) {
				include($helper_file);
				if(!class_exists($helper_class_name,false))	throw new Peak_Exception('ERR_VIEW_HELPER_NOT_FOUND',$name);				
				$this->_objects[$name] = new $helper_class_name(); 			
				return $this->_objects[$name];
			}
			else throw new Peak_Exception('ERR_VIEW_HELPER_NOT_FOUND',$name);			
		}
	}
	
	/**
	 * Check if $_objects key name exists
	 *
	 * @param  string $object_name
	 * @return bool
	 */
	public function __isset($object_name)
	{
		return (isset($this->_objects[$object_name])) ? true : false;
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