<?php

/**
 * Peak Core
 * 
 * @author   Francois Lajoie
 * @version  $Id$ 
 */

define('_VERSION_','0.7.8');
define('_NAME_','PEAK');
define('_DESCR_','Php wEb Application Kernel');

class Peak_Core
{
    //core object extension
    protected static $core_ext = array();
   
    //application and library paths
    protected static $paths = array();                            
    
    // Modules and Controllers
    protected $modules = array();
    protected $controllers = array();
   
    // core extentension objects
    protected $extensions = array();
    
    public $w_errors = array(); //@deprecated
    
    private static $_instance = null; //object itself
        
    /**
     * Singleton wyn core
     *
     * @return  object instance
     */
    public static function getInstance()
	{
		if (is_null(self::$_instance)) self::$_instance = new self();
		return self::$_instance;
	}
    
    private function __construct()
    {        
        /* check DEV_MODE */
        if((defined('DEV_MODE')) && (DEV_MODE === true)) {
            ini_set('error_reporting', (version_compare(PHP_VERSION, '5.3.0', '<') ? E_ALL|E_STRICT : E_ALL));
            //error_reporting(E_ALL | E_STRICT);
        }

        $this->getControllers();
        $this->getModules();       
    }
    
    /**
     * Set application and system paths
     *
     * @param string $path
     */
    final public static function setPath($app_path, $lib_path)
    {

        /* current app paths */
        self::$paths['application']         = $app_path;
        self::$paths['controllers']         = $app_path.'/controllers';
        self::$paths['controllers_helpers'] = self::$paths['controllers'].'/helpers';
        self::$paths['modules']             = $app_path.'/modules';
        self::$paths['lang']                = $app_path.'/lang';
        self::$paths['cache']               = $app_path.'/cache';
        
        self::$paths['views']          = $app_path.'/views';       
        self::$paths['views_ini']      = self::$paths['views'].'/ini';
        self::$paths['views_helpers']  = self::$paths['views'].'/helpers';
        self::$paths['views_themes']   = self::$paths['views'].'/themes';              
        
        self::$paths['theme']          = self::$paths['views_themes'].'/'.APP_THEME;
        self::$paths['theme_scripts']  = self::$paths['theme'].'/scripts';
        self::$paths['theme_partials'] = self::$paths['theme'].'/partials';
        self::$paths['theme_layouts']  = self::$paths['theme'].'/layouts';
        self::$paths['theme_cache']    = self::$paths['theme'].'/cache';

        
        /* current libray paths */
        self::$paths['library']     = $lib_path;       
        self::$paths['libs']        = $lib_path.'/Peak/libs';
        self::$paths['viewhelpers'] = $lib_path.'/Peak/view/helpers';  //@depreacted
        
        // Generate dynamicly constants from application and peak library path
        if(defined('SVR_ABSPATH')) {
        	foreach(self::$paths as $pathname => $val)
        	{
        		if(($pathname === 'application') || ($pathname === 'library')) continue;
        		define(strtoupper($pathname).'_ROOT', self::getPath($pathname,false));
        		define(strtoupper($pathname).'_ABSPATH',$val);
        		//define(strtoupper($pathname).'_URL', SVR_URL.self::getPath($pathname,false));
        	}
        }
        
        // Url constants
        if(defined('SVR_URL')) {
        	define('ROOT_URL', SVR_URL.'/'.ROOT);
        	if(file_exists(ROOT_ABSPATH.'/'.basename(self::$paths['views_themes']).'/'.APP_THEME)) {
        		define('THEME_URL', ROOT_URL.'/themes/'.basename(THEME_ROOT));
        		define('THEME_PUBLIC_ABSPATH', ROOT_ABSPATH.'/themes/'.basename(THEME_ROOT));
        	}
        }
        
    }
    
    /**
     * Get application different paths
     *
     * @param  string $path
     * @return string
     */
    public static function getPath($path = 'application', $absolute_path = true) 
    {
        //$pathvar = $path;
        if(isset(self::$paths[$path])) {
            if($absolute_path) return self::$paths[$path];
            else return str_replace(SVR_ABSPATH,'', self::$paths[$path]);
        }
        else return false;
    }
    
    /**
     * Return paths array
     *
     * @return array
     */
    public static function getPaths()
    {
    	return self::$paths;
    }
    
    
    /**
     * Check if controller name exists
     *
     * @param  string $name
     * @return bool
     */
    public function isController($name)
    {
        return (in_array($name,$this->controllers)) ? true : false;
    }
    
    /**
     * Check if modules name exists
     *
     * @param string $name
     * @return bool
     */
    public function isModule($name)
    {
        return (array_key_exists($name, $this->modules)) ? true : false;
    }
    
    /**
     * Get module infos
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
       
    /**
     * Load wyn modules
     *
     * @return array
     */
    public function getModules()
    {
        $cached = $this->getCachedModules();
        
        //use session cache if $cached !== false;
        if($cached) { 
            $this->modules = $cached;
            return $cached; 
        }
        
        //list modules directory with their additionnal info if exists
        try
        {

            $it = new DirectoryIterator(self::getPath('modules'));

            while($it->valid())
            {
                if(($it->isDir()) && (!in_array($it->getFilename(),array('.','..'))))
                {
                    $mod = $it->getFilename();
                    $plugin_file = self::getPath('modules').'/'.$mod.'/'.$mod.'.php';
                    
                    if(file_exists($plugin_file))
                    {
                        $this->modules[$mod] = array('name' => $mod);
                        
                        $plugin_js = self::getPath('modules').'/'.$mod.'/'.$mod.'.js';
                        if(file_exists($plugin_js)) $info['js'] = $mod;                     
                                            
                        $plugin_info = self::getPath('modules').'/'.$mod.'/'.$mod.'.ini';
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
            
            $this->cacheModules();
            
            //echo '<pre>'; print_r($this->modules);
            return $this->modules;
        }
        catch(Exception $e) { $this->w_errors[] = $e->getMessage(); return false; }

    }
        
    /**
     * Cache modules list into session
     */
    public function cacheModules()
    {
        $_SESSION['Peak_Modules'] = $this->modules;
    }
    
    /**
     * Get modules session cache or false
     *
     * @return array/bool
     */
    public function getCachedModules()
    {
        if(isset($_SESSION['Peak_Modules'])) return $_SESSION['Peak_Modules'];
        else return false;
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
    
    /**
     * Load/return Core extension objects
     * 
     * @param string $name core extension name
     */
    public function ext($name)
    {
        $ext_name_prefix = 'Peak_Core_';

        $ext_name = trim(stripslashes(strip_tags($name)));
        $ext_file = self::$paths['library'].'/Peak/Core/'.$name.'.php';

        $ext_class_name = $ext_name_prefix.$ext_name;

        if(!isset($this->extensions[$ext_name])) {
            if(file_exists($ext_file)) {
                include($ext_file);
                $this->extensions[$ext_name] = new $ext_class_name();
            }
            else throw new Peak_Exception('ERR_CORE_EXTENSION_NOT_FOUND');
        }

        return $this->extensions[$ext_name];
    }        
    
}