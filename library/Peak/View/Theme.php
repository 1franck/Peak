<?php

/**
 * Manage Application Views Themes
 * 
 * @desc     Theme /layout, /partials, /scripts, /cache, /theme.ini
 *           By default they are in your application /views folder.
 *           If you set theme name with method folder(), application /views/themes/[name] will be used
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
    private $_theme_folder = null;
      
    
    public function __construct()
    {
        $this->setOptions();
    }
    
    /**
     * Use views themes folder ( views/themes/[$name]/ ).
     * If $name is null, application /views/ folder will be used as views themes folder
     *
     * @param string/null $name
     */
    public function folder($name)
    {
    	$config = Peak_Registry::o()->core_config;
    	
    	if(is_null($name))
    	{
    		$config->views_themes_path   = $config->views_path;
    		$config->theme_path          = $config->views_themes_path;
    		$config->theme_scripts_path  = $config->theme_path.'/scripts';
    		$config->theme_partials_path = $config->theme_path.'/partials';
    		$config->theme_layouts_path  = $config->theme_path.'/layouts';
    		$config->theme_cache_path    = $config->theme_path.'/cache';

    	}
    	elseif(!file_exists($config->views_path.'/themes/'.$name)) { 
    		throw new Peak_Exception('ERR_VIEW_THEME_NOT_FOUND', $name);
    	}
    	else {

    		$config->views_themes_path   = $config->views_path.'/themes';
    		$config->theme_path          = $config->views_themes_path.'/'.$name;

    		$config->theme_scripts_path  = $config->theme_path.'/scripts';
    		$config->theme_partials_path = $config->theme_path.'/partials';
    		$config->theme_layouts_path  = $config->theme_path.'/layouts';
    		$config->theme_cache_path    = $config->theme_path.'/cache';
    	}

    	$this->setOptions();
    	$this->_theme_folder = $name;
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
    


}