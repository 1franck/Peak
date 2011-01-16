<?php
/**
 * Form validation base class, support sanitize with php filter extension
 * This class help to validate/sanitize forms
 * 
 * @author  Francois Lajoie
 * @version $Id$
 */
abstract class Peak_FormValidation 
{
	/**
	 * Data on which we work 
	 * @var array
	 */
	protected $_data = array();
	
	/**
	 * Form method (post or get)
	 * @var string
	 */
	protected $_method = 'post';
	
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
	 * Keep unknow key in $_data when using sanitize()
	 * If false, each key that exists in $_data but not in $_sanitize will be removed (default behavior of filter_* functions)
	 * @var bool
	 */
	protected $_keep_unknow_sanitize_key = false;

	
	/**
	 * Push array to the class for validation and sanitization
	 *
	 * @param array $data
	 */
	public function __construct()
	{	
		// call setUp method if exists
		if(method_exists($this, 'setUp')) $this->setUp();
		
		if($this->_method = 'post') {
			$this->_data = $_POST;
		}
		else {
			$this->_data = $_GET;
		}
		
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
		
		if($this->_keep_unknow_sanitize_key) {
			$buffer_data = $this->_data;
		}
		
		$this->_data = filter_var_array($this->_data, $filters);
		
		if(isset($buffer_data)) {
			foreach($buffer_data as $k => $v)
			{
				if(!isset($this->_data[$k])) {
					$this->_data[$k] = $v;
				}
			}
		}
		
		return $this->_data;	
	}
	
	/**
	 * Validate $_data using $_validate filters
	 *
	 * @return bool
	 */
	public function validate()
	{
		//echo '<pre>data:'.print_r($this->_data,true).'<br />';
		
		$result = array();
		
		foreach($this->_validate as $keyname => $keyval) {
			
			$i = 0;
			
			foreach($keyval['filters'] as $subkey => $subkey_val) {
				
				if(is_int($subkey)) {
					$filter_name = $subkey_val;
					$filter_method = $this->_filter2method($filter_name);
					$filter_param = null;
				}
				else {
					$filter_name = $subkey;
					$filter_method = $this->_filter2method($filter_name);
					$filter_param  = $subkey_val;
				}
						
				if($this->_filterExists($filter_name)) {
					
					if(is_null($filter_param)) {
						$filter_result = $this->$filter_method($this->_data[$keyname]);
					}
					else {
						$filter_result = $this->$filter_method($this->_data[$keyname],$filter_param);
						if((bool)$filter_result === false) $filter_result = false;
					}
					
					if($filter_result === false) {
						//$result[$keyname] = $filter_result;
						if((is_array($keyval['errors'])) && (isset($keyval['errors'][$i]))) {
							$this->_errors[$keyname] = $keyval['errors'][$i];
						}
						else $this->_errors[$keyname] = 'not valid';
						//important! if a filter test fail, skip other test
						break;
					}
					//else $result[$keyname] = true;
						
				}
				unset($filter_result, $filter_param, $filter_method, $filter_name);
			
				++$i;
			}
		}
		
		//echo '<br />Result:';
		//print_r($result);
		//echo '<br />Errors'.print_r($this->_errors,true).'</pre>';
		
		if(empty($this->_errors)) return true;
		else return false;		
	}
	
	/**
	 * Check if a filter name exists
	 *
	 * @param  string $name
	 * @return bool
	 */
	public function _filterExists($name)
	{
		return (method_exists($this, $this->_filter2method($name)));
	}
	
	/**
	 * Get filter method name
	 *
	 * @param  string $name
	 * @return string
	 */
	public function _filter2method($name)
	{
		return '_filter_'.$name;
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
	 * Get errors
	 *
	 * @return array
	 */
	public function getErrors()
	{
		return $this->_errors;
	}
	
	
	protected function _filter_not_empty($v)
	{
		if(empty($v)) return false;
		else return true;
	}
	
	protected function _filter_empty($v)
	{
		return empty($v);
	}
	
	protected function _filter_lenght($v, $opt)
	{
		if(isset($opt['min'])) $min = $opt['min'];
		if(isset($opt['max'])) $max = $opt['max'];
		if(isset($min) && !isset($max)) {
			return (strlen($v) >= $min) ? true : false;
		}
		elseif(isset($max) && !isset($min)) {
			return (strlen($v) <= $max) ? true : false;
		}
		else {
			return ((strlen($v) >= $min) && (strlen($v) <= $max)) ? true : false;
		}
	}
	
	protected function _filter_email($v)
	{
		return filter_var($v, FILTER_VALIDATE_EMAIL);
	}
	
	protected function _filter_word($v)
	{
		return filter_var($v, FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => '/^[a-zA-Z0-9]*$/')));
	}
	
	protected function _filter_words($v)
	{
		return filter_var($v, FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => '/^[a-zA-Z0-9\s]*$/')));
	}
	
	protected function _filter_int($v)
	{
		return filter_var($v, FILTER_VALIDATE_INT);
	}
	
	protected function _filter_match_key($v, $opt)
	{
		return ($v === ($this->_data[$opt])) ? true : false;
	}
		
	protected function _filter_regexp($v, $regexp)
	{
		return filter_var($v, FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => $regexp)));
	}
	
	protected function _filter_range($v, $opt)
	{
		//(is_array($opt))
	}
	
	
}