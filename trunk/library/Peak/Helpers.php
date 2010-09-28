<?php

/**
 * Peak Abstract Helpers Object Containers
 *  
 * @author   Francois Lajoie 
 * @version  $Id$ 
 */
abstract class Peak_Helpers
{
    
	/**
	 * Class name prefix
	 * @var string
	 */
	protected $_prefix = '';
	
	/**
	 * Helpers file path(s)
	 * @var string
	 */
	protected $_paths = array();
	
	/**
	 * Helpers objects
	 * @var array
	 */
    protected $_objects = array();
    
    /**
     * Exception constant
     * @var string
     */
    protected $_exception = 'ERR_DEFAULT';

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
			$name = trim(stripslashes(strip_tags($name)));
			$helper_class_name = $this->_prefix.$name;

			$file_found = false;
			foreach($this->_paths as $k => $v)
			{
				$helper_file = $v.'/'.$name.'.php';
				if(file_exists($helper_file)) {
					$file_found = true;
					break;
				}
			}

			if($file_found) {
				include($helper_file);
				if(!class_exists($helper_class_name,false))	throw new Peak_Exception($this->_exception,$name);
				$this->_objects[$name] = new $helper_class_name();
				return $this->_objects[$name];
			}
			else throw new Peak_Exception($this->_exception,$name);
		}
	}
	
	/**
	 * Check if $_objects key name exists
	 *
	 * @param  string $object_name
	 * @return bool
	 */
	public function __isset($name)
	{
		return (isset($this->_objects[$name])) ? true : false;
	}
	
	/**
	 * Unset helper object
	 *
	 * @param string $name
	 */
	public function __unset($name)
	{
		if(array_key_exists($name,$this->_objects)) unset($this->_objects[$name]);
	}
	
	/**
	 * Check recursively if helper file exists
	 *
	 * @param  string $helper_name
	 * @return bool
	 */
	public function exists($name)
	{
		$file_found = false;
		foreach($this->_paths as $k => $v)
		{
			if(file_exists($v.'/'.$name.'.php')) {
				$file_found = true;	
				break;
			}
		}
		return ($file_found) ? true : false; 
	}
	
}