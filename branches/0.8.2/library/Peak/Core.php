<?php
/**
 * Peak Core
 * 
 * @author   Francois Lajoie
 * @version  $Id$ 
 */

define('_VERSION_','0.8.2');
define('_NAME_','PEAK');
define('_DESCR_','Php wEb Application Kernel');

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
     * plugins extensions @experimental
     * @var array
     */
    protected static $_plugins = array();

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
     * @deprecated
     * Init Peak_Config object, set into registry and define usefull constants.
     * Called inside boot.php
     * 
     * @final
     */
    final public static function init()   
    {
    	$config = Peak_Registry::set('core_config',new Peak_Config());
    	
    	// Url constants
        if(defined('SVR_URL')) define('ROOT_URL', SVR_URL.'/'.ROOT);           
    }
    
    /**
     * Init application config
     *
     * @param string $file
     */
    final public static function initConfig($file)
    {
    	$filetype = pathinfo($file,PATHINFO_EXTENSION);
    	$env = self::getEnv();
    	
    	//we load configuration object according to the file extension
    	switch($filetype) {
    		case 'ini' : 
    		    include LIBRARY_ABSPATH.'/Peak/Config/Ini.php';
    		    $conf = new Peak_Config_Ini(APPLICATION_ABSPATH.'/'.$file, true);
    		    break;
    	}
    	
    	//check if we got the configuration for current environment mode or at least section 'all'
    	if(!isset($conf->$env)) {
    		if(!isset($conf->all)) {
    			//die('no configurations for '.$env.' mode');
    			die('no general configurations and/or '.$env.' configurations');
    		}
    	}
    	

    	//get config array
    	$loaded_config = $conf->getVars();

    	//try to merge array section if exists
    	if(isset($loaded_config['all']) && isset($loaded_config[$env])) {

    		//"Extend" recursively array $a with array $b values (no deletion in $a, just added and updated values) (from php.net)
    		function array_extend($a, $b) {	foreach($b as $k=>$v) {	if( is_array($v) ) { if( !isset($a[$k]) ) {	$a[$k] = $v; } else { $a[$k] = array_extend($a[$k], $v); }} else { $a[$k] = $v;	}} return $a; }
    		$final_config = array_extend($loaded_config['all'],$loaded_config[$env]);
    	}
    	elseif(isset($loaded_config[$env])) {
    		$final_config = $loaded_config[$env];
    	}
    	else {
    		$final_config = $loaded_config['all'];
    	}

    	$conf->setVars($final_config);
    	
    	//}
    	echo '<pre>';
    	print_r($conf);
    	echo '</pre>';
    	
    	//push merged config object to Peak_Registry
    	Peak_Registry::set('core_config', $conf);
    	
    	if(isset($conf->svr_url)) { 
    		define('SVR_URL', $conf->svr_url);
    		define('PUBLIC_URL', $conf->svr_url.'/'.PUBLIC_ROOT); 
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
    	
    	//echo '<pre>'; 	print_r($config);  	echo '</pre>';
    	
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
     * Get environment in .htaccess and store it to $_env
     * If environment if already stored in $_env, we return it instead
     * 
     * @return string
     */
    public static function getEnv()
    {
    	if(!isset(self::$_env)) {
    		$env = getenv('APPLICATION_ENV');
    		if(!in_array($env,array('development', 'testing', 'staging', 'production'))) {
    			self::$_env = 'production';
    		}
    		else self::$_env = $env;
    	}
    	return self::$_env;	
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
     * Register a plugins object @experimental
     *
     * @param object $object
     */
    public static function registerPlugin($object)
    {
    	self::$_plugins[] = $object;
    }
    
    /**
     * Dispatch event to plugins object(s) @experimental
     *
     * @param string $event
     */
    public static function dispatchPlugin($event)
	{		
		if(!empty(self::$_plugins)) {
			$event = str_ireplace(array('Peak_','::'),array('','_'),$event);

			echo $event;
			foreach(self::$_plugins as $obj) {
				if(!method_exists($obj,$event)) continue;
				$obj->$event();
			}
		}
	}	

    /**
     * Check if controller name exists
     *
     * @param  string $name
     * @return bool
     */
    public function isController($name)
    {
    	return (file_exists(Peak_Core::getPath('controllers').'/'.$name.'.php')) ? true : false; 
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
    	return (file_exists(Peak_Core::getPath('modules').'/'.$name)) ? true : false;
    }

}

/**
 * MISC USEFULL FUNCS
 */
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