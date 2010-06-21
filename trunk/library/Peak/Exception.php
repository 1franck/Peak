<?php

/**
 * Peak exception
 * 
 * @author   Francois Lajoie
 * @version  $Id$
 */
class Peak_Exception extends Exception
{
	
    private $_trace;	
	private $_errkey;
    
	const ERR_APP_MOD_NOT_FOUND             = 'Application: module %1$s not found.';
	const ERR_APP_MOD_NOT_SPECIFIED         = 'Application: module not specified.';
	const ERR_APP_CTRL_NOT_FOUND            = 'Application: controller %1$s not found.';
	const ERR_CORE_NO_CTRL_FOUND            = 'Core: application controllers not found.';
	const ERR_CTRL_DEFAULT_ACTION_NOT_FOUND = 'Controller: no _index() method found.';
	const ERR_VIEW_ENGINE_NOT_SET           = 'View rendering engine not set. Use setRenderEngine() from Peak_View before trying to render application controller.';
	const ERR_VIEW_TPL_NOT_FOUND            = 'View: template file %1$s not found.';
	const ERR_VIEW_LAYOUT_NOT_FOUND         = 'View: layout template file %1$s not found.';
	const ERR_DEFAULT                       = 'Request failed';
		
	
    /**
     * Set error key constant
     *
     * @param string $errkey
     * @param string $infos
     */
    public function __construct($errkey = null, $infos = null)
	{		    	    
	    $this->_trace = array('file' => basename(parent::getFile()),'line' => parent::getLine(),'code' => parent::getCode());

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
	        if(is_array($infos)) $r = vsprintf($r,trim($infos));
	        else $r = sprintf($r,trim($infos));	        
	    }

		return $r;
	}
	
	public function getErrkey() { return $this->_errkey; }
	
	public function getTraceBase() { return $this->_trace; }
	
	public function getLevel() { return $this->_level; }
	
	public function getTime() { return date('Y-m-d H:i:s'); }
}