<?php

/**
 * Peak Core
 * 
 * @author   Francois Lajoie
 * @version  $Id$ 
 */

define('_VERSION_','0.8.0');
define('_NAME_','PEAK');
define('_DESCR_','Php wEb Application Kernel');

//handle all uncaught exceptions (try/catch block missing)
set_exception_handler('pkexception');

class Peak_Core
{
      
    /**
     * core extentensions object
     * @var object
     */
    protected $_extensions;

    /**
     * object itself
     * @var object
     */
    private static $_instance = null; 
        
    /**
     * Singleton peak core
     *
     * @return  object instance
     */
    public static function getInstance()
	{
		if (is_null(self::$_instance)) self::$_instance = new self();
		return self::$_instance;
	}
    
	/**
	 * Activate error_reporting on DEV_MODE
	 */
    private function __construct()
    {            	
        // check DEV_MODE
        if((defined('DEV_MODE')) && (DEV_MODE === true)) {
            ini_set('error_reporting', (version_compare(PHP_VERSION, '5.3.0', '<') ? E_ALL|E_STRICT : E_ALL));
        }
    }
    
    /**
     * Try to return a extension object based the method name.
     *
     * @param  string $helper
     * @param  null   $args not used
     * @return object
     */
    public function __call($extension, $args = null)
    {
    	if((isset($this->ext()->$extension)) || ($this->ext()->exists($extension))) {
        	return $this->ext()->$extension;
        }
        elseif((defined('DEV_MODE')) && (DEV_MODE)) {
            trigger_error('DEV_MODE: Core/Extension method '.$extension.'() doesn\'t exists');
        }
    }
    
    /**
     * Load/return Core extension objects
     */
    public function ext()
    {        
        if(!is_object($this->_extensions)) $this->_extensions = new Peak_Core_Extensions();
    	return $this->_extensions;
    }
    
    /**
     * Init Peak_Config object, set into registry and define usefull constants.
     * Called inside boot.php
     * 
     * @final
     */
    final public static function init()   
    {
    	$config = Peak_Registry::set('core_config',new Peak_Config());
    	
    	// Url constants
        if(defined('SVR_URL')) {
        	define('ROOT_URL', SVR_URL.'/'.ROOT);
        	/*if(file_exists(ROOT_ABSPATH.'/themes/'.APP_THEME)) {
        		define('THEME_URL', ROOT_URL.'/themes/'.APP_THEME);
        		define('THEME_PUBLIC_ABSPATH', ROOT_ABSPATH.'/themes/'.APP_THEME);
        	}*/
        }              
    }
    
    /**
     * Prepare paths and store it inside Peak_Config.
     * Called inside boot.php
     *
     * @param string $app_path
     * @param string $lib_path
     */
    public static function initApp($app_path, $lib_path)
    {
    	$config = Peak_Registry::o()->core_config;
    	
    	// current libray paths
        $config->library_path     = $lib_path;       
        $config->libs_path        = $lib_path.'/Peak/libs';  
    	   	
    	// current app paths
        $config->application_path         = $app_path;
        $config->cache_path               = $app_path.'/cache';
        $config->controllers_path         = $app_path.'/controllers';
        $config->controllers_helpers_path = $config->controllers_path .'/helpers';
        $config->models_path              = $app_path.'/models';       
        $config->modules_path             = $app_path.'/modules';
        $config->lang_path                = $app_path.'/lang';
        
        $config->views_path          = $app_path.'/views';       
        $config->views_ini_path      = $config->views_path.'/ini';
        $config->views_helpers_path  = $config->views_path.'/helpers';
                    
        
        if(defined('APP_THEME')) {
            $config->views_themes_path   = $config->views_path.'/themes';  
        	$config->theme_path          = $config->views_themes_path.'/'.APP_THEME;
        }
        else {
        	$config->views_themes_path   = $config->views_path;  
        	$config->theme_path          = $config->views_themes_path;
        }
        
        $config->theme_scripts_path  = $config->theme_path.'/scripts';
        $config->theme_partials_path = $config->theme_path.'/partials';
        $config->theme_layouts_path  = $config->theme_path.'/layouts';
        $config->theme_cache_path    = $config->theme_path.'/cache';
    }
       
       
    /**
     * Get application different vars from Peak_Configs ending by '_path'
     *
     * @param   string $path
     * @return  string
     * 
     * @example getPath('application') = Peak_Registry::o()->core_config->application_path
     */
    public static function getPath($path = 'application', $absolute_path = true) 
    {
        $pathvar = $path.'_path';
        if(isset(Peak_Registry::o()->core_config->$pathvar)) {
            if($absolute_path) return Peak_Registry::o()->core_config->$pathvar;
            else return str_replace(SVR_ABSPATH,'', Peak_Registry::o()->core_config->$pathvar);
        }
        else return null;
    }
    
