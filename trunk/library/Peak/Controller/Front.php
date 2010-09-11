<?php


/**
 * Peak_Controller_Front
 * 
 * @author  Francois Lajoie
 * @version $Id$
 *
 */
class Peak_Controller_Front
{
	
	
	public $controller;         //controller object
	
	public $default_controller; //defaut controller
	
	
	/**
	 * Initialize router uri request
	 */
	public function preDispatch()
	{
		Peak_Registry::o()->router->getRequestURI();
	}
	
	
	/**
	 * Start dispatching to controller with the help of router 
	 *
	 */
	public function dispatch($default_ctrl = null, $flush_request = false)
	{
		$router = Peak_Registry::o()->router;       
        $core   = Peak_Registry::o()->core;

        if(isset($default_ctrl)) $this->default_controller = $default_ctrl.'Controller';
        
        if($flush_request) { //$router->reset();
        	unset($router->controller);
        }
                
        if(isset($router->controller))
        {
        	$ctrl_name = $router->controller.'Controller';

        	//check if controller is not found in current core controllers
        	if(!$core->isController($ctrl_name))
        	{
        		//check for peak internal controller
        		if((defined('ENABLE_PEAK_CONTROLLERS')) && (ENABLE_PEAK_CONTROLLERS) &&
        		($core->isInternalController($router->controller))) {
        			$ctrl_name = 'Peak_Controller_Internal_'.$router->controller;
        			$this->controller = new $ctrl_name();
        		}
        		else throw new Peak_Exception('ERR_APP_CTRL_NOT_FOUND', $ctrl_name);
        	}
        	else $this->controller = new $ctrl_name();
        }
        elseif((isset($this->default_controller)) && ($core->isController($this->default_controller)))
        {
        	$default_ctrl = $this->default_controller;
        	$this->controller = new $default_ctrl();
        }
        else throw new Peak_Exception('ERR_APP_CTRL_NOT_FOUND',$default_ctrl);
        
        //class method run if exists, usefull for loading a modules app via a controller
        if($this->controller instanceof Peak_Application_Modules) $this->controller->run();
                
        // execute controller action
        elseif(method_exists($this->controller,'handleAction')) {
        	$this->controller->handleAction();
        	$this->postDispatch();        
        }
               
        //$this->redirect('index','index',null);            
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
	 *
	 */
    public function postDispatch()
    {
    	//nothing by default
    }
}