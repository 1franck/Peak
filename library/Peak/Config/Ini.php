<?php
/**
 * Peak_Config_Ini
 * 
 * This class allows you to:
 * - define multi-dimensional structures
 * - group configurations (e.g. production, development, testing, etc.) into separate sections (native INI sections)
 * - extend sections from one another
 * - override keys of extended sections in extending sections
 * 
 * @author  Original author: Andris at http://codeaid.net
 *          Thanks to him for letting me use and modify his class for the needs of the framework
 * @version $Id$
 */
class Peak_Config_Ini extends Peak_Config
{

	/**
	 * Parse ini from file
	 *
	 * @see _load()
	 */
	public function loadFile($filename, $process_sections = false, $section_name = null)
	{
		if(!file_exists($filename)) throw new Peak_Exception('ERR_CUSTOM', __CLASS__.' has tried to load non-existent ini file');
		else {
			$ini = parse_ini_file($filename, $process_sections);
			return $this->_load($ini, $process_sections, $section_name);
		}
	}
	
	/**
	 * Parse ini from a string (PHP 5 >= 5.3.0)
	 * 
	 * If you really need this under PHP 5 < 5.3.0, uncomment function 
	 * parse_ini_string() at the bottom of this file
	 * 
	 * @see _load()
	 */
	public function loadString($string, $process_sections = false, $section_name = null)
	{
		//PHP 5 >= 5.3.0
		$ini = parse_ini_string($string, $process_sections);
		return $this->_load($ini, $process_sections, $section_name);
	}
	
	/**
	 * Loads in the ini file specified in filename, and returns the settings in
	 * it as an associative multi-dimensional array
	 * 
	 * @param  string  $ini              Parsed content by php function parse_ini_*
	 * @param  boolean $process_sections By setting the process_sections parameter to TRUE,
	 *                                   you get a multidimensional array, with the section
	 *                                   names and settings included. The default for
	 *                                   process_sections is FALSE
	 * @param  string $section_name      Specific section name to extract upon processing
	 * @throws Peak_Exception
	 * @return array|boolean
	 */
	public function _load($ini, $process_sections = false, $section_name = null)
	{		
		// load the raw ini file 
		//$ini = parse_ini_string($data, $process_sections);

		// fail if there was an error while processing the specified ini file
		if ($ini === false)	return false;

		// reset the result array
		$this->_vars = array();

		if ($process_sections === true) {
			// loop through each section
			foreach ($ini as $section => $contents)	$this->_processSection($section, $contents);
		} else {
			// treat the whole ini file as a single section
			$this->_vars = $this->_processSectionContents($ini);
		}

		//  extract the required section if required
		if ($process_sections === true) {
			if ($section_name !== null) {
				// return the specified section contents if it exists
				if (isset($this->_vars[$section_name])) return $this->_vars[$section_name];
				else {
					throw new Peak_Exception('ERR_CUSTOM', __CLASS__.': Section ' . $section_name . ' not found in the ini file');
				}
			}
		}

		// if no specific section is required, just return the whole result
		return $this->_vars;
	}


	/**
	 * Process contents of the specified section
	 *
	 * @param string $section Section name
	 * @param array $contents Section contents
	 * @throws Exception
	 * @return void
	 */
	private function _processSection($section, array $contents)
	{
		// the section does not extend another section
		if (stripos($section, ':') === false) {
			$this->_vars[$section] = $this->_processSectionContents($contents);

		// section extends another section
		} else {
			// extract section names
			list($ext_target, $ext_source) = explode(':', $section);
			$ext_target = trim($ext_target);
			$ext_source = trim($ext_source);

			// check if the extended section exists
			if (!isset($this->_vars[$ext_source])) {
				throw new Peak_Exception('ERR_CUSTOM', __CLASS__.': Unable to extend section ' . $ext_source . ', section not found');
			}

			// process section contents
			$this->_vars[$ext_target] = $this->_processSectionContents($contents);

			// merge the new section with the existing section values
			$this->_vars[$ext_target] = $this->_arrayMergeRecursive($this->_vars[$ext_source], $this->_vars[$ext_target]);
		}
	}


	/**
	 * Process contents of a section
	 *
	 * @param array $contents Section contents
	 * @return array
	 */
	private function _processSectionContents(array $contents)
	{
		$result = array();

		// loop through each line and convert it to an array
		foreach ($contents as $path => $value) {
			// convert all a.b.c.d to multi-dimensional arrays
			$process = $this->_processContentEntry($path, $value);
			// merge the current line with all previous ones
			$result = $this->_arrayMergeRecursive($result, $process);
		}
		
		return $result;
	}


	/**
	 * Convert a.b.c.d paths to multi-dimensional arrays
	 *
	 * @param string $path Current ini file's line's key
	 * @param mixed $value Current ini file's line's value
	 * @return array
	 */
	private function _processContentEntry($path, $value)
	{
		$pos = strpos($path, '.');

		if($pos === false)	return array($path => $value);

		$key = substr($path, 0, $pos);
		$path = substr($path, $pos + 1);

		return array($key => $this->_processContentEntry($path, $value));
	}


	/**
	 * Merge two arrays recursively overwriting the keys in the first array
	 * if such key already exists
	 *
	 * @param mixed $a Left array to merge right array into
	 * @param mixed $b Right array to merge over the left array
	 * @return mixed
	 */
	private function _arrayMergeRecursive($a, $b)
	{
		// merge arrays if both variables are arrays
		if (is_array($a) && is_array($b)) {
			// loop through each right array's entry and merge it into $a
			foreach ($b as $key => $value) {
				if (isset($a[$key])) {
					$a[$key] = $this->_arrayMergeRecursive($a[$key], $value);
				} else {
					if($key === 0) $a= array(0 => $this->_arrayMergeRecursive($a, $value));
					else $a[$key] = $value;
				}
			}
		} 
		else $a = $b; // one of values is not an array

		return $a;
	}

}


//TEMPORARY FIX FOR PHP 5 < 5.3.0
//EMULATE parse_ini_string function from php 5.3.0
/*
if(!function_exists('parse_ini_string')){
    function parse_ini_string($str, $ProcessSections=false){
        $lines  = explode("\n", $str);
        $return = array();
        $inSect = false;
        foreach($lines as $line){
            $line = trim($line);
            if(!$line || $line[0] == '#' || $line[0] == ';') continue;
            if($line[0] == '[' && $endIdx = strpos($line, ']')){
                $inSect = substr($line, 1, $endIdx-1);
                continue;
            }
            // (We don't use "=== false" because value 0 is not valid as well)
            if(!strpos($line, '=')) continue;
            
            $tmp = explode('=', $line, 2);
            if($ProcessSections && $inSect) $return[$inSect][trim($tmp[0])] = ltrim($tmp[1]);
            else $return[trim($tmp[0])] = ltrim($tmp[1]);
        }
        return $return;
    }
}
*/