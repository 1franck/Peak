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
	 * Call appropriate dispatching methods
	 */
	public function dispatch()
	{
	    $this->_dispatchController();
	    
	    if($this->controller instanceof Peak_Application_Modules) {
        	$this->_dispatchModule();
        }               
        // execute a normal controller action
        elseif($this->controller instanceof Peak_Controller_Action) {
        	$this->_dispatchControllerAction(); 
        }
	}
	
	/**
	 * Dispatch appropriate controller according to the router
	 */
	protected function _dispatchController()
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
	}
	
	/**
	 * Dispatch action of controller
	 */
	protected function _dispatchControllerAction()
	{
	    $this->controller->handleAction();
        $this->postDispatch(); 
	}
	
	/**
	 * Dispatch a module and run it
	 */
	protected function _dispatchModule()
	{
	    Peak_Registry::o()->app->module = $this->controller;
        $this->controller->run();
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
		//$this->forceDispatch($this->error_controller);
		
		$router = Peak_Registry::o()->router;
		$router->controller = $this->error_controller;
		$router->action     = 'index';
		
		$this->_dispatchController();

        if(($this->controller instanceof Peak_Controller_Action) && (isset($exception))) {
            $this->controller->exception = $exception;
        }
        
        $this->_dispatchControllerAction();
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