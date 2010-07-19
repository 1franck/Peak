<?php

/**
 * Peak Controller Helpers Object containers
 *  
 * @author   Francois Lajoie 
 * @version  $Id$ 
 */
class Peak_Controller_Helpers
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
			$helper_name_prefix = 'ctrl_helper_';
			$name = trim(stripslashes(strip_tags($name)));
			$helper_file = CONTROLLERS_ABSPATH.'/helpers/'.$name.'.php';

			$new_helper = $helper_name_prefix.$name;

			if(!isset($this->_objects[$new_helper])) {
				if(file_exists($helper_file)) {
					include($helper_file);
					$this->_objects[$name] = new $new_helper();
				}
				else {
					throw new Peak_Exception('ERR_CTRL_HELPER_NOT_FOUND');
				}
			}
			
			return $this->_objects[$name];
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
    
}