<?php

/**
 * Peak abstract controller
 * 
 * @author Francois Lajoie
 * @version 20100502
 *
 */
abstract class Peak_Controller
{
    public $name;                 //child class name
    public $title;                //controller("module") title from wyncore

    public $file = null;          //view script file to render
    public $path;                 //absolute view scripts controller path
    public $type;                 //'controller' or a 'module' controller
       
    public $c_actions = array();  //actions methods list   
    public $c_aprefix = '_';      //action methods name prefix

    protected $view;              //instance of view
    
    protected $helpers;           //controller helpers objects
    
    protected $params;            //request params
    protected $action;            //action called by handleRequest()
    
        
    public function __construct()
    {   
        //initialize ctrl
        $this->initController();
        
        //list all methods name beginning by $c_aprefix
        $this->listActions();

        //handle controller routing action
        //$this->handleAction();  
    }

    /**
     * Initialize controller $name, $title, $path, $url_path and $type
     *
     * @param string $ctrl_type 'module' or 'controller'
     */
    final private function initController()
    {       
        $this->view = Peak_Registry::obj()->view;
                
        $core = Peak_Registry::obj()->core;
               
        $this->name = get_class($this);       
        
        $this->title = $this->name;
        
        $this->type = Peak_Registry::obj()->router->controller_type;
        
        if($this->type === 'module') {
            $this->path = $core->getPath('modules').'/'.$this->name;
            $this->title = $core->getModule($this->name,'title');
            if(is_null($this->title)) $this->title = $this->name;
        }
        else {
            $this->path = THEME_SCRIPTS_ABSPATH.'/'.$this->name;
        }

        //retreive requests param from router and remove 'mod' request witch it's used only by router
        $this->params = Peak_Registry::obj()->router->params;
    }
    
    /**
     * Create a list of "actions"(methods) from child ctlr class beginning with a specified prefix
     * that can be called by get_ctlr
     *
     * @param string $prefix
     */
    protected function listActions($prefix = '_')
    {
        $c_methods = get_class_methods($this->name);
        
        $this->c_aprefix = $prefix;
        $regexp = '/^(['.$this->c_aprefix.']{1}[a-zA-Z]{1})/';
         
        foreach($c_methods as $method) {            
            if(preg_match($regexp,$method)) $this->c_actions[] = $method;
        }
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
        
        if((isset($action)) && (in_array($action,$this->c_actions))) $this->action = $action;
        elseif((isset($action_by_default)) && (in_array($action_by_default,$this->c_actions)))
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
     * Load helpers objects method and return helper obj
     * 
     * controller helper class name syntax  --> ctrl_helper_[title]
     * controller helper class file name    --> controllers/helpers/[title].php
     * controller helper call in ctrl class --> $this->helper([title])->myfunc([$param],[$param2],[..]);
     *
     * @param string $name specify an helper to load or ignore this param
     */
    public function helper($name)
    {
        $helper_name_prefix = 'ctrl_helper_';

        $name = trim(stripslashes(strip_tags($name)));
        $helper_file = CONTROLLERS_ABSPATH.'/helpers/'.$name.'.php';

        $new_helper = $helper_name_prefix.$name;

        if(!isset($this->helpers[$new_helper])) {
            if(file_exists($helper_file)) {
                include($helper_file);
                $this->helpers[$new_helper] = new $new_helper();
            }
            else {
                throw new Peak_Exception('ERR_CTRL_HELPER_NOT_FOUND');
            }
        }
        
        $name = $helper_name_prefix.$name;

        return $this->helpers[$new_helper];
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
    public function preAction()
    { 
        // Nothing by default    
    }
    
    /**
     * Action after controller requested action
     */
    public function postAction()
    {
        // Nothing by default
    }
    
    /**
     * Action after view rendering
     */
    public function postRender()
    {
        // Nothing by default
    }
    
}