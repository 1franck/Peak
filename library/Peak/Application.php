<?php 

/**
 * Peak Application launcher
 * 
 * @author   Francois Lajoie
 * @version  $Id$
 */
class Peak_Application
{
    
    public $bootstrap;                //app bootstrap object if exists
  
    public $controller;               //app object controller
        
    private static $_instance = null; //app object itself
    
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
        
        // register application instance
        Peak_Registry::set('app', $this);
                               
        // register core
        $core = $reg->set('core', Peak_Core::getInstance());
        
        // register lang translator 
        $reg->set('lang', new Peak_Lang(APP_LANG) );
        
        // register template + setting wyn modules var
        $reg->set('view', new Peak_View('default.ini') );
        
        // register router
        $reg->set('router', new Peak_Router(ROOT));
        
        // execute app bootstrap
        if(class_exists('bootstrap',false)) new bootstrap();       
    }
          
    
	/**
	 * Internal login
	 *
	 * @return bool
	 */
	public function validSession() 
	{
	    $r = false;
	    
	    if(isset($_POST['lognow'])) {
            $_SESSION['wName'] = sha1(_clean($_POST['wname']));
            $_SESSION['wPass'] = sha1(_clean($_POST['wpass']));
        }
	    
	    if(!isset($_SESSION['wName'])) $_SESSION['wName'] = '';
	    if(!isset($_SESSION['wPass'])) $_SESSION['wPass'] = '';   
	    	    
	    if(defined('APP_LOGIN_NAME')) {
	        if(((sha1(APP_LOGIN_NAME) === $_SESSION['wName']))) {
	            $r = true;
	            if((defined('APP_LOGIN_PASS')) && (sha1(APP_LOGIN_PASS) !== $_SESSION['wPass'])) $r = false;            
	        }
	        else $r = false;
	    }
	    else $r = true; //if no W_LOGIN, skip login validation
	    
	    return $r;	    
	}
	   

    /**
     * Run application controller from router object
     *
     * @param string $default_ctrl Controller called by default when no request
     * @param bool $flush_request Flush all router request and try to execute controller $default_ctrl
     */
    public function run($default_ctrl = null, $flush_request = false)
    {
        $is_valid_session = $this->validSession();
        
        $router = Peak_Registry::obj()->router;
        $router->getRequestURI();
        
        $core = Peak_Registry::obj()->core;
                
        if($flush_request) $router->controller = $default_ctrl;
        
        if(($router->controller_type === 'module') && (!$is_valid_session)) {
            if($core->isModule($router->controller)) {
                if(!$core->getModule($router->controller,'login')) {
                    $is_valid_session = true;
                }              
            }
        }
        
        //beginning controller routing
        if($is_valid_session)
        {
            //normal controller + peak internal controller
            if($router->controller_type === 'controller')
            {
                if(isset($router->controller))
                {
                	$ctrl_name = $router->controller.'Controller';
                	
                    if((defined('ENABLE_PEAK_CONTROLLERS')) && (ENABLE_PEAK_CONTROLLERS)) {
                        $int_ctrl = LIBRARY_ABSPATH.'/Peak/Controller/Internal/'.$router->controller.'.php';
                    }
                    
                    //check is controller is not found in current core controllers
                    if(!$core->isController($ctrl_name))
                    {
                        //check for peak internal controller
                        if((isset($int_ctrl)) && (file_exists($int_ctrl))) {
                            $ctrl_name = 'Peak_Controller_Internal_'.$router->controller;
                            $this->controller = new $ctrl_name();             
                        }
                        else throw new Peak_Exception('ERR_APP_CTRL_NOT_FOUND', $ctrl_name);
                    }
                    else $this->controller = new $ctrl_name();                    
                }
                elseif((isset($default_ctrl)) && ($core->isController($default_ctrl))) 
                {
                    $this->controller = new $default_ctrl();
                }
                else throw new Peak_Exception('ERR_APP_CTRL_NOT_FOUND',$default_ctrl);
            }
            else
            {
                if(isset($router->controller))
                {
                	$ctrl_name = $router->controller;                	
                    if(!$core->isModule($ctrl_name)) throw new Peak_Exception('ERR_APP_MOD_NOT_FOUND', $ctrl_name);                   
                    $this->controller = new $ctrl_name();
                }
                else throw new Peak_Exception('ERR_APP_MOD_NOT_SPECIFIED');
            }
        }
        else {
        	$this->controller = new LoginController();  
        }
        
        // execute controller action
        $this->controller->handleAction();     
        
    }
    	         
}



function _clean($str) {
    $str = stripslashes($str); $str = strip_tags($str); $str = trim($str); $str = htmlspecialchars($str,ENT_NOQUOTES); $str = htmlentities($str);
    return $str;
}
function _cleans($strs,$keys_to_clean = null) {
    if(isset($keys_to_clean)) {  foreach($keys_to_clean as $k => $v) { if(isset($strs[$v])) $strs[$v] = _clean($strs[$v]); } }
    else { foreach($strs as $k => $v) $strs[$k] = _clean($v); }
    return $strs;
}