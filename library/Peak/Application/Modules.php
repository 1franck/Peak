<?php

/**
 * Peak Modules Application Abstract Launcher 
 * 
 * @author   Francois Lajoie
 * @version  $Id$
 */
abstract class Peak_Application_Modules
{
	   
    protected $_module_name = '';
    private   $_ctrl_name = '';
         
    /**
     * Get the name of child class and use it as the module name
     * Prepare core to run a module
     * init module bootstrap if exists
     */
    public function __construct()
    {   
    	//ctrl name
    	$this->_ctrl_name = str_ireplace('controller','',get_class($this));     	  	
        //module name
        if(empty($this->_module_name)) $this->_module_name = $this->_ctrl_name;
        
        if(!(Peak_Registry::o()->core->isModule($this->_module_name))) die('Application modules '.$this->_module_name.' not found');

        //overdrive application paths to modules folder with Peak_Core_Extension_Modules
        Peak_Registry::o()->core->modules()->init($this->_module_name);
              
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
     * Run modules requested controller.
     *
     * @param string $default_ctrl
     */
    public function run($default_ctrl = 'indexController')
    {      	
        //add module name to the end Peak_Router $base_uri
        Peak_Registry::o()->router->base_uri = Peak_Registry::obj()->router->base_uri.$this->_module_name;   

        //re-call Peak_Application run() for handling the new routing
        Peak_Registry::o()->app->run($default_ctrl);
    }

        
}