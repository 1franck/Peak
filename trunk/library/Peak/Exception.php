<?php

/**
 * Peak exception
 * 
 * @author Francois Lajoie
 * @version 20100404
 * 
 */

class Peak_Exception extends Exception
{
	
    private $trace;	
	private $errkey;
    
	const ERR_ROUTER_MOD_NOT_FOUND          = 'Router: module %1$s not found';
	const ERR_ROUTER_MOD_NOT_SPECIFIED      = 'Router: module not specified';
	const ERR_ROUTER_CTRL_NOT_FOUND         = 'Router: controller %1$s not found';
	const ERR_CORE_NO_CTRL_FOUND            = 'Core: application controllers not found';
	const ERR_CTRL_DEFAULT_ACTION_NOT_FOUND = 'Controller: no _index() method found';
	const ERR_VIEW_TPL_NOT_FOUND            = 'View: template file %1$s not found';
	const ERR_VIEW_LAYOUT_NOT_FOUND         = 'View: layout template file %1$s not found';
	const ERR_DEFAULT                       = 'Request failed';
		
	
    /**
     * Traite l'erreur mysql et renvoi un message, puis log de celle-ci si $error != null 
     *
     * @param string $errkey
     * @param string $infos
     */
    public function __construct($errkey = null, $infos = null)
	{	
	    	    
	    $this->trace = array('file' => basename(parent::getFile()),'line' => parent::getLine(),'code' => parent::getCode());

	    $this->errkey = $errkey;    
	    
	    $message = $this->handleErrConstToText($errkey,$infos);
	    
	    
	    
		parent::__construct($message);
	}
	
	/**
	 * Handle pseudo constant error;
	 *
	 * @param  integer $no
	 * @return string
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
	
	public function getErrkey() { return $this->errkey; }
	
	public function getTraceBase() { return $this->trace; }
	
	public function getLevel() { return $this->level; }
	
	public function getTime() { return date('Y-m-d H:i:s'); }
}


?>