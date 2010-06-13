<?php

/**
 * Peak_Dispatcher
 * 
 * @desc This is an standalone component and should no be used inside framework MVC!
 *       This class looks for actions keys in global var like $_GET and $_POST and 
 *       dispatch them to action(s) depending on $_recursive_depth properties.
 *       IMPORTANT. The data of action value are not used neither filtered so be sure to escape/valid action datas before anything
 *  
 * @author  Francois Lajoie
 * @version $Id$
 */
abstract class Peak_Dispatcher
{
       
    private $_accepted_globals = array('_GET','_POST','_SESSION'); //global variable allow
    
    public $resource; //always reflect current resource of a called action
    
    private $_actions;  //actions method list depending on $_accepted_globals
    
    private $_recursivity  = false; //allow multiple actions calls
    
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
     * Start the first in, first out action(s) dispath. 
     */
    public function start()
    {
        foreach($this->_accepted_globals as $prefix) 
        {            
            switch($prefix) {
                case '_GET' : $this->resource = $_GET; break;
                case '_POST' : $this->resource = $_POST; break;
                case '_SESSION' : 
                     if(session_id() !== '') $this->resource = $_SESSION; 
                     else $this->resource = null;
                     break;
                default : $this->resource = null;
            }
            
            if(is_array($this->resource))
            {                
                foreach($this->_actions as $action)
                {                   
                    $action_key = str_ireplace($prefix.'_','',$action);
                                            
                    if(isset($this->resource[$action_key])) {
                        ++$this->_action_triggered;
                        $this->$action();
                        if(!$this->_recursivity) {
                            $this->stop();
                            return;
                        }
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
        $this->resource = null;
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