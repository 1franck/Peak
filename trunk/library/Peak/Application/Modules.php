<?php

/**
 * Peak Modules Application Abstract Launcher 
 * 
 * @author   Francois Lajoie
 * @version  $Id$
 */
abstract class Peak_Application_Modules
{
	   
    protected $_module_name;
         
    /**
     * Get the name of child class and use it as the module name
     * Prepare core to run a module
     * init module bootstrap if exists
     */
    public function __construct()
    {   
        //name of child class
        $this->_module_name = str_ireplace('controller','',get_class($this));

        //overdrive application path to modules folder
        Peak_Core::initModule($this->_module_name);
              
        //initialize module bootstrap
        if(file_exists(Peak_Core::getPath('application').'/bootstrap.php')) {
        	include Peak_Core::getPath('application').'/bootstrap.php';
        }
        $bootstrap_class = $this->_module_name.'_Bootstrap';
        if(class_exists($bootstrap_class)) 
        {
        	$this->bootstrap = new $bootstrap_class();
        }
    }
      
    
    /**
     * Run modules requested controller. Will overdrive Peak_Application controller object property
     *
     * @param string $default_ctrl
     */
    public function run($default_ctrl = 'index')
    {
    	$app = Peak_Registry::obj()->app;
    	$router = Peak_Registry::obj()->router;       
        $core = Peak_Registry::obj()->core;
             
        $router->base_uri = $router->base_uri.$this->_module_name;
        
        $router->getRequestURI();
        
    	if(isset($router->controller))
    	{
    		$ctrl_name = $router->controller.'Controller';
    		if(!$core->isController($ctrl_name)) throw new Peak_Exception('ERR_APP_MOD_NOT_FOUND', $ctrl_name);
    		$app->controller = new $ctrl_name();
    	}
    	elseif((isset($default_ctrl)))
    	{
    		$ctrl_name = $default_ctrl.'Controller';
    		//if(!$core->isController($ctrl_name)) throw new Peak_Exception('ERR_APP_MOD_NOT_FOUND', $ctrl_name);
    		$app->controller = new $ctrl_name();
    	}
    	else throw new Peak_Exception('ERR_APP_MOD_NOT_SPECIFIED');
    	    
    }

        
}