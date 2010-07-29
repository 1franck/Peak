<?php

/**
 * Peak_Core_Codegen object extension 
 *
 * @author  Francois Lajoie
 * @version $Id$
 */
class Peak_Core_Codegen
{
	
	//path where code sample file are
	private $sample_path;
		
	
	/**
	 * Setup sample path
	 */
	public function __construct()
	{
		$this->sample_path = dirname(__FILE__).'/Codegen/';
	}
	
	/**
	 * Add data for sample(sample have method) and load simple file and return the content parsed
	 *
	 * @param string $file
	 * @param array  $data
	 */
	public function genFromSample($sample, $data = null)
	{
		$sample_file = $this->sample_path.'/'.$sample.'.php';
		if(file_exists($sample_file)) 
		{
			$method = $sample.'Sample';
			if(method_exists($this, $method)) { 
				$data = $this->$method($data);
			}
			
			$content = include($sample_file);
		}
		else $content = false;
		
		return $content;		
	}
	
	/**
	 * Save generated sample code to a file
	 *
	 * @param string $sample
	 * @param string $filepath
	 * @param array/null $data
	 * @param bool $overwrite
	 * @return bool
	 */
	public function saveSample($sample, $filepath, $data = null, $overwrite = false)
	{
		$result = $this->genFromSample($sample,$data);
		if(!$result) {
			return false;
		}
		elseif(file_exists($filepath)) {
			if($overwrite) { 			
				if(file_put_contents($filepath,$result)) return true;
			}

		}
		else {
			if(file_put_contents($filepath,$result)) return true;
			else return false;
		}
		return false;
	}
    
	/**
	 * Application configs.php
	 *
	 * @param  array $data
	 * @return array
	 */
    public function configsSample($data)
    {        
    	$original_data = array('PROJECT_NAME'     => 'MyApp',
    	                       'PROJECT_DESCR'    => '',
    	                       'DEV_MODE'         => 'false',
    	                       'APP_THEME'        => 'default',
    	                       'APP_DEFAULT_CTRL' => 'indexController',
    	                       'SVR_URL'          => 'http://127.0.0.1',
    	                       'ROOT'             => '',
    	                       'LIBRARY_ROOT'     => '',
    	                       'APPLICATION_ROOT' => '',
    	                       'ZEND_LIB_ROOT'    => '',
    	                       'APP_LOGIN_NAME'   => '',
    	                       'APP_LOGIN_PASS'   => '',
    	                       'ENABLE_PEAK_CONTROLLERS' => 'false');
    	                       
        $data = array_merge((array)$original_data, (array)$data);
        
        return $data;

    }
    
    /**
     * Application controller
     *
     * @param  array $data
     * @return array
     */
    public function controllerSample($data)
    {
    	$original_data = array('ctrl_name' => 'index');
    	$data = array_merge((array)$original_data, (array)$data);        
        return $data;
    }
    
    /**
     * Application public index
     *
     * @param  array $data
     * @return array
     */
    public function indexSample($data)
    {
    	$original_data = array('');
    	$data = array_merge((array)$original_data, (array)$data);
    	return $data;
    }
    
    
    
}