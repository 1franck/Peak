<?php

/**
 * Manage Application Views Themes
 * 
 * @author   Francois Lajoie
 * @version  $Id$
 */
class Peak_View_Theme
{
    /**
     * theme.ini options variables
     *
     * @var array
     */
    private $_options = array();
    private $_theme_folder = 'default';
      
    
    public function __construct()
    {
        $this->setOptions();
    }
    
    /**
     * Change the default app view theme folder
     * Folder must exists, otherwise $_theme_folder won't change
     *
     * @param string $name
     */
    public function folder($name)
    {
    	$config = Peak_Registry::o()->core_config;

    	if(is_dir($config->views_themes.'/'.$name))
    	{
    		$config->theme          = $config->views_themes.'/'.$name;
    		$config->theme_scripts  = $config->theme.'/scripts';
    		$config->theme_partials = $config->theme.'/partials';
    		$config->theme_layouts  = $config->theme.'/layouts';
    		$config->theme_cache    = $config->theme.'/cache';
    		
    		$this->setOptions();
    		$this->_theme_folder = $name;
    	}
    }
    
    /**
     * Get template options array
     *
     * @return array
     */
    public function getOptions($key = null)
    {
        if(isset($key)) {
            if(isset($this->_options[$key])) return $this->_options[$key];
            else return null;
        }
        return $this->_options;
    }
    
    /**
     * Get theme.ini variables if file exists
     * result go to $this->options
     */
    public function setOptions()
    {
        $filepath = Peak_Core::getPath('theme').'/theme.ini';
        if(file_exists($filepath)) {
            $this->_options = parse_ini_file($filepath,true);
        }    
    }
    
    /**
     * Include themes functions.php if exists 
     *
     * @return included file or false
     */
    public function getFunctionsFile()
    {
        $file = Peak_Core::getPath('theme').'/functions.php';
        return (file_exists($file)) ? $file : false;
    }

}