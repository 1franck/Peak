<?php

/**
 * Application Bootstrap base
 * 
 * @author   Francois Lajoie
 * @version  $Id$
 */
abstract class Peak_Bootstrap
{
    
    /**
     * init bootstrap
     */
    public function __construct()
    {
        $this->boot();
    }
    
    /**
     * Call all bootstrap methods prefixed by _
     *
     * @param string $prefix
     */
    private function boot($prefix = '_')
    {
        $c_methods = get_class_methods(get_class($this));
        $regexp = '/^(['.$prefix.']{1}[a-zA-Z]{1})/';      
        foreach($c_methods as $method) {            
            if(preg_match($regexp,$method)) $this->$method();
        }
    }
    
    /**
     * Get environment in .htaccess
     *
     * @return string
     */
    public function getEnvironment()
    {
    	return getenv('APPLICATION_ENV'); 	
    }

}