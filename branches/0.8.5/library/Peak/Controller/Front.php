<?php
/**
 * Peak_Controller_Front
 * 
 * @author  Francois Lajoie
 * @version $Id$
 */
class Peak_Controller_Front
{

	/**
	 * Controller object
	 * @var object
	 */
	public $controller;

	/**
	 * Default controller name
	 * @var string
	 */
	public $default_controller = 'index';
	
	/**
	 * Exception|error controller (used by errorDispatch())
	 * @var string
	 */
	public $error_controller = 'error';
	
	/**
	 * Allow/Disallow the use of Peak library internal controllers
	 * @var bool
	 */
	public $allow_internal_controllers = false;
	
	
	/**
	 * class construct
	 */
	public function __construct()
	{
		$this->_registryConfig();
	}
	
	/**
     * Get array 'front' from registered object 'config' if exists
     */
    private function _registryConfig()
    {
    	if(isset(Peak_Registry::o()->config->front)) {
    		foreach(Peak_Registry::o()->config->front as $k => $v) {
    			if($k === 'allow_internal_controllers') $v = (bool)$v;
    			$this->$k = $v;
    		}
    	}
    }

	/**
	 * Initialize router uri request
	 */
	public function getRoute()
	{
		Peak_Registry::o()->router->getRequestURI();
	}

	/**
	 * Called before routing dispatching
	 * Empty by default
	 */
	public function preDispatch() {	}

	/**
	 * Start dispatching to controller with the help of router
	 * 
	 * @param string $default_ctrl Controller called by default when no request
	 */
	public function dispatch()
	{
		$router = Peak_Registry::o()->router;      
		
		//set default controller if router doesn't have one
		if(!isset($router->controller)) {
			$router->controller = $this->default_controller;
		}

		//set controller class name
		$ctrl_name = $router->controller.'Controller';

		//check if it's valid application controller
		if(!$this->isController($ctrl_name))
		{
			//check for peak internal controller
			if(($this->allow_internal_controllers === true) && ($this->isInternalController($router->controller))) {
				$ctrl_name = 'Peak_Controller_Internal_'.$router->controller;
				$this->controller = new $ctrl_name();
			}
			else throw new Peak_Exception('ERR_APP_CTRL_NOT_FOUND', $ctrl_name);
		}
		else $this->controller = new $ctrl_name();
 
        //if class if is an instance of Peak_Application_Modules, load a module app via a controller
        if($this->controller instanceof Peak_Application_Modules) 
        {
        	Peak_Registry::o()->app->module = $this->controller;
        	$this->controller->run();
        }               
        // execute a normal controller action
        elseif($this->controller instanceof Peak_Controller_Action) {
        	$this->controller->handleAction();
        	$this->postDispatch();  
        }       
        else {
        	//need something
        	//class is neither a module or a controller
        }
	}
	
	/**
	 * Force dispatching to a specific controller/action
	 *
	 * @param string $ctrl
	 * @param string $action
	 */
	public function forceDispatch($controller, $action = 'index')
	{
		$router = Peak_Registry::o()->router;
		$router->controller = $controller;
		$router->action = $action;
		$this->dispatch();
	}
	
	/**
	 * Force dispatch of $error_controller
     *
     * @param object $exception
	 */
	public function errorDispatch($exception = null)
	{
		$this->forceDispatch($this->error_controller);

        if(($this->controller instanceof Peak_Controller_Action) && (isset($exception))) {
            $this->controller->exception = $exception;
        }
	}

    /**
     * Set a new request and redispath the controller
     *
     * @param string     $ctrl
     * @param string     $action
     * @param array/null $params
     */
    public function redirect($ctrl, $action = 'index', $params = null)
    {
    	Peak_Registry::o()->router->setRequest( array($ctrl, $action, $params) );
    	$this->dispatch();
    }

    /**
	 * Called after controller action dispatching
	 * Empty by default
	 */
    public function postDispatch() { }
    
    /**
     * Called after rendering controller view
     * Empty by default
     */
    public function postRender() { }   
    
    /**
     * Check if controller filename exists
     *
     * @param  string $name
     * @return bool
     */
    public function isController($name)
    {
    	return (file_exists(Peak_Core::getPath('controllers').'/'.$name.'.php')); 
    }

    /**
     * Check if internal Peak Controller filename exists
     *
     * @param  string $name
     * @return bool
     */
    public function isInternalController($name)
    {
    	return (file_exists(LIBRARY_ABSPATH.'/Peak/Controller/Internal/'.$name.'.php')) ? true : false;
    }

    /**
     * Check if modules dirname exists
     *
     * @param  string $name
     * @return bool
     */
    public function isModule($name)
    {
    	return (file_exists(Peak_Core::getPath('modules').'/'.$name)) ? true : false;
    }
}