<?php
/**
 * Template variables registry, helpers object, theme object, rendering object
 * 
 * @author   Francois Lajoie
 * @version  $Id$  
 * @uses     Peak_View_Theme, Peak_View_Helpers, Peak_View_Render, Peak_View_Render_*
 */
class Peak_View
{
	/**
	 * view vars
	 * @var array
	 */
    protected $_vars = array();

    /**
     * view helpers object
     * @var object
     */
    private $_helpers;

    /**
     * view theme object
     * @var object
     */
    private $_theme;

    /**
     * view rendering object
     * @var object
     */
    private $_engine;


    /**
     * Load view - set an array|ini file as template variable(s) (optionnal)
     *
     * @param array|string $vars
     */
    public function __construct($vars = null)
    {
        if(isset($vars)) {
            if(is_array($vars)) $this->_vars = $vars;
            else $this->iniVar($vars);
        }
        $this->_registryConfig();
    }   

    /**
     * Set/overwrite view variable
     *
     * @param string $name
     * @param anything $value
     */
    public function __set($name,$value = null)
    {
        $this->_vars[$name] = $value;
    }

    /**
     * Get view variable
     *
     * @param  string $name
     * @return anything
     */
    public function __get($name)
    {        
        return array_key_exists($name,$this->_vars) ? $this->_vars[$name] : null;
    }

    /**
     * Isset $vars keyname
     *
     * @param  string $name
     * @return bool
     */
    public function __isset($name)
    {
    	return array_key_exists($name,$this->_vars) ? true : false;
    }

    /**
     * Unset $vars keyname
     *
     * @param string $name
     */
    public function __unset($name)
    {
    	if(array_key_exists($name,$this->_vars)) unset($this->_vars[$name]);
    }

    /**
     * We try to call View Render Engine object method.
     * If not, we try to return a helper object based on $method name.
     * So every Rendering Engine Method can be called directly inside Peak_View and
     * every instanciated Peak_View_Helpers
     *
     * @param string $method
     * @param array  $args
     */
    public function  __call($method, $args = null)
    {
        if(method_exists($this->engine(),$method)) {
        	return call_user_func_array(array($this->engine(), $method), $args);        
        }
        elseif((isset($this->helper()->$method)) || ($this->helper()->exists($method))) return $this->helper()->$method;
        elseif(defined('APPLICATION_ENV') && in_array(APPLICATION_ENV, array('development', 'testing'))) {
            trigger_error('View method/helper '.$method.'() doesn\'t exists');
        }
    }
    
    /**
     * Get array 'view' from registered object 'config' if exists
     */
    private function _registryConfig()
    {
    	if(isset(Peak_Registry::o()->config->view)) {
    		foreach(Peak_Registry::o()->config->view as $k => $v) { 			
    			if(is_array($v)) {
    				foreach($v as $p1 => $p2) $this->$k($p1,$p2);
    			}
    			else $this->$k($v);
    		}
    	}
    }

    /**
     * Set/overwrite view variable
     * 
     * @see    __set()
     * @return Peak_View
     */
    public function set($name, $value = null)
    {
    	$this->__set($name,$value);
    	return $this;    	
    }

    /**
     * Count template variables
     *
     * @return integer
     */
    public function countVars()
    {
        return count($this->getVars());
    }

    /**
     * Get template variables
     *
     * @return array
     */
    public function getVars()
    {
        return $this->_vars;
    }

    /**
     * Set/Overwrite some view vars
     */
    public function setVars($vars)
    {
        $this->_vars = $vars;
    }

    /**
     * Set/Add some view vars
     * Existing var key name will be overwrited, otherwise var is added to current $_vars 
     */
    public function addVars($vars)
    {
        foreach($vars as $k => $v) {
            $this->set($k,$v);
        }
    }

    /**
     * Clean all variable in $vars
     */
    public function resetVars()
    {
        $this->_vars = array();
    }

    /**
     * Set/Get current view rendering engine object
     *
     * @param  string $engine_name 
     * @return object Peak_View_Render_*
     */
    public function engine($engine_name = null)
    {
        if(isset($engine_name)) {
            $engine_class = 'Peak_View_Render_'.$engine_name;
            $this->_engine = new $engine_class();
        }
        
        return $this->_engine;
    }

    /**
     * Get render engine name
     *
     * @return string
     */
    public function getEngineName()
    {
        if(is_object($this->_engine)) {
            return strtolower(str_replace('Peak_View_Render_', '', get_class($this->_engine)));
        }
        else return null;
    }

    /**
     * Render Controller Action View file with the current rendering engine
     * 
     * @param string $file
     * @param string $path
     * @return string or array   return array of view files when layout is used
     */
    public function render($file,$path)
    {
        if(is_object($this->_engine)) {
            $this->engine()->render($file,$path);
        }
        else throw new Peak_Exception('ERR_VIEW_ENGINE_NOT_SET');
    }

    /**
     * Create/return view object
     *
     * @return object
     */
    public function theme($folder = null)
    {
        if(!($this->_theme instanceof Peak_View_Theme)) $this->_theme = new Peak_View_Theme($folder);
        elseif(isset($folder)) $this->_theme->setFolder($folder); 
        return $this->_theme;
    }

    /**
     * Load helpers objects method and return helper obj
     *
     * @return object Peak_View_Helpers
     */
    public function helper()
    {
    	if(!is_object($this->_helpers)) $this->_helpers = new Peak_View_Helpers();
    	return $this->_helpers;
    }

    /**
     * Load ini file into view vars
     *
     * @param string $file
     * @param string $path leave empty if ini file is under yourapp/views/ini
     */
    public function iniVar($file, $path = null)
    {
    	if(!isset($path)) $filepath = Peak_Core::getPath('views_ini').'/'.$file;
    	else $filepath = $path.'/'.$file;

    	if(file_exists($filepath)) {
    	    $ini = new Peak_Config_Ini($filepath);
    	    $merge_vars = $ini->arrayMergeRecursive($ini->getVars(), $this->_vars);
    	    $this->_vars = $merge_vars;
    	}
    }    
}