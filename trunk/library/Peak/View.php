<?php

/**
 * Peak View
 * 
 * @author   Francois Lajoie
 * @version  $Id$
 * 
 * @desc     template variables registry, helpers object, theme object
 * @uses     Peak_View_Theme, Peak_View_Helpers, Peak_View_Render, Peak_View_Render_*
 */
class Peak_View
{
	
    protected $vars = array();            //view vars
    
    private $helpers;                     //view helpers object 
    
    private $theme;                       //view theme object
    
    private $view_engine = 'partials';    //view rendering object * partials by default       
  
    /**
     * Start template - set an array as template variable(optionnal)
     *
     * @param array $vars
     */
    public function __construct($vars = null)
    {
        if(isset($vars)) {
            if(is_array($vars)) $this->vars = $vars;
            else $this->iniVar($vars);
        }
    }
    
    
    /**
     * Set/overwrite template variable
     *
     * @param string $name
     * @param anything $value
     */
    public function __set($name,$value = null)
    {
        $this->vars[$name] = $value;
    }
    
    /**
     * Unset $vars namekey
     *
     * @param string $name
     */
    public function __unset($name)
    {
    	if(array_key_exists($name,$this->vars)) unset($this->vars[$name]);
    }
    
    /**
     * Get view variable
     *
     * @param  string $name
     * @return anything
     */
    public function __get($name)
    {        
        return array_key_exists($name,$this->vars) ? $this->vars[$name] : null;
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
        elseif((defined('DEV_MODE')) && (DEV_MODE)) {
            trigger_error('DEV_MODE: View Render method '.$method.'() doesn\'t exists');
        }
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
    public function &getVars()
    {
        return $this->vars;
    }
    
    /**
     * Clean all variable in $vars
     */
    public function resetVars()
    {
        $this->vars = array();
    }
        
    /**
     * Set view rendering engine
     * 'Partials' by default : This represent the default behavior of displaying directly controller view or group it with partials files.
     * 
     * @param string $engine [Partials|Layouts|Xml|Json] ( /Peak/View/Render/ )
     */
    public function setRenderEngine($engine = 'Partials')
    {
        switch($engine)
        {
            case 'partials':
            case 'Partials':
                $groups = $this->theme()->getOptions('partials_groups');
                $groups = (is_array($groups)) ? $groups : array();  
                $options = $groups;
                break;
                
            default :
            	if(!class_exists('Peak_View_Render_'.$engine)) {
            		$this->setRenderEngine('Partials');
            		return;
            	}
                break;
        }
        
        $engine_class = 'Peak_View_Render_'.$engine;
        
        $this->view_engine = (isset($options)) ? new $engine_class($options) : new $engine_class();

        return $this;       
    }
    
    /**
     * Return current view rendering engine object
     *
     * @return object Peak_View_Render_*
     */
    public function engine()
    {
        return $this->view_engine;
    }
        
    /**
     * Render Controller Action View file with the current rendering engine
     * 
     * @param string $file
     * @param string $path
     * @return string or array   return array of view files when layout is used
     *
     */
    public function render($file,$path)
    {
        if(is_object($this->view_engine)) {
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
        if(!($this->theme instanceof Peak_View_Theme)) $this->theme = new Peak_View_Theme($folder);
        elseif(isset($folder)) $this->theme->folder($folder); 
        return $this->theme;
    }
    
    
    /**
     * Load helpers objects method and return helper obj
     *
     * @return object Peak_View_Helpers
     */
    public function helper()
    {
    	if(!is_object($this->helpers)) $this->helpers = new Peak_View_Helpers();
    	return $this->helpers;
    }
    
    /**
     * Load ini file into view vars
     *
     * @param string $file
     */
    public function iniVar($file)
    {
        $filepath = Peak_Core::getPath('views_ini').'/'.$file;
        if(file_exists($filepath)) {
            $ini_vars = parse_ini_file($filepath);
            
            //check for constants ( ini constant syntax = #CONST_NAME# )
            foreach($ini_vars as $k => $v)
            {
                $pattern = '/#(?P<name>\w+)#/i';  //# in ini is deprecated in php 5.3.x, need to be fix or change
                preg_match_all($pattern, $v, $m);
                                
                if(isset($m['name'])) {
                    foreach($m['name'] as $constant) {
                        if(defined($constant)) {
                            $ini_vars[$k] = str_replace('#'.$constant.'#',constant($constant),$ini_vars[$k]);
                        }
                    }
                }
            }
            $this->vars = array_merge($this->vars,$ini_vars);
        }
    }
    
}