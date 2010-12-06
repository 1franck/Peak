<?php

/**
 * Force http header file download
 * 
 * @author  Francois Lajoie
 * @version $Id$
 */
class Peak_View_Helper_Download 
{
	
	private $_file;
	
	
	/**
	 * Set file to attach
	 *
	 * @param string $file
	 */
	public function attach($file)
	{
		$this->_file = $file;
	}
		
	/**
	 * Execute the download. Return false if fail
	 *
	 * @return bool
	 */
	public function execute()
	{
		if(!file_exists($this->_file)) {
			return false;
		}
		elseif(!headers_sent()) {
			$filesize = filesize($this->_file);
			header('Pragma: public');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Cache-Control: private',false);
			header('Content-Type: application/force-download');
			header('Content-Type: application/octet-stream');
			header('Content-Type: application/download');
			header('Content-Disposition: attachment; filename="'.basename($this->_file).'";');
			header('Content-Transfer-Encoding:­ binary');
			header('Content-Length: '.$filesize);
			readfile($this->_file);
			return true;
		}
		else return false;
	}

}