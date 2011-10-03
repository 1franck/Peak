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
	const ERR_ROUTER_URI_NOT_FOUND          = 'Request uri not found.';
	const ERR_CORE_INIT_CONST_MISSING       = '%1$s is not specified (const %2$s)';
	const ERR_DEFAULT                       = 'Request failed';
	const ERR_CUSTOM                        = '%1$s';


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
			$r = (is_array($infos)) ? vsprintf($r,$infos) : sprintf($r,trim($infos));
	    }

		return htmlentities(strip_tags($r))."\n";
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

		if((defined('APPLICATION_ENV')) && (APPLICATION_ENV === 'development')) {
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