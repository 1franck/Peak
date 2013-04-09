<?php
/**
 * Manage http redirection for url and controller
 * 
 * @authors  Francois Lajoie
 * @version  $Id: redirect.php 453 2011-10-03 00:22:42Z snake386@hotmail.com $
 */
class Peak_View_Helper_Redirect
{
	
	private $_status_codes = array('300' => 'Multiple Choices',
	                               '301' => 'Moved Permanently',
	                               '302' => 'Found',
	                               '303' => 'See Other',
	                               '304' => 'Not Modified',
	                               '305' => 'Use Proxy',
	                               '307' => 'Temporary Redirect',
	                               '310' => 'Too many Redirect'); 
	                               

	/**
	 * Redirect to an url
	 *
	 * @param string  $url
	 * @param integer $status
	 */
	public function url($url, $status = 302)
	{
		if(!headers_sent()) {
			if(isset($this->_status_codes[(string)$status])) {
				$status_text = $this->_status_codes[(string)$status]; 
			}
			else {
				$status_text = $this->_status_codes['302'];
				$status = 302;
			}
			header('Status: '.$status.' '.$status_text, false, (integer)$status);
			header('Location: '.$url);
		}
	}
	
	
}