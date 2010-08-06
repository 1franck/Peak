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
	 */
	public function init($module)
	{
		$config = Peak_Registry::obj()->core_config;
    	
    	$module_path = $config->modules.'/'.$module;
    	
    	if(is_dir($module_path)) {
    		$config->module_name = $module;
    		
    		// current app paths
    		$config->application         = $module_path;
    		$config->cache               = $module_path.'/cache';
    		$config->controllers         = $module_path.'/controllers';
    		$config->controllers_helpers = $config->controllers .'/helpers';
    		$config->modules             = $module_path.'/modules';
    		$config->lang                = $module_path.'/lang';

    		$config->views          = $module_path.'/views';
    		$config->views_ini      = $config->views.'/ini';
    		$config->views_helpers  = $config->views.'/helpers';
    		$config->views_themes   = $config->views.'/themes';

    		$config->theme          = $config->views_themes.'/'.APP_THEME;
    		$config->theme_scripts  = $config->theme.'/scripts';
    		$config->theme_partials = $config->theme.'/partials';
    		$config->theme_layouts  = $config->theme.'/layouts';
    		$config->theme_cache    = $config->theme.'/cache';
    		
    		//echo '<pre>';
    		//print_r($config);
    	}
	}
	
	/**
     * list old modules @deprecated
     *
     * @return array
     */
    public function get()
    {
        
        //list modules directory with their additionnal info if exists
        try
        {

            $it = new DirectoryIterator(Peak_Core::getPath('modules'));

            while($it->valid())
            {
                if(($it->isDir()) && (!in_array($it->getFilename(),array('.','..'))))
                {
                    $mod = $it->getFilename();
                    $plugin_file = Peak_Core::getPath('modules').'/'.$mod.'/'.$mod.'.php';
                    
                    if(file_exists($plugin_file))
                    {
                        $this->modules[$mod] = array('name' => $mod);
                        
                        $plugin_js = Peak_Core::getPath('modules').'/'.$mod.'/'.$mod.'.js';
                        if(file_exists($plugin_js)) $info['js'] = $mod;                     
                                            
                        $plugin_info = Peak_Core::getPath('modules').'/'.$mod.'/'.$mod.'.ini';
                        if(file_exists($plugin_info))
                        {
                            $info = parse_ini_file($plugin_info); 
                            
                            if(!isset($info['title'])) $info['title'] = $mod;
                            
                            if(!isset($info['login'])) $info['login'] = true;
                            
                            if((isset($info['devmode_only'])) && ($info['devmode_only']) && (!DEV_MODE)) {
                                unset($this->modules[$mod]);
                            }                          
                            elseif((isset($info['hidden'])) && ($info['hidden'])) {
                                unset($this->modules[$mod]);
                            }
                            else $this->modules[$mod] = array_merge($this->modules[$mod],$info);
                        }
                        else {
                            $this->modules[$mod]['login'] = true;
                            $this->modules[$mod]['title'] = $mod;
                        }
                         
                    }
                    
                }
              
                $it->next();
            }
            
            //echo '<pre>'; print_r($this->modules);
            return $this->modules;
        }
        catch(Exception $e) { $this->w_errors[] = $e->getMessage(); return false; }

    }
    
    /**
     * Get module infos @deprecated
     *
     * @param string $name
     * @param string $opt
     * @return string/array
     */
    public function getModule($name,$opt = null)
    {
        if($this->isModule($name)) {
            if(!isset($opt)) return $this->modules[$name];
            return (isset($this->modules[$name][$opt])) ? $this->modules[$name][$opt] : null;
        }
        else return null;
    }
	
}