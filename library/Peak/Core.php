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
    
    protected static $core_ext = array();
   
    protected static $paths = array();                            
    
    /* Modules and Controller */
    protected $modules = array();
    protected $controllers = array();
   
    
    public $w_errors = array();
    
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
        if((defined('DEV_MODE')) && (DEV_MODE === true)) error_reporting(E_ALL | E_STRICT);

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
        self::$paths['application'] = $app_path;
        self::$paths['controllers'] = $app_path.'/controllers';
        self::$paths['modules']     = $app_path.'/modules';
        self::$paths['lang']        = $app_path.'/lang';
        self::$paths['cache']       = $app_path.'/cache';
        
        self::$paths['views']        = $app_path.'/views';       
        self::$paths['views_ini']     = self::$paths['views'].'/ini';
        self::$paths['views_helpers'] = self::$paths['views'].'/helpers';
        self::$paths['views_themes']  = self::$paths['views'].'/themes';              
        
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
        foreach(self::$paths as $pathname => $val)
        {
            if(($pathname === 'application') || ($pathname === 'library')) continue;
            define(strtoupper($pathname).'_ROOT', self::getPath($pathname,false));
            define(strtoupper($pathname).'_ABSPATH',$val);
            //define(strtoupper($pathname).'_URL', SVR_URL.self::getPath($pathname,false));
        }
        
        // Url constants
        define('ROOT_URL', SVR_URL.'/'.ROOT);      
        if(file_exists(ROOT_ABSPATH.'/'.basename(self::$paths['views_themes']).'/'.APP_THEME)) {
            define('THEME_URL', ROOT_URL.'/themes/'.basename(THEME_ROOT));
            define('THEME_PUBLIC_ABSPATH', ROOT_ABSPATH.'/themes/'.basename(THEME_ROOT));
        }
        
    }
    
    /**
     * Get application different paths
     *
     * @param string $path
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
     * Check if controller name exists
     *
     * @param string $name
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
     * Parse constants of file configs.php
     *
     * @return array
     */
    public function getConfigs($file, $nologin = false)
    {
        $lines = file($file);
        $configs_vars = array();
        foreach($lines as $line_num => $line)
        {
            if(preg_match('.^(define\().',ltrim($line)))
            {
                $temp = explode(';',$line);
                $temp[0] = str_replace(array('define(',')'),'',$temp[0]);
                $define = explode(',',$temp[0]);
                
                $param = str_replace(array('"','\''),'',$define[0]);
                $value = $define[1];
                
                $info = (isset($temp[1])) ? str_replace(array('#( )','#(!)','#(X)'),'',$temp[1]) : '?';                
                
                $configs_vars[$line_num] = array('original_line' => trim($line),
                                                 'param' => $param,
                                                 'value' => $value,
                                                 'line_num'  => $line_num,
                                                 'eval'  => constant($param),
                                                 'info'  => trim($info));
                                                 
                if(($nologin) && (($param === 'W_LOGIN') || ($param === 'W_PASS') )) {
                    unset($configs_vars[$line_num]);

                }
                                                 
            }
        }
        return $configs_vars;
    }
    
    
    /**
     * Get valid language folders available @test
     * 
     * @example /lang/en/main.php is valid
     *
     * @return array
     */
    public function getLang()
    {               
        $core_lang = new Peak_Core_Lang();
        return $core_lang->getLang();
    }
    
    /**
     * Check different config
     *
     * @return array
     */
    public function checkConfigs()
    {
        $warnings = array();
        
        /* check DEV_MODE */
        if((defined('DEV_MODE')) && (DEV_MODE === true)) { 
            $warnings[] = 'DEV_MODE is enabled!';
        }
        
        //@deprecated
        /* check W_LOGIN and W_PASS */
        /*
        if(!defined('W_LOGIN')) $warnings[] = 'W_LOGIN config doesn\'t exists!';
        elseif(W_LOGIN === '') $warnings[] = 'W_LOGIN config found but empty';
        else {
            if(!defined('W_PASS')) $warnings[] = 'W_PASS config doesn\'t exists!';
            elseif(W_PASS === '') $warnings[] = 'W_PASS config found but empty';
        }
        
        if(empty($warnings)) $warnings = null;
        */
        return $warnings;
    }
        
    
}