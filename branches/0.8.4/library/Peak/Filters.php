<?php
/**
 * Filter extension wrapper for sanitization and/or validation of array
 * This class can help to validate/sanitize forms
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
	 * Errors msg gather from $_validate
	 * @var array
	 */
	protected $_errors_msg = array();

	
	/**
	 * Push array to the class for validation and sanitization
	 *
	 * @param array $data
	 */
	public function __construct($data)
	{
		if(!is_array($data)) trigger_error('MUST BE AN ARRAY');
		else {
			$this->_data = $data;
		}
		
		// call setUp method if exists
		if(method_exists($this, 'setUp')) $this->setUp();
		
		// call those method if exists to gather validate and sanitize filters from child class
		if(method_exists($this,'setSanitization')) $this->_sanitize = $this->setSanitization();
		if(method_exists($this,'setValidation')) $this->_validate = $this->setValidation();
	}
	
	/**
	 * Sanitize $_data using $_sanitize filters
	 * 
	 * @return array 
	 */
	public function sanitize()
	{	
		$filters = $this->_sanitize;
		
		$this->_data = filter_var_array($this->_data, $filters);
		
		return $this->_data;	
	}
	
	/**
	 * Validate $_data using $_validate filters
	 *
	 * @return bool
	 */
	public function validate()
	{
		$filters = $this->_array2def($this->_validate);
		
		$data = filter_var_array($this->_data, $filters);
		
		foreach($data as $k => $v)
		{			
	
			if((isset($this->_validate[$k]['flags']) && $this->_validate[$k]['flags'] == 134217728)) {
				if(is_null($v) || ($v === false)) {
					$this->_errors[$k] = 'fail';
				}
			}
			elseif($v === false) {
				$this->_errors[$k] = 'fail';
			}
			
			if(isset($this->_errors[$k]) && (isset($this->_errors_msg[$k]))) {
				$this->_errors[$k] = $this->_errors_msg[$k];
			}
		}
		
		if(!empty($this->_errors)) return false;
		else return true;
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
	 * Set error message for data keyname
	 * Usefull for FILTER_CALLBACK method
	 *
	 * @param string $name
	 * @param string $message
	 */
	protected function _setError($name, $message)
	{
		$this->_errors_msg[$name] = $message;
	}
	
	/**
	 * Shorcut of FILTER_VALIDATE_REGEXP
	 * Usefull for FILTER_CALLBACK methods
	 *
	 * @param  string $value
	 * @param  string $regexp
	 * @return string|false
	 */
	protected function _regexp($value, $regexp)
	{
		return filter_var($value, FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => $regexp)));
	}
	

	
	/**
	 * Transform an array to valid filters array
	 *
	 * @param  array  $array 
	 * @param  string $type 'sanitize' or 'validate'
	 * @return array
	 */
	private function _array2def($array)
	{
	
		if(is_array($array)) {
								
			foreach($array as $k => $v)
			{
				if(is_array($v)) {
										
					//errors (push errors string to $this->_errors_msg because they are not a part of filters definition
					if(isset($v['error'])) {
						$this->_errors_msg[$k] = $v['error'];
						unset($array[$k]['error']);
					}
				}
			}
			
			return $array;
		}
		return $this->_data;
	}
	
}