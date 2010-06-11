<?php

/**
 * Peak_Dispatcher
 * 
 * @desc This is an standalone component and should no be used inside framework MVC!
 *       This class looks for actions keys in global var like $_GET and $_POST and 
 *       dispatch them to action(s) depending on $_recursive_depth properties.
 *  
 * @author  Francois Lajoie
 * @version $Id$
 */
abstract class Peak_Dispatcher
{
      
    public $request;
    
    private $_accepted_globals = array('_GET','_POST','_SESSION');
    
    private $_actions;
    
    private $_recursivity  = false;
    
    private $_recursivity_depth = 3;
    
    private $_action_triggered = 0;
    
    
    /**
     * Load dispatcher actions
     */
    public function __construct($accepted_globals = null, $recursivity = false, $recursivity_depth = 3)
    {
        if(isset($accepted_globals)) $this->_accepted_globals = $accepted_globals;
        $this->setRecursivity($recursivity, $recursivity_depth);
        $this->_listActions();
    }
    
    /**
     * Start the dispath
     *
     */
    public function start()
    {
        foreach($this->_accepted_globals as $prefix) 
        {            
            switch($prefix) {
                case '_GET' : $resource = $_GET; break;
                case '_POST' : $resource = $_POST; break;
                case '_SESSION' : $resource = $_SESSION; break;
                default : $resource = null;
            }
            
            if(is_array($resource))
            {                
                foreach($this->_actions as $action)
                {                   
                    $action_key = str_ireplace($prefix.'_','',$action);
                                            
                    if(isset($resource[$action_key])) {
                        ++$this->_action_triggered;
                        $this->$action();
                        if(!$this->_recursivity) return;
                        else {                           
                            if($this->_action_triggered >= $this->_recursivity_depth) {                        
                                $this->stop();
                                return;
                            }
                        }
                    }
                }
            }
            
        }
    }
    
    
    /**
     * Stop recursivity
     *
     */
    public function stop()
    {
        $this->_recursivity = false;
    }
    
    /**
     * Set Recursion to true/false
     *
     * @param bool $status
     */
    public function setRecursivity($status,$depth = 3)
    {
        $this->_recursivity = $status;
        $this->_recursivity_depth = $depth;        
    }
    
    /**
     * List all actions regarding $_accepted_globals
     */
    private function _listActions()
    {       
        $regexps = array();
        
        foreach($this->_accepted_globals as $prefix) {
            $l = strlen($prefix) + 1;
            $regexps[] = '/^(['.$prefix.'_]{'.$l.'}[a-zA-Z]{1})/';
        }
        //print_r($regexps);
                
        $c_methods = get_class_methods(get_class($this));
        
        //print_r($c_methods);
        
        if(!is_null($c_methods)) {
            foreach($c_methods as $method) {
                foreach($regexps as $regexp) {
                    if(preg_match($regexp,$method)) {
                        $this->_actions[] = $method; break;
                    }
                }
            }
        }
    }
    
    /**
     * Return actions
     *
     * @return array
     */
    public function getActions()
    {
        return $this->_actions;
    }
    
}