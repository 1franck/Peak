<?php

/**
 * Peak Core
 * 
 * @author   Francois Lajoie
 * @version  $Id$ 
 */

define('_VERSION_','0.7.98');
define('_NAME_','PEAK');
define('_DESCR_','Php wEb Application Kernel');

class Peak_Core
{
   
    
    // Controllers
    protected $_controllers = array();
   
    // core extentension objects
    protected $_extensions;

    //object itself
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
	 * Activative error_reporting on DEV_MODE
	 */
    private function __construct()
    {            	
        // check DEV_MODE
        if((defined('DEV_MODE')) && (DEV_MODE === true)) {
            ini_set('error_reporting', (version_compare(PHP_VERSION, '5.3.0', '<') ? E_ALL|E_STRICT : E_ALL));
        }
    }
    
    /**
     * Try to return a helper object based the method name.
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
     * @final Init Peak_Config object, set into registry and define usefull constants
     * Called inside boot.php
     * 
     */
    final public static function init()   
    {
    	$config = Peak_Registry::set('core_config',new Peak_Config());
    	
    	// Url constants
        if(defined('SVR_URL')) {
        	define('ROOT_URL', SVR_URL.'/'.ROOT);
        	if(file_exists(ROOT_ABSPATH.'/themes/'.APP_THEME)) {
        		define('THEME_URL', ROOT_URL.'/themes/'.APP_THEME);
        		define('THEME_PUBLIC_ABSPATH', ROOT_ABSPATH.'/themes/'.APP_THEME);
        	}
        }              
    }
    
    /**
     * Prepare paths and store it inside Peak_Config
     * Called inside boot.php
     *
     * @param string $app_path
     * @param string $lib_path
     */
    public static function initApp($app_path, $lib_path)
    {
    	$config = Peak_Registry::obj()->core_config;
    	   	
    	// current app paths
        $config->application         = $app_path;
        $config->cache               = $app_path.'/cache';
        $config->controllers         = $app_path.'/controllers';
        $config->controllers_helpers = $config->controllers .'/helpers';       
        $config->modules             = $app_path.'/modules';
        $config->lang                = $app_path.'/lang';
        
        $config->views          = $app_path.'/views';       
        $config->views_ini      = $config->views.'/ini';
        $config->views_helpers  = $config->views.'/helpers';
        $config->views_themes   = $config->views.'/themes';              
        
        $config->theme          = $config->views_themes.'/'.APP_THEME;
        $config->theme_scripts  = $config->theme.'/scripts';
        $config->theme_partials = $config->theme.'/partials';
        $config->theme_layouts  = $config->theme.'/layouts';
        $config->theme_cache    = $config->theme.'/cache';
        
        /* current libray paths */
        $config->library     = $lib_path;       
        $config->libs        = $lib_path.'/Peak/libs';  
    }
       
       
    /**
     * Get application different paths from Peak_Configs
     *
     * @param  string $path
     * @return string
     */
    public static function getPath($path = 'application', $absolute_path = true) 
    {
        //$pathvar = $path;
        if(isset(Peak_Registry::obj()->core_config->$path)) {
            if($absolute_path) return Peak_Registry::obj()->core_config->$path;
            else return str_replace(SVR_ABSPATH,'', Peak_Registry::obj()->core_config->$path);
        }
        else return null;
    }
    
    /**
     * Get Core configs
     *
     * @param unknown_type $k
     * @param unknown_type $v
     * @return unknown
     */
    public static function config($k,$v = null)
    {
    	if(isset($v)) self::$_config->$k = $v;
    	elseif(isset(self::$_config->$k)) return self::$_config->$k;
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
     * @param string $name
     * @return bool
     */
    public function isModule($name)
    {
    	if(empty($this->modules)) {
    		return (file_exists(Peak_Core::getPath('modules').'/'.$name.'/'.$name.'.php')) ? true : false;
    	}
        return (array_key_exists($name, $this->modules)) ? true : false;
    }
               
    
    /**
     * Get wyn controllers - wsystem/controllers
     * 
     * return array()
     *
     */
    public function getControllers()
    {
        $cached = $this->getCachedControllers();
        
        //use session cache if $cached !== false;
        
        if($cached) {            
            $this->controllers = $cached;
            return $cached; 
        }
        
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
        catch(Exception $e) { $this->w_errors[] = $e->getMessage(); return false; }
 
        //no controllers
        if(empty($this->controllers)) throw new wyn_exception('ERR_CORE_NO_CTRL_FOUND');
            
        $this->cacheControllers();
            
        return $this->controllers;        
    }
    
    /**
     * Cache controllers list into session
     */
    public function cacheControllers()
    {
        $_SESSION['Peak_Controllers'] = $this->controllers;
    }
    
    /**
     * Get controllers session cache or false
     *
     * @return array/bool
     */
    public function getCachedControllers()
    {
        if(isset($_SESSION['Peak_Controllers'])) return $_SESSION['Peak_Controllers'];
        else return false;
    }
            
    
}