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
	 * @var string|array
	 */
	protected $_prefix = '';
	
	/**
	 * Helpers file path(s)
	 * @var array
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
     * Exception class
     * @var string
     */
    protected $_exception_class = 'Peak_Exception';

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

			$file_found = false;
			foreach($this->_paths as $k => $v) {
				$helper_file = $v.'/'.$name.'.php';
				if(file_exists($helper_file)) {
					$file_found = true;
					break;
				}
			}

			if($file_found) {
				include_once $helper_file;
				
				if(!is_array($this->_prefix)) $this->_prefix = array($this->_prefix);
		
				foreach($this->_prefix as $prefix) {
					if(!class_exists($prefix.$name,false)) continue;
					else {
						$helper_class_name = $prefix.$name;
						break;
					}
				}
				if(!isset($helper_class_name)) throw new $this->_exception_class($this->_exception,$name);
				
				$this->_objects[$name] = new $helper_class_name();
				return $this->_objects[$name];
			}
			else throw new $this->_exception_class($this->_exception,$name);
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
	 * Check recursively if helper file exists based on $_path
	 *
	 * @param  string $helper_name
	 * @return bool
	 */
	public function exists($name)
	{
		$file_found = false;
		foreach($this->_paths as $k => $v) {
			if(file_exists($v.'/'.$name.'.php')) {
				$file_found = true;	
				break;
			}
		}
		return $file_found; 
	}
	
}