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
        elseif((defined('DEV_MODE')) && (DEV_MODE)) {
            trigger_error('DEV_MODE: Controller method '.$helper.'() doesn\'t exists');
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

        //retreive requests param from router and remove 'mod' request witch it's used only by router
        $this->params = Peak_Registry::o()->router->params;
        $this->params_assoc = Peak_Registry::o()->router->params_assoc;
    }

    /**
     * Create a list of "actions"(methods)
     * Support methods with underscore(_) suffix
     */
    public function listActions()
    {
        $c_methods = get_class_methods($this->name);
     
        $regexp = '/^([_]{1}[a-zA-Z]{1})/';
              
        foreach($c_methods as $method) {            
            if(preg_match($regexp,$method)) $this->actions[] = $method;
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
     * Analyse router and lauch associated action method
     *
     * @param string $action_by_default   default method name if no request match to module actions
     */   
    public function handleAction($action_by_default = '_index')
    {
        $this->preAction();
        
        $action = Peak_Registry::o()->router->action;
        if(empty($action)) $action = $action_by_default;
        
        if(($this->isAction($action))) $this->action = $action;
        elseif(($action !== $action_by_default) && (!($this->isAction($action)))) {
        	throw new Peak_Exception('ERR_CTRL_ACTION_NOT_FOUND', array($action, $this->name));
        }
        else throw new Peak_Exception('ERR_CTRL_DEFAULT_ACTION_NOT_FOUND');       

        //set action filename
        $this->file = substr($this->action,1).'.php';
        
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