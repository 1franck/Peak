<?php

/**
 * Validate Filters extension wrapper
 *
 * @author  Francois Lajoie
 * @version $Id$
 */
class Peak_Controller_Helper_Validate
{
	
	private $_regexp = array('string' => '/^[a-zA-Z0-9]+$/');
	

	/**
	 * Validate boolean
	 *
	 * @param  string $data
	 * @param  string $flag [NULL_ON_FAILURE]
	 * @return bool
	 */
	public function bool($data, $flag = null)
	{
		$flag = $this->_flags($flag);		
		return filter_var($data, FILTER_VALIDATE_BOOLEAN, $flag);
	}
	
	/**
	 * Validate email
	 *
	 * @param  string $data
	 * @return bool
	 */
	public function email($data)
	{
		return filter_var($data, FILTER_VALIDATE_EMAIL);
	}
	
	
	/**
	 * Validate float
	 *
	 * @param  misc         $data
	 * @param  string|array $flag [ALLOW_THOUSAND]
	 * @param  string       $decimal
	 * @return unknown
	 */
	public function float($data, $flag = null, $decimal)
	{
		$flag = $this->_flags($flag);
		if(isset($decimal)) {
			$options = array('options' => array('decimal' => $decimal));
		}
		else $options = null;
		return filter_var($data, FILTER_VALIDATE_FLOAT, $flag, $options);
	}
	
	/**
	 * Validate integer
	 *
	 * @param  misc         $data
	 * @param  string|array $flag [ALLOW_OCTAL|ALLOW_HEX]
	 * @param  integer      $min
	 * @param  integer      $max
	 * @return bool
	 */
	public function int($data, $flag = null, $min = null, $max = null)
	{
		$flag = $this->_flags($flag);
		
		if(isset($min) || isset($max)) {
			$options = array('options' => array('min_range' => $min, 'max_range' => $max));
		}
		else $options = null;
		return filter_var($data, FILTER_VALIDATE_INT, $flag, $options);
	}

	/**
	 * Validate ip address
	 *
	 * @param  string       $data
	 * @param  string|array $flag [IPV4|IPV6|NO_PRIV_RANGE|NO_RES_RANGE]
	 * @return bool
	 */
	public function ip($data, $flag = null)
	{		
		$flag = $this->_flags($flag);
		
		return filter_var($data, FILTER_VALIDATE_IP, $flag);
	}
	
	/**
	 * Validate regular expression
	 *
	 * @param  string $data
	 * @param  string $regex
	 * @return bool
	 */
	public function regexp($data, $regex)
	{
		return filter_var($data, FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => $regex)));
	}
	
	/**
	 * Validate URL
	 *
	 * @param  string       $data
	 * @param  string|array $flag [PATH_REQUIRED|QUERY_REQUIRED]
	 * @return bool
	 */
	public function url($data, $flag = null)
	{
		$flag = $this->_flags($flag);
		return filter_var($data, FILTER_VALIDATE_URL, $flag);
	}
	
	/**
	 * Tranfors flag name and array flags names to constants FILTER_FLAG_*
	 *
	 * @param  string|array $name
	 * @return const(s) 
	 */
	private function _flags($name)
	{
		if(is_array($name)) {
			foreach($name as $i => $n) {

				$cn = 'FILTER_FLAG_'.strtoupper($n);
				if($i == 0) {
					if(defined($cn)) $flags = constant($cn);
				}
				elseif(isset($flags)) {
					if(defined($cn)) {
						$flags = $flags | constant($cn);
					}
				}
				else {
					if(defined($cn)) $flags = constant($cn);
				}
			}
			if(!isset($flags)) $flags = null;
			return $flags;
		}

		$constant_string = 'FILTER_FLAG_'.strtoupper($name);
		if(defined($constant_string)) return constant($constant_string);
		else return null;
	}
	
	
}