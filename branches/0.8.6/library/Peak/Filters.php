<?php
/**
 * Filter base wrapper
 * 
 * @author  Francois Lajoie
 * @version $Id$
 */
abstract class Peak_Filters 
{
	/**
	 * Data on which we work 
	 * @var array
	 */
	protected $_data = array();

	/**
	 * Sanitize filters
	 * @var array
	 */
	protected $_sanitize;
	
	/**
	 * Global sanitize filters for all data
	 * @var array
	 */
	protected $_global_sanitize;

	/**
	 * Validate filters
	 * @var array
	 */
	protected $_validate;

	/**
	 * Errors found when validating
	 * @var array
	 */
	protected $_errors = array();

	/**
	 * 
	 */
	public function __construct()
	{		
		// call setUp method if exists
		if(method_exists($this, 'setUp')) $this->setUp();

		// call those methods if exists to gather validate and sanitize filters from child class
		if(method_exists($this,'setSanitization')) $this->_sanitize = $this->setSanitization();
		if(method_exists($this,'setValidation')) $this->_validate = $this->setValidation();
		if(method_exists($this,'setGlobalSanitization')) $this->_global_sanitize = $this->setGlobalSanitization();
	}

	/**
	 * Get data
	 *
	 * @return array
	 */
	public function getData()
	{
		return $this->_data;
	}

	/**
	 * Get sanitize filters var
	 *
	 * @return array
	 */
	public function getSanitizeFilters()
	{
		return $this->_sanitize;
	}
	
	/**
	 * Get global sanitize filter
	 *
	 * @return array
	 */
	public function getGlobalSanitizeFilter()
	{
	    return $this->_global_sanitize;
	}

	/**
	 * Get validate filters var
	 *
	 * @return array
	 */
	public function getValidateFilters()
	{
		return $this->_validate;
	}

	/**
	 * Get errors
	 *
	 * @return array
	 */
	public function getErrors()
	{
		return $this->_errors;
	}
	
	/**
	 * Sanitize all data with $_global_sanitize
	 */
	public function globalSanitize()
	{
	    $filter = isset($this->_global_sanitize['filter']) ? $this->_global_sanitize['filter'] : null;
	    $flags  = isset($this->_global_sanitize['flags'])  ? $this->_global_sanitize['flags']  : null;
	    
	    foreach($this->_data as $k => $v) {
	        $this->_data[$k] = filter_var($v, $filter, $flags);
	    }
	    
	    return $this->_data;
	}
}