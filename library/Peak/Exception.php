<?php

/**
 * Peak exception
 * 
 * @author   Francois Lajoie
 * @version  $Id$
 */
class Peak_Exception extends Exception
{
	   
    /**
     * Error constant name
     * @var string
     */
	private $_errkey;
    
	/**
	 * Errors messages
	 */
	const ERR_APP_CTRL_NOT_FOUND            = 'Application: controller %1$s not found.';
	const ERR_ROUTER_URI_NOT_FOUND          = 'Router: request uri not found.';
	const ERR_CORE_EXTENSION_NOT_FOUND      = 'Core: extension %1$s not found.';
	const ERR_CTRL_ACTION_NOT_FOUND         = 'Controller: action \'%1$s\' not found in %2$s.';
	const ERR_CTRL_DEFAULT_ACTION_NOT_FOUND = 'Controller: no _index() method found.';
	const ERR_CTRL_HELPER_NOT_FOUND         = 'Controller: helper \'%1$s\' not found.';
	const ERR_VIEW_ENGINE_NOT_SET           = 'View rendering engine not set. Use setRenderEngine() from Peak_View before trying to render application controller.';
	const ERR_VIEW_HELPER_NOT_FOUND         = 'View: helper \'%1$s\' not found.';
	const ERR_VIEW_SCRIPT_NOT_FOUND         = 'View: script file %1$s not found.';
	const ERR_VIEW_THEME_NOT_FOUND          = 'View: theme \'%1$s\' folder not found.';
	const ERR_DEFAULT                       = 'Request failed';
		
	
    /**
     * Set error key constant
     *
     * @param string $errkey
     * @param string $infos
     */
    public function __construct($errkey = null, $infos = null)
	{		    	    
	    $this->_errkey = $errkey;    
	    
	    $message = $this->handleErrConstToText($errkey,$infos);	    
   
		parent::__construct($message);
	}
	
	/**
	 * Handle error key constants
	 *
	 * @param  integer $errkey
	 * @return string  $info
	 */
	public function handleErrConstToText($errkey = null,$infos = null)
	{ 
	    if(defined(sprintf('%s::%s', get_class($this), $errkey))) {
	        $r = constant(sprintf('%s::%s', get_class($this), $errkey));
	    }
	    else $r = self::ERR_DEFAULT;
	    
	    if(isset($infos)) {
	        if(is_array($infos)) $r = vsprintf($r,$infos);
	        else $r = sprintf($r,trim($infos));	        
	    }

		return $r."\n";
	}

	/**
	 * Get debug trace of current exception @deprecated
	 *
	 * @return string
	 */
	public function getDebugTrace()
	{
		$trace = debug_backtrace();

		$err_propagation = array();
		foreach($trace as $i => $v) {
			if(isset($v['file']) && isset($v['line'])) $err_propagation[$v['line']] = $v['file'];
		}

		$debug = 'Files:<br />';
		foreach($err_propagation as $line => $file) $debug .= '- '.$file.' (Line: '.$line.')<br />';

		if((defined('DEV_MODE')) && (DEV_MODE)) {
			$debug .= '<br />Trace dump ['.$this->getTime().']:<pre>';
			$debug .= print_r($trace,true);
			$debug .= '</pre>';
		}
		
		return $debug;
	}
	
		
	public function getErrkey() { return $this->_errkey; }
		
	public function getLevel() { return $this->_level; }
	
	public function getTime() { return date('Y-m-d H:i:s'); }

}