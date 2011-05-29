<?php
/**
 * Peak abstract action controller
 * 
 * @author   Francois Lajoie
 * @version  $Id$
 */
abstract class Peak_Controller_Action
{
    /**
     * view script file to render
     * @var string
     */
    public $file;

    /**
     * view scripts controller absolute path
     * @var string
     */
    public $path;

    /**
     * action called by handleRequest()
     * @var string
     */
    public $action;
    
    /**
     * Action method prefix
     * @var string
     */
    protected $action_prefix = '_';

    /**
     * instance of view
     * @var object
     */
    public $view;

    /**
     * controller helpers objects
     * @var object
     */
    protected $helpers;

    /**
     * request params array
     * @var array
     */
    protected $params;

    /**
     * request params associative array
     * @var array
     */
    protected $params_assoc;


    public function __construct()
    {   
        //initialize ctrl
        $this->initController();
        //get route to dispatch
        $this->getRoute();
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
        elseif(defined('APPLICATION_ENV') && in_array(APPLICATION_ENV, array('development', 'testing'))) {
            trigger_error('Controller method/helper '.$method.'() doesn\'t exists');
        }
    }

    /**
     * Initialize controller $name, $title, $path, $url_path and $type
     * @final
     */
    final private function initController()
    {       
        $this->view = Peak_Registry::o()->view; 
  
        $this->path = Peak_Core::getPath('theme_scripts').'/'.$this->getTitle();
    }
    
    /**
     * Get controller class name
     *
     * @return string
     */
    public function getName()
    {
        return get_class($this);
    }
    
    /**
     * Get controller class title
     * 
     * @return string
     */
    public function getTitle()
    {
        return str_ireplace('controller', '', $this->getName());  
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
     * Get array of controller "actions"(methods)
     */
    public function getActions()
    {
    	$actions = array();
    	
        $c_methods = get_class_methods($this);

        $regexp = '/^(['.$this->action_prefix.']{'.strlen($this->action_prefix).'}[a-zA-Z]{1})/';
              
        foreach($c_methods as $method) {            
            if(preg_match($regexp,$method)) $actions[] = $method;
        }

        return $actions;
    }
    
    /**
     * Get data from router needed for dispatch
     */
    public function getRoute()
    {
        $this->params = Peak_Registry::o()->router->params;        
        $this->params_assoc = new Peak_Config(Peak_Registry::o()->router->params_assoc);
        $this->action = $this->action_prefix . Peak_Registry::o()->router->action;
    }    
    
    /**
     * Dispatch controller action and other stuff around it
     */
    public function dispatch()
    {
        $this->preAction();
        $this->dispatchAction();
        $this->postAction();
    }
    
    /**
     * Dispatch action requested by router or the default action(_index)
     */
    public function dispatchAction()
    {
        $action = $this->action;
        if($action === $this->action_prefix) $action = $this->action_prefix . 'index';
        
        if(($this->isAction($action))) $this->action = $action;
        elseif(($action !== 'index') && (!($this->isAction($action)))) {
        	throw new Peak_Exception('ERR_CTRL_ACTION_NOT_FOUND', array($action, $this->getName()));
        }
        else throw new Peak_Exception('ERR_CTRL_DEFAULT_ACTION_NOT_FOUND');       

        //set action filename
        if($this->action_prefix === '_') $this->file = substr($this->action,1).'.php';
        else $this->file = str_replace($this->action_prefix, '',$this->action).'.php';
        
        //call requested action
        $this->$action(); 
    }

    /**
     * Check if action method name exists
     *
     * @param  string $name
     * @return bool
     */
    public function isAction($name)
    {
    	return (method_exists($this, $name)) ? true : false;
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
     * Instanciate models
     * 
     * @example 
     * $page = $this->model('test/page')  is the same as $page = new App_Models_Test_Page();
     * $this->model('test/page', 'page')  is the same as $this->page = new App_Models_Test_Page();
     *
     * @param  string $model_path
     * @param  string $varname
     * @return objct    return object if $varname is null
     */
    public function model($model_path, $varname = null)
    {
        $model = str_replace('/','_',$model_path);
        $class = 'App_Models_'.$model;
        if(isset($varname)) {
            $this->$varname = new $class();
            return $this;
        }
        else return new $class();
    }

    /**
     * Access to params_assoc object
     *
     * @return object
     */
    public function params()
    {
        return $this->params_assoc;
    }    

    /**
     * Call view render with controller $file and $path
     *
     * @return string
     */    
    public function render()
    {                
        $this->view->render($this->file, $this->path);     
        $this->postRender();
    }

    /**
     * Call front controller redirect() method
     *
     * @param string     $ctrl
     * @param string     $action
     * @param array|null $params
     */
    public function redirect($ctrl, $action, $params = null)
    {
        Peak_Registry::o()->app->front->redirect($ctrl, $action, $params);
    }
    
    /**
     * Call front controller redirect() method. 
     * Same as redirect() but redirect to an action in the current controller only
     *
     * @param string     $action
     * @param array|null $params
     */
    public function redirectAction($action, $params = null)
    {
        Peak_Registry::o()->app->front->redirect($this->getTitle(), $action, $params);
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