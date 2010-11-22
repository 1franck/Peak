<?php

/**
 * Peak_Core_Extension_Modules
 *
 * @desr    Prepare core to a module application
 * 
 * @author  Francois Lajoie
 * @version $Id$
 */
class Peak_Core_Extension_Modules
{
	
	/**
	 * Overdrive core application paths configs to a module application paths.
	 *
	 * @param string $module  folder name of the module to load
	 * @param string 
	 */
	public function init($module, $path = null)
	{
		$config = Peak_Registry::o()->core_config;
	
    	$module_path = (isset($path)) ? $path : $config->modules_path.'/'.$module;
    	
    	if(is_dir($module_path)) {
    		$config->module_name = $module;
    		
    		// current app paths
    		$config->application_path         = $module_path;
    		$config->cache_path               = $module_path.'/cache';
    		$config->controllers_path         = $module_path.'/controllers';
    		$config->controllers_helpers_path = $config->controllers_path .'/helpers';
    		$config->modules_path             = $module_path.'/modules';
    		$config->lang_path                = $module_path.'/lang';

    		$config->views_path          = $module_path.'/views';
    		$config->views_ini_path      = $config->views_path.'/ini';
    		$config->views_helpers_path  = $config->views_path.'/helpers';
    		
    		//no theme folder by default for modules. theme reside inside views folder
    		//use view->theme()->setFolder('themename') to themes folder
    		$config->views_themes_path   = $config->views_path;   		
    		$config->theme_path          = $config->views_themes_path;
    		$config->theme_scripts_path  = $config->theme_path.'/scripts';
    		$config->theme_partials_path = $config->theme_path.'/partials';
    		$config->theme_layouts_path  = $config->theme_path.'/layouts';
    		$config->theme_cache_path    = $config->theme_path.'/cache';
    		
    		//echo '<pre>';
    		//print_r($config);
    	}
	}
		
}