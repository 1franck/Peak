<?php
/**
 * Peak Core
 * 
 * @author   Francois Lajoie
 * @version  $Id$ 
 */

define('PK_VERSION', '0.8.6');
define('PK_NAME'   , 'PEAK');
define('PK_DESCR'  , 'Php wEb Application Kernel');

//handle all uncaught exceptions (try/catch block missing)
set_exception_handler('pkexception');

class Peak_Core
{

    /**
     * core extensions object
     * @var object
     */
    protected $_extensions;
    
    /**
     * Current Environment
     * @final
     * @var string
     */
    private static $_env;
    
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
	 * Activate error_reporting based on app env
	 */
    private function __construct()
    {
        if(self::getEnv() === 'development') {
        	//ini_set('error_reporting', (version_compare(PHP_VERSION, '5.3.0', '<') ? E_ALL|E_STRICT : E_ALL|E_DEPRECATED));
        	//faster...?
        	ini_set('error_reporting', (!function_exists('class_alias')) ? E_ALL|E_STRICT : E_ALL|E_DEPRECATED);
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
        elseif(self::getEnv() === 'development') {
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
     * Init application config
     *
     * @param string $file
     */
    public static function initConfig($file, $apppath)
    {    		
    	self::getInstance();
    	
    	$filetype = pathinfo($file, PATHINFO_EXTENSION);
    	$env = self::getEnv();
    	
    	//load configuration object according to the file extension
    	switch($filetype) {
    		
    		case 'ini' : 
    		    $conf = new Peak_Config_Ini($apppath.'/'.$file, true);
    		    break;
    		    
    		case 'php' :
    			$array = include($apppath.'/'.$file);
    			$conf = new Peak_Config();
    			$conf->setVars($array);
    			break;
    	}
    	
    	//check if we got the configuration for current environment mode or at least section 'all'
    	if((!isset($conf->$env)) && (!isset($conf->all))) {
    		throw new Peak_Exception('ERR_CUSTOM', 'no general configurations and/or '.$env.' configurations');
    	}

    	//get config array and merge according to the environment
    	$loaded_config = $conf->getVars();
    	
    	//add APPLICATION_ABSPATH to path config array if exists
    	if(isset($loaded_config['all']['path'])) {
    		foreach($loaded_config['all']['path'] as $pathname => $path) {
    			$loaded_config['all']['path'][$pathname] = $apppath.'/'.$path;
    		}
    	}
    	
    	//add APPLICATION_ABSPATH to 'path' key value
    	if(isset($loaded_config[$env]['path'])) {
    		foreach($loaded_config[$env]['path'] as $pathname => $path) {
    			$loaded_config[$env]['path'][$pathname] = $apppath.'/'.$path;
    		}
    	}
    	
    	//merge app config paths with core app paths  	
    	if(isset($loaded_config['all']['path'])) {
    	    $loaded_config['all']['path'] = $conf->arrayMergeRecursive(self::getDefaultAppPaths($apppath), $loaded_config['all']['path']);
    	}
    	else {
    		$loaded_config['all']['path'] = self::getDefaultAppPaths($apppath);
    	}

    	//try to merge array section 'all' with current environment section if exists
    	if(isset($loaded_config['all']) && isset($loaded_config[$env])) {
    		$final_config = $conf->arrayMergeRecursive($loaded_config['all'],$loaded_config[$env]);
    	}
    	elseif(isset($loaded_config[$env])) $final_config = $loaded_config[$env];
    	else $final_config = $loaded_config['all'];

    	//save transformed config
    	$conf->setVars($final_config);
    	Peak_Registry::set('config', $conf);  	   	

    	//set some php ini settings
    	if(isset($conf->php)) {
    		foreach($conf->php as $setting => $val) {
    			if(!is_array($val)) ini_set($setting, $val);
    			else {
    				foreach($val as $k => $v) ini_set($setting.'.'.$k, $v);
    			}    			
    		}
    	}

    }

    /**
     * Generate an array of paths that represent all application subfolders
     *
     * @param string $app_path Current application absolute path
     */
    public static function getDefaultAppPaths($app_path)
    {  	
    	return array('application'         => $app_path,
    	             'cache'               => $app_path.'/cache',
    	             'controllers'         => $app_path.'/controllers',
    	             'controllers_helpers' => $app_path.'/controllers/helpers',
    	             'models'              => $app_path.'/models',
    	             'modules'             => $app_path.'/modules',
    	             'lang'                => $app_path.'/lang',
    	             'views'               => $app_path.'/views',
    	             'views_ini'           => $app_path.'/views/ini',
    	             'views_helpers'       => $app_path.'/views/helpers',
    	             'views_themes'        => $app_path.'/views',
    	             'theme'               => $app_path.'/views',
    	             'theme_scripts'       => $app_path.'/views/scripts',
    	             'theme_partials'      => $app_path.'/views/partials',
                     'theme_layouts'       => $app_path.'/views/layouts',
                     'theme_cache'         => $app_path.'/views/cache');
    }
    
    /**
     * Get environment in .htaccess or from constant APPLICATION_DEV and store it to $_env
     * If environment if already stored in $_env, we return it instead.
     * Define APPLICATION_DEV if not already defined.
     * 
     * @return string
     */
    public static function getEnv()
    {
    	if(!isset(self::$_env)) {
    		if(!defined('APPLICATION_ENV'))	$env = getenv('APPLICATION_ENV');
    		else $env = APPLICATION_ENV;
    		if(!in_array($env,array('development', 'testing', 'staging', 'production'))) {
    			self::$_env = 'production';
    		}
    		else self::$_env = $env;   		
    		if(!defined('APPLICATION_ENV'))	define('APPLICATION_ENV', self::$_env);		
    	}
    	return self::$_env;	
    }

    /**
     * Get application path vars from Peak_Registry::o()->core_config
     *
     * @param   string $path
     * @return  string|null
     * 
     * @example Peak_Core::getPath('application') = Peak_Registry::o()->config->path['application']
     */
    public static function getPath($path = 'application') 
    {
    	$c = Peak_Registry::o()->config;
    	
    	if(isset($c->path[$path])) return $c->path[$path];
    	else return null;
    }
    
    /**
     * Framework booting level
     *
     * @param  integer $level
     * @return null|Peak_Application
     */
    public static function init($level = 1)
    {
        //only peak basic config / includepath
        if($level >= 1) {
            
            //define server document root absolute path
            $svr_path = str_replace('\\','/',realpath($_SERVER['DOCUMENT_ROOT']));
            if(substr($svr_path, -1, 1) !== '/') $svr_path .= '/';
            define('SVR_ABSPATH', $svr_path); unset($svr_path);
            
            //define libray path
            define('LIBRARY_ABSPATH', str_ireplace(array(substr(__FILE__, -14),'\\'), array('','/'), __FILE__));
            
            //add LIBRARY_ABSPATH to include path
            set_include_path(implode(PATH_SEPARATOR, array(LIBRARY_ABSPATH, LIBRARY_ABSPATH.'/Peak/Libs', get_include_path())));
        }
                
        //peak autoloader
        if($level >= 2) {

            //load peak core autoloader
            include LIBRARY_ABSPATH.'/Peak/autoload.php';
        }
        
        //peak basic config with app config
        if($level >= 3) {

            define('PUBLIC_ABSPATH', SVR_ABSPATH . PUBLIC_ROOT);
            define('APPLICATION_ABSPATH', realpath(SVR_ABSPATH . APPLICATION_ROOT));
            if(defined('ZEND_LIB_ROOT')) define('ZEND_LIB_ABSPATH',SVR_ABSPATH.ZEND_LIB_ROOT);
            
            //if ZEND_LIB_ABSPATH is specified, we add it to include path
            if(defined('ZEND_LIB_ABSPATH')) {
                set_include_path(implode(PATH_SEPARATOR, array(get_include_path(), ZEND_LIB_ABSPATH)));
            }
        }
      
        //peak app config init
        if($level >= 4) {
            
            //init app&core configurations
            if(defined('APPLICATION_CONFIG')) {
                Peak_Core::initConfig(APPLICATION_CONFIG, APPLICATION_ABSPATH);
            }
            else throw new Peak_Exception('ERR_CUSTOM', 'No configuration have been specified!');
        }
        
        //peak app object init
        if($level >= 5) {
            
            //include application bootstrap if exists
            if(file_exists(APPLICATION_ABSPATH.'/bootstrap.php')) include APPLICATION_ABSPATH.'/bootstrap.php';

            //include application front extension if exists
            if(file_exists(APPLICATION_ABSPATH.'/front.php')) include APPLICATION_ABSPATH.'/front.php';
            
            return new Peak_Application();
        }
    }

}

/**
 * MISC USEFULL FUNCS
 */
function _clean($str) {
    $str = stripslashes($str); $str = strip_tags($str); $str = trim($str); $str = htmlspecialchars($str,ENT_NOQUOTES); $str = htmlentities($str);
    return $str;
}
function _cleans($strs, $keys_to_clean = null, $remove_keys = null) {
	if(is_array($remove_keys)) { foreach($remove_keys as $k) { unset($strs[$key]); } }
    if(is_array($keys_to_clean)) {  foreach($keys_to_clean as $k => $v) { if(isset($strs[$v])) $strs[$v] = _clean($strs[$v]); } }
    else { foreach($strs as $k => $v) $strs[$k] = _clean($v); }
    return $strs;
}
function pkexception($e) {
	die('<b>Uncaught Exception</b>: '. $e->getMessage());
}