    /**
     * Get/Set core configurations
     *
     * @param  string $k configuration keyname
     * @param  misc   $v configuration keyname value
     * @return misc   return null if no config found or setting a new config value
     */
    public static function config($k,$v = null)
    {
    	$c = Peak_Registry::o()->core_config;
    	if(isset($v)) $c->$k = $v;
    	elseif(isset($c->$k)) return $c->$k;
    	else return null;
    }
       
       
    /**
     * Check if controller name exists
     *
     * @param  string $name
     * @return bool
     */
    public function isController($name)
    {
    	if(empty($this->controllers)) {
    		return (file_exists(Peak_Core::getPath('controllers').'/'.$name.'.php')) ? true : false;
    	}
        return (in_array($name,$this->controllers)) ? true : false;        
    }
    
    /**
     * Check if internal Peak Controller exists
     *
     * @param  string $name
     * @return bool
     */
    public function isInternalController($name)
    {
    	return (file_exists(LIBRARY_ABSPATH.'/Peak/Controller/Internal/'.$name.'.php')) ? true : false;
    }
    
    /**
     * Check if modules name exists
     *
     * @param  string $name
     * @return bool
     */
    public function isModule($name)
    {
    	if(empty($this->modules)) {
    		return (file_exists(Peak_Core::getPath('modules').'/'.$name)) ? true : false;
    	}
        return (array_key_exists($name, $this->modules)) ? true : false;
    }
               
    
    /**
     * Get controllers @deprecated
     * 
     * @return array()
     */
    public function getControllers()
    {      
        //list controllers directory
        try {
            $it = new DirectoryIterator(self::getPath('controllers'));

            while($it->valid()) {
                if((!$it->isDir()) && (!in_array($it->getFilename(),array('.','..'))) && (pathinfo($it->getFilename(),PATHINFO_EXTENSION) === 'php'))
                {
                    $ctrl = str_replace('.'.pathinfo($it->getFilename(),PATHINFO_EXTENSION),'',$it->getFilename());
                    $this->controllers[] = $ctrl;                   
                }
                $it->next();
            }
        }
        catch(Exception $e) { return $e->getMessage(); }
 
        //no controllers
        //if(empty($this->controllers)) throw new wyn_exception('ERR_CORE_NO_CTRL_FOUND');            
        return $this->controllers;        
    }        
}


function _clean($str) {
    $str = stripslashes($str); $str = strip_tags($str); $str = trim($str); $str = htmlspecialchars($str,ENT_NOQUOTES); $str = htmlentities($str);
    return $str;
}
function _cleans($strs,$keys_to_clean = null, $remove_keys = null) {
	if(is_array($remove_keys)) { foreach($remove_keys as $k) { unset($strs[$key]); } }
    if(is_array($keys_to_clean)) {  foreach($keys_to_clean as $k => $v) { if(isset($strs[$v])) $strs[$v] = _clean($strs[$v]); } }
    else { foreach($strs as $k => $v) $strs[$k] = _clean($v); }
    return $strs;
}
function pkexception($e) {
	die('<b>Uncaught Exception</b>: '. $e->getMessage());
}