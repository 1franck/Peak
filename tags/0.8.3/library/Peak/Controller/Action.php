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
	 * child class name
	 * @var string
	 */
    public $name;

    /**
     * child controller title
     * @var string
     */
    public $title;

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
     * actions methods list
     * @var array
     */
    public $actions = array();
    
    /**
     * Action method prefix
     * @var string
     */
    protected $action_prefix = '_';

    /**
     * instance of view
     * @var object
     */
    protected $view;

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
                               
        $this->name = get_class($this);              
        $this->title = str_ireplace('controller', '', $this->name);      
  
        $this->path = Peak_Core::getPath('theme_scripts').'/'.$this->title;

        //retreive requests param(s) from router
        $this->params = Peak_Registry::o()->router->params;
        $this->params_assoc = Peak_Registry::o()->router->params_assoc;
    }

    /**
     * Create a list of "actions"(methods)
     */
    public function listActions()
    {
    	$this->actions = array();
    	
        $c_methods = get_class_methods($this->name);

        $regexp = '/^(['.$this->action_prefix.']{'.strlen($this->action_prefix).'}[a-zA-Z]{1})/';
              
        foreach($c_methods as $method) {            
            if(preg_match($regexp,$method)) $this->actions[] = $method;
        }
    }

    /**
     * Check if action method name exists
     *
     * @param  string $name
     * @return bool
     */
    public function isAction($name)
    {
    	return (method_exists($this->name, $name)) ? true : false;
    }

    /**
     * Analyse router and lauch associated action method
     *
     * @param string $action_by_default   default method name if no request match to module actions
     */   
    public function handleAction($action_by_default = 'index')
    {
        $this->preAction();
        
        $action = $this->action_prefix . Peak_Registry::o()->router->action;
        if($action === $this->action_prefix) $action = $this->action_prefix . $action_by_default;
        
        if(($this->isAction($action))) $this->action = $action;
        elseif(($action !== $action_by_default) && (!($this->isAction($action)))) {
        	throw new Peak_Exception('ERR_CTRL_ACTION_NOT_FOUND', array($action, $this->name));
        }
        else throw new Peak_Exception('ERR_CTRL_DEFAULT_ACTION_NOT_FOUND');       

        //set action filename
        if($this->action_prefix === '_') $this->file = substr($this->action,1).'.php';
        else $this->file = str_replace($this->action_prefix, '',$this->action).'.php';
        
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
        $this->view->render($this->file, $this->path);     
        $this->postRender();
    }

    /**
     * Call front controller redirect() method
     *
     * @param string     $ctrl
     * @param string     $action
     * @param array/null $params
     */
    public function redirect($ctrl, $action, $params = null)
    {
    	Peak_Registry::o()->app->front->redirect($ctrl, $action, $params);
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