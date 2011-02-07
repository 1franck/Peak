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
		$config = Peak_Registry::o()->config;
	
    	$module_path = (isset($path)) ? $path : $config->modules_path.'/'.$module;
    	
    	if(is_dir($module_path)) {
    	        		
    		//backup previous application configs before overloading core configurations
    		Peak_Registry::set('app_core_config', clone $config);
    		
    		$config->module_name = $module;
    		
    		//get default path structure for module path application
    		$config->path = Peak_Core::getDefaultAppPaths($module_path);
    		
    		//*trivial* need to be better than this
    		//first attempt to add ini config for a modules
    	    /*if(file_exists(Peak_Core::getPath('application').'/module.ini')) {
    	        Peak_Core::initConfig('module.ini', $path);
    	        Peak_Registry::o()->view->_registryConfig();
    	    }*/
    	}
    	
    	
	}
		
}