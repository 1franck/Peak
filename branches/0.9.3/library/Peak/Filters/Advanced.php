<?php
/**
 * Filters advanced class wrapper
 * This class help to validate data with multiple filters
 * 
 * @author  Francois Lajoie
 * @version $Id$
 * 
 * FILTERS LIST FOR VALIDATION. 
 * if you want more info on what a filter do, checkout the filter method docblock 
 *  ____________________________________________
 * | NAME      | OPTIONS* | METHOD NAME
 * |____________________________________________
 * |           |          |
 * | alpha     | lower    | _filter_alpha()   
 * |           | upper    |
 * |           | space    |
 * |           | punc     |
 * |           | french   |
 * |-------------------------------------------
 * | alpha_num | lower    | _filter_alpha_num()
 * |           | upper    |
 * |           | space    |
 * |           | punc     |
 * |           | french   |
 * |-------------------------------------------
 * | email     |          | _filter_email()
 * |-------------------------------------------
 * | empty     |          | _filter_empty()
 * |-------------------------------------------
 * | enum      | (array)  | _filter_enum()
 * |-------------------------------------------
 * | int       | min      | _filter_int()
 * |           | max      |
 * |-------------------------------------------
 * | float     | min      | _filter_float()
 * |           | max      |
 * |           | thousansd|
 * |-------------------------------------------
 * | length    | min      | _filter_length()
 * |           | max      |
 * |-------------------------------------------
 * | match     | (string) | _filter_match()
 * |-------------------------------------------
 * | not_empty |          | _filter_not_empty()
 * |-------------------------------------------
 * | text      |          | _filter_text()
 * |-------------------------------------------
 * | regexp    | (string) | _filter_regexp()
 * |___________________________________________
 * 
 * 
 * VALIDATION SPECIAL FILTERS
 *  ____________________________________________
 * | NAME          |  DESCRIPTION
 * |____________________________________________
 * | required      |  Generate an error if
 * |               |  data key not set
 * |-------------------------------------------
 * | if_not_empty  |  Process other filters if
 * |               |  data key is not empty.
 * |               |  Otherwise skip data key
 * |               |  without generating error
 * |___________________________________________
 * 
 *   *options can be array keyname or variable type
 * 
 * Note that you can also define your own filters in your extended class
 * by adding methods _filter_[youfiltername]
 */
abstract class Peak_Filters_Advanced extends Peak_Filters 
{

	/**
	 * Keep unknow key in $_data when using sanitize()
	 * If false, each key that exists in $_data but not in $_sanitize will be removed (default behavior of filter_* functions)
	 * @var bool
	 */
	protected $_keep_unknown_key_in_sanitize = false;


