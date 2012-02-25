<?php
/**
 * Peak_Config_Json
 *
 * Takes a JSON encoded file/string and converts it into a PHP variable.
 * 
 * @author  Francois Lajoie
 * @version $Id$
 */
class Peak_Config_Json extends Peak_Config
{
	
	/**
	 * Load file on class construct
	 *
	 * @see loadFile()
	 */
	public function __construct($file = null)
	{
		if(isset($file)) $this->loadFile($file);
	}
	
	/**
	 * Load json file array
	 *
	 * @param  string $file
	 * @return array
	 */
	public function loadFile($file)
	{
		if(!file_exists($file)) throw new Peak_Exception('ERR_CUSTOM', __CLASS__.' has tried to load non-existent json file');
		else {
			$content = file_get_contents($file);
			return $this->loadString($content);
		}
	}
	
	/**
	 * Load json content
	 *
	 * @param  string $data
	 * @return array
	 */
	public function loadString($data)
	{
		$this->_vars = json_decode($data, true);
		$this->_jsonError();
		return $this->_vars;
	}
	
	/**
	 * Get last json error if exists
	 * PHP 5 >= 5.3.0
	 */
	private function _jsonError()
	{
		if(function_exists('json_last_error')) {

            //workaround JSON_ERROR_UTF8 --> PHP 5 >= 5.3.3
			if(!defined('JSON_ERROR_UTF8')) define('JSON_ERROR_UTF8',5);
			   
			switch(json_last_error()) {
				case JSON_ERROR_DEPTH:
					$e =  'Maximum stack depth exceeded';
					break;
				case JSON_ERROR_CTRL_CHAR:
					$e = 'Unexpected control character found';
					break;
				case JSON_ERROR_SYNTAX:
					$e = 'Syntax error, malformed JSON';
					break;
				case JSON_ERROR_STATE_MISMATCH:
					$e = 'Invalid or malformed JSON';
					break;
				case JSON_ERROR_UTF8:
					$e = 'Malformed UTF-8 characters, possibly incorrectly encoded'; //PHP 5 >= 5.3.3
					break;
			}
			
			if(isset($e)) throw new Peak_Exception('ERR_CUSTOM', __CLASS__.': '.$e);
		}
	}
	
}