<?php

/**
 * Peak abstract controller
 * 
 * @author   Francois Lajoie
 * @version  $Id$
 */
abstract class Peak_Controller
{
    public $name;                 //child class name
    public $title;                //controller("module") title from wyncore

    public $file = null;          //view script file to render
    public $path;                 //absolute view scripts controller path
    public $type;                 //'controller' or a 'module' controller
       
    public $actions = array();    //actions methods list

    protected $view;              //instance of view
    
    protected $helpers;           //controller helpers objects
    
    protected $params;            //request params array
    protected $params_assoc;      //request params associative array
    protected $action;            //action called by handleRequest()
    
        
    public function __construct()
    {   
        //initialize ctrl
        $this->initController();
    }
    
    /**
     * Try to return a helper object based the method name.
     *
     * @param  string $helper
     * @param  null   $args not used
     * @return object
     */
    public function __call($helper, $args = null)
    {
    	if((isset($this->helper()->$helper)) || ($this->helper()->exists($helper))) {
        	return $this->helper()->$helper;
        }
        elseif((defined('DEV_MODE')) && (DEV_MODE)) {
            trigger_error('DEV_MODE: Controller method '.$method.'() doesn\'t exists');
        }
    }

    /**
     * Initialize controller $name, $title, $path, $url_path and $type
     * 
     */
    final private function initController()
    {       
        $this->view = Peak_Registry::obj()->view;
                               
        $this->name = get_class($this);              
        $this->title = $this->name;      
        $this->type = Peak_Registry::obj()->router->controller_type;
        
        $core = Peak_Registry::obj()->core;
        
        if($this->type === 'module') {
            $this->path = $core->getPath('modules').'/'.$this->name;
            $this->title = $core->getModule($this->name,'title');
            if(is_null($this->title)) $this->title = str_ireplace('controller', '', $this->name);
        }
        else {
        	$script_folder = str_ireplace('controller', '', $this->name);
        	$this->title = $script_folder;
            $this->path = $core->getPath('theme_scripts').'/'.$script_folder;
        }

        //retreive requests param from router and remove 'mod' request witch it's used only by router
        $this->params = Peak_Registry::obj()->router->params;
        $this->params_assoc = Peak_Registry::obj()->router->params_assoc;
    }
    
    /**
     * Create a list of "actions"(methods) 
     * Support methods with _ suffix(_dashboard)  and method like zend (dashboardAction)
     * 
     */
    public function listActions()
    {
        $c_methods = get_class_methods($this->name);
     
        $regexp = '/^([_]{1}[a-zA-Z]{1})/';
        $regexp2 = '/^([a-zA-Z]*)Action$/';
        $methods_ignored = array('preAction','postAction','isAction','handleAction','isZendAction','zendAction','getAction');
              
        foreach($c_methods as $method) {            
            if(preg_match($regexp,$method)) $this->actions[] = $method;
            elseif((preg_match($regexp2,$method)) && (!in_array($method,$methods_ignored))) $this->actions[] = $method;
        }
    }
    
    /**
     * Check if action exists. Support zend controller action method name
     *
     * @param  string $name
     * @return bool
     */
    public function isAction($name)
    {
    	return (method_exists($this->name,$name)) ? true : false;
    }
    
    /**
     * Check if zend action exists.
     *
     * @param  string $name
     * @return bool
     */
    public function isZendAction($name)
    {
    	$name = $this->zendAction($name);
    	if(method_exists($this->name,$name)) {
    		return true;
    	}
    }
    
    /**
     * Format peak action to zend action syntax
     *
     * @param string $action
     */
    public function zendAction($name)
    {
    	return str_replace('_','',$name).'Action';
    }
       
    /**
     * Analyse router and lauch associated action method
     *
     * @param string $action_by_default   default method name if no request match to module actions
     */   
    public function handleAction($action_by_default = '_index')
    {
        $this->preAction();
        
        $action = Peak_Registry::obj()->router->action;
        
        if((isset($action)) && ($this->isAction($action))) $this->action = $action;
        elseif((isset($action)) && ($this->isZendAction($action))) 
        {
        	$this->action = $action;
        	$action = $this->zendAction($action);
        }
        elseif((isset($action_by_default)) && ($this->isAction($action_by_default)))
        {
            $action = $action_by_default;
            $this->action = $action_by_default;
        }
        else throw new Peak_Exception('ERR_CTRL_DEFAULT_ACTION_NOT_FOUND');       

        //set action filename
        $this->file = ($this->type === 'controller') ? substr($this->action,1).'.php' : 'view.'.substr($this->action,1).'.php';
        
        //call requested action
        $this->$action();    
        
        $this->postAction();
    }
    
    /**
     * Load/access to controllers helpers objects
     * 
     * @return object Peak_Controller_Helpers
     */
    public function helper()
    {
        if(!is_object($this->helpers)) $this->helpers = new Peak_Controller_Helpers();
    	return $this->helpers;
    }
    
        
    /**
     * Get current action method name
     *
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }
           
    /**
     * Call view render with controller $file and $path
     *
     * @return string
     */    
    public function render()
    {                
        $this->view->render($this->file,$this->path);     
        $this->postRender();
    }
    
    /**
     * Action before controller requested action
     */
    public function preAction() { }
    
    /**
     * Action after controller requested action
     */
    public function postAction() { }
    
    /**
     * Action after view rendering
     */
    public function postRender() { }
    
}