	/**
	 * Sanitize $_data using $_sanitize filters
	 * 
	 * @return array 
	 */
	public function sanitize()
	{	
		$filters = $this->_sanitize;

		if($this->_keep_unknown_key_in_sanitize) {
			$buffer_data = $this->_data;
		}

		$this->_data = filter_var_array($this->_data, $filters);

		if(isset($buffer_data)) {
			foreach($buffer_data as $k => $v) {
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
		$result = array();
		
		if(empty($this->_validate)) return true;

		foreach($this->_validate as $keyname => $keyval) {

			$i = 0;
			//we got a key to validate that do not exists in $_data
			//so we check if the keyname have 'required' filter name w/o error msg,
			//otherwise we simply skip the validation of the data keyname
			//without triggering an error
			if(!isset($this->_data[$keyname])) {
			    
				if(in_array('required',$keyval['filters'])) {
					//find key index for required filter
					foreach($keyval['filters'] as $fkey => $fval) {
						if($fval === 'required') { $findex = $fkey; break; }
					}
					if(isset($keyval['errors'][$findex])) {
						$this->_errors[$keyname] = $keyval['errors'][$findex];
					}
					else $this->_errors[$keyname] = 'required';
					continue;
				}
				else continue;
			}
			
			foreach($keyval['filters'] as $fkey => $fval) {
			    
			    //skip special filters and conditionnal filter
			    if($fval === 'required') continue;
			    elseif($fval === 'if_not_empty') {
			        if(!empty($this->_data[$keyname])) continue;
			        else break;
			    }

				if(is_int($fkey)) {
					$filter_name   = $fval;
					$filter_method = $this->_filter2method($filter_name);
					$filter_param  = null;
				}
				else {
					$filter_name  = $fkey;
					$filter_method = $this->_filter2method($filter_name);
					$filter_param  = $fval;
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
						if((isset($keyval['errors'])) && (is_array($keyval['errors'])) && (isset($keyval['errors'][$i]))) {
							$this->_errors[$keyname] = $keyval['errors'][$i];
						}
						else $this->_errors[$keyname] = 'not valid';
						//important! if a filter test fail, skip other test for the keyname
						break;
					}
				}
				else {
					throw new Peak_Exception('ERR_CUSTOM', get_class($this).': unknow filter name '.$filter_name);
				}
				
				unset($filter_result, $filter_param, $filter_method, $filter_name);

				++$i;
			}
		}

		return (empty($this->_errors)) ? true : false;	
	}

	/**
	 * Check if a filter name exists
	 *
	 * @param  string $name
	 * @return bool
	 */
	public function _filterExists($name)
	{
	    //special filters
	    if(in_array($name,array('required'))) return true;
		else return (method_exists($this, $this->_filter2method($name)));
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

		foreach($methods as $m) {
			if($this->_filter_regexp($m,'/^([_filter_][a-zA-Z]{1})/')) {
				$filters[] = $m;
			}
		}
		return $filters;
	}
	
		
	/**
	 * Set sanitize filters array
	 *
	 * @param array $filters
	 */
	public function setValidateFilters($filters)
	{
		$this->_validate = $filters;
	}
	
	/**
	 * Add a single sanitize data_name filter on the fly
	 *
	 * @param  array  $data_name
	 * @param  array  $filter
	 * @param  mixed  $errors
	 * @return object $this (for chaining)
	 */
	public function addValidateFilter($data_name, $filters, $errors = null)
	{
		if(!is_array($this->_validate)) $this->_validate = array();		
		$this->_validate[$data_name] = array('filters' => $filters, 'errors' => $errors);
		return $this;
	}
		
	/**
     * Check if data is not empty
     * 
     * @param  misc $v
     * @return bool
     */
	protected function _filter_not_empty($v)
	{
		return (empty($v)) ? false : true;
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
     * Check length of a string
     *
     * @param  string $v
     * @param  array  $opt keys supported: min, max
     * @return bool
     */
	protected function _filter_length($v, $opt)
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
	 * Checks if a value exists in an array
	 *
	 * @param  string $v
	 * @param  array $opt
	 * @return bool
	 */
	protected function _filter_enum($v, $opt)
	{
	    return (in_array($v,$opt));
	}

	/**
	 * Check for alpha char (a-z), with optionnaly space(s) and custom punctuation(s)
	 *
	 * @uses   FILTER_VALIDATE_REGEXP
	 * @param  string     $v
	 * @param  array|null $opt keys supported: lower, upper, space, punc. if null, lower and upper key are used
	 * @return bool
	 */
	protected function _filter_alpha($v, $opt = null, $return_regopt = false)
	{
		if(is_array($opt)) {
			$regopt = array();
			if(isset($opt['lower']) && ($opt['lower'] === true)) $regopt[] = 'a-z';
			if(isset($opt['upper']) && ($opt['upper'] === true)) $regopt[] = 'A-Z';
			if(isset($opt['french']) && ($opt['french'] === true)) $regopt[] = 'À-ÿ';
			if(empty($regopt)) $regopt = array('a-z','A-Z','À-ÿ');
			if(isset($opt['space']) && ($opt['space'] === true)) $regopt[] = '\s';
			if(isset($opt['punc']) && is_array($opt['punc'])) {
			    foreach($opt['punc'] as $punc) {
			        $regopt[] = '\\'.$punc;
			    }
			}
		}
		else $regopt = array('a-z','A-Z','À-ÿ');

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
	protected function _filter_alpha_num($v, $opt = null)
	{
		$regopt = $this->_filter_alpha(null, $opt, true);
		$regopt[] = '0-9';
		return filter_var($v, FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => '/^['.implode('',$regopt).']+$/')));
	}
	
	/**
	 * Same as _filter_alpha_num but some default punctuations/symbol 
	 * ().!-?_,;'"%$
	 *
	 * @param  string $v
	 * @return bool
	 */
	protected function _filter_text($v)
	{
	    $opt = array('space' => true, 'punc' => array('(', ')', '.', '!', '-', '?', '_', ',', ';', '\'','"','%','$'));
	    $regopt = $this->_filter_alpha(null, $opt, true);
		$regopt[] = '0-9';
		return filter_var($v, FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => '/^['.implode('',$regopt).']+$/')));
	}

    /**
     * Check for integer
     *
     * @uses   FILTER_VALIDATE_INT
     * @param  integer $v
     * @param  array   $opt keys supported: min, max. if null, no range is used
     * @return bool
     */
	protected function _filter_int($v, $opt = null)
	{
	    if(!isset($opt)) {
	        return filter_var($v, FILTER_VALIDATE_INT);
	    }
	    else {
	        if(filter_var($v, FILTER_VALIDATE_INT) !== false) {
	            $return = array();
	            if(isset($opt['min'])) {
	                $return['min'] = ($v >= $opt['min']) ? true : false;
	            }
	            if(isset($opt['max'])) {
	                $return['max'] = ($v <= $opt['max']) ? true : false;
	            }
	            foreach($return as $r) if($r === false) return false;
	            return true;
	        }
	        else return false;
	    }
	}

	/**
	 * Validate float number
	 *
	 * @param  float $v
	 * @param  array $opt
	 * @return bool
	 */
	protected function _filter_float($v, $opt = null)
	{
		if(!isset($opt)) {
	        return filter_var($v, FILTER_VALIDATE_FLOAT);
	    }
	    else {
	    	if(isset($opt['thousand'])) $flag = FILTER_FLAG_ALLOW_THOUSAND;
	    	else $flag = null;

	        if(filter_var($v, FILTER_VALIDATE_FLOAT, $flag) !== false) {
	            $return = array();
	            if(isset($opt['min'])) {
	                $return['min'] = ($v >= $opt['min']) ? true : false;
	            }
	            if(isset($opt['max'])) {
	                $return['max'] = ($v <= $opt['max']) ? true : false;
	            }
	            foreach($return as $r) if($r === false) return false;
	            return true;
	        }
	        else return false;
	    }
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