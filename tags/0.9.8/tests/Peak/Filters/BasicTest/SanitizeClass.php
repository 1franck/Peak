<?php

class SanitizeClass extends Peak_Filters_Basic 
{
	public function setSanitization()
	{
		return array(
		  
		  'number' => FILTER_SANITIZE_NUMBER_INT,
		  'cap'    => array('filter'  => FILTER_CALLBACK,
		                    'options' => array($this, 'sanitizeCap'))
		
		);
	}
	
	public function sanitizeCap($value)
	{
		return strtoupper($value);
	}
}