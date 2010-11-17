<?php
/**
 * Peak__Config_Json
 *
 * Takes a JSON encoded file/string and converts it into a PHP variable.
 * 
 * @author  Francois Lajoie
 * @version $Id$
 */
class Peak_Config_Json extends Peak_Config
{
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
		return $this->_vars;
	}
	
}