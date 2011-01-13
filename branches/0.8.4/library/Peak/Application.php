<?php
/**
 * Load the framework objects, application bootstrap and front controller.
 *   
 * @author    Francois Lajoie
 * @version   $Id$
 * @exception Peak_Exception
 */
class Peak_Application
{

	/**
	 * app bootstrap object if exists
	 * @var object
	 */
    public $bootstrap;

    /**
     * app object front controller
     * @var object
     */
    public $front;
    
    /**
     * Module app controller
     * @var object|null
     */
    public $module = null;

	/**
	 * Start framework
	 */
    public function __construct()
    {                
        // register application/view/router instance
        Peak_Registry::set('app', $this);
        Peak_Registry::set('view', new Peak_View('default.ini'));
        Peak_Registry::set('router', new Peak_Router(PUBLIC_ROOT));
             
        // load app bootstrap
        if(class_exists('bootstrap',false)) $this->bootstrap = new bootstrap();   
        
        // load front controller
        if(class_exists('front',false)) {
        	$this->front = new front();
        } else $this->front = new Peak_Controller_Front();
    }
    
    /**
     * Load front controller and start dispatching
     * @see Peak_Controller_Front::dispatch() for param
     */
    public function run()
    {
    	$this->front->getRoute();
    	$this->front->preDispatch();
    	$this->front->dispatch();
    	
    	return $this;
    }

    /**
     * Call front controller render() method
     */
    public function render()
    {
    	$this->front->controller->render();
    	$this->front->postRender();
    }

}