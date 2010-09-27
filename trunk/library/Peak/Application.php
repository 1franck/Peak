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
     * app object itself
     * @var object
     */
    private static $_instance = null;
    
    /**
     * Singleton application
     *
     * @return  object instance
     */
    public static function getInstance()
	{
		if(is_null(self::$_instance)) self::$_instance = new self();
		return self::$_instance;
	}

	/**
	 * Start framework
	 */
    private function __construct()
    {                
        // start registry     
        $reg = Peak_Registry::getInstance();                
        
        // register application/core/view/router instance
        $reg->set('app', $this);
        $reg->set('core', Peak_Core::getInstance());
        $reg->set('view', new Peak_View('default.ini'));
        $reg->set('router', new Peak_Router(ROOT));
        
        // execute app bootstrap
        if(class_exists('bootstrap',false)) new bootstrap();   
        
        // load front controller
        if(class_exists('front',false)) {
        	$this->front = new front();
        } else $this->front = new Peak_Controller_Front();
    }
   	   
    
    /**
     * Load front controller and start dispatching
     *
     * @param string $default_ctrl Controller called by default when no request
     * @param bool   $flush_request Flush all router request and try to execute controller $default_ctrl
     */
    public function run($default_ctrl = 'index', $flush_request = false)
    {	    	
    	$this->front->preDispatch();
    	
    	$this->front->dispatch($default_ctrl, $flush_request);   	
    }
    
    /**
     * Call front controller render() method
     */
    public function render()
    {
    	$this->front->controller->render();
    }
                	         
}