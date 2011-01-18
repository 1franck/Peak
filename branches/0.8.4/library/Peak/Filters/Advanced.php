<?php
/**
 * Filters advanced class wrapper
 * This class help to validate data with multiple filters
 * 
 * @author  Francois Lajoie
 * @version $Id$
 */
abstract class Peak_Filters_Advanced extends Peak_Filters 
{
		
	/**
	 * Keep unknow key in $_data when using sanitize()
	 * If false, each key that exists in $_data but not in $_sanitize will be removed (default behavior of filter_* functions)
	 * @var bool
	 */
	protected $_keep_unknow_sanitize_key = false;

		
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
	 * Get class filters list
	 */
	public function getFiltersList()
	{
		$filters = array();
		$methods = get_class_methods($this);
		
		foreach($methods as $method) {
			if($this->_filter_regexp($method,'/^([_filter_][a-zA-Z]{1})/')) {
				$filters[] = $method;
			}
		}
		return $filters;
	}
	
	/**
     * Check if data is not empty
     * 
     * @param  misc $v
     * @return bool
     */
	protected function _filter_not_empty($v)
	{
		if(empty($v)) return false;
		else return true;
	}

    /**
     * Check if data is empty
     *
     * @param  misc $v
     * @return bool
     */
	protected function _filter_empty($v)
	{
		return empty($v);
	}

    /**
     * Check lenght of a string
     *
     * @param  string $v
     * @param  array  $opt keys supported: min, max
     * @return bool
     */
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

    /**
     * Check if valid email
     *
     * @uses   FILTER_VALIDATE_EMAIL
     * @param  string $v
     * @return bool/string
     */
	protected function _filter_email($v)
	{
		return filter_var($v, FILTER_VALIDATE_EMAIL);
	}
	
	/**
	 * Check for alpha char (a-z)
	 *
	 * @uses   FILTER_VALIDATE_REGEXP
	 * @param  string     $v
	 * @param  array|null $opt keys supported: lower, upper. if null both key are used
	 * @return bool
	 */
	protected function _filter_alpha($v, $opt = null, $return_regopt = false)
	{
		if(is_array($opt)) {
			$regopt = array();
			if(isset($opt['lower']) && ($opt['lower'] === true)) { $regopt[] = 'a-z'; }
			if(isset($opt['upper']) && ($opt['upper'] === true)) { $regopt[] = 'A-Z'; }
			if(empty($regopt)) $regopt = array('a-z','A-Z');
		}
		else {
			$regopt = array('a-z','A-Z');
		}
		if($return_regopt) return $regopt;
		return filter_var($v, FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => '/^['.implode('',$regopt).']+$/')));
	}
	
	/**
	 * Same as _filter_alpha but support number(s)
	 * 
	 * @uses   FILTER_VALIDATE_REGEXP
	 * @param  string $v
	 * @param  array  $opt
	 * @return bool
	 */
	protected function _filter_alpha_num($v,$opt = null)
	{
		$regopt = $this->_filter_alpha(null, $opt, true);
		$regopt[] = '0-9';
		return filter_var($v, FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => '/^['.implode('',$regopt).']+$/')));
	}


    /**
     * Check for integer
     *
     * @uses   FILTER_VALIDATE_INT
     * @param  integer $v
     * @return bool
     */
	protected function _filter_int($v)
	{
		return filter_var($v, FILTER_VALIDATE_INT);
	}

    /**
     * Check if data match with another $_data key
     *
     * @param  string $v
     * @param  string $opt
     * @return bool
     */
	protected function _filter_match_key($v, $opt)
	{
		return ($v === ($this->_data[$opt])) ? true : false;
	}

    /**
     * Check for a regular expression
     *
     * @uses   FILTER_VALIDATE_REGEXP
     * @param  string $v
     * @param  string $regexp
     * @return bool
     */
	protected function _filter_regexp($v, $regexp)
	{
		return filter_var($v, FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => $regexp)));
	}
		
}