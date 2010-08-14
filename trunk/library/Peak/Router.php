<?php

/**
 * Simple router URL php rewriting
 * Support 2 type of controllers folders
 * Support url like :  http://example.com/[application php file]?[controller]=[action]&[param1]=[param2]&[...]
 *                     http://example.com/index.php?server=php&view=ver  
 *                     AND
 *                     http://example.com/[controller]/[action]/[param1]/[param2]/[...]
 *                     http://example.com/server/php/view/ver
 *
 * @author   Francois Lajoie
 * @version  $Id$
 * 
 * @todo filter variables
 * 
 * @uses .htaccess file example:
 * 
 * -------------------------------------
 * RewriteEngine On
 *
 * ## never rewrite for existing files, directories and links
 *
 * RewriteCond %{REQUEST_FILENAME} !-f
 * RewriteCond %{REQUEST_FILENAME} !-d
 * RewriteCond %{REQUEST_FILENAME} !-l
 *
 * ## rewrite everything else to index.php
 *
 * RewriteRule !\.(js|ico|gif|jpg|png|css)$ index.php
 * -------------------------------------            
 */
class Peak_Router
{
        
    public $base_uri;                   //default url relative root set with __construct()
    
    public $request_uri;                //$_SERVER['REQUEST_URI'] without base_uri.
    
    public $request;                    //original unparsed request array
    
    public $controller;                 //controller name
    
    public $controller_type;            //controller type
    
    public $action;                     //requested action
    
    public $params = array();           //action param(s) array
    
    public $params_assoc = array();     //actions param(s) associative array
    
    /**
     * Reserved first param keyword for designating the kind of controllers we request.
     * leaving empty keyword will make the controller type default @deprecated
     *
     * @var array
     */
    public $ctrls_type_base = array('controller' => '',
                                    'modules'    => 'mod');
    
    
    /**
     * Set base url of your application index.php
     * 
     * @example your application script page url is http://example.com/myapp/index.php
     *          so $base_uri would be : '/myapp/'
     * @param   string $base_uri - We recommened that you use constant 'ROOT' when instantiate this object
     */
    public function __construct($base_uri)
    {
    	$this->setBaseUri($base_uri);   
    }
    
    /**
     * Set the base of url request
     *
     * @param string $base_uri
     */
    public function setBaseUri($base_uri)
    {               
        if(empty($base_uri)) {
        	$this->base_uri = SVR_URL.$base_uri;
        }
        else {
        	//fix '/' missing at left and right of $base_uri
        	if(substr($base_uri, 0, 1) !== '/') $base_uri = '/'.$base_uri;
        	if(substr($base_uri, -1, 1) !== '/') $base_uri = $base_uri.'/';
        	$this->base_uri = $base_uri;
        }
    }

        
    /** 
	 * Retreive request param(s) from url and save them to $request 
	 * Work with/without rewrited url
	 */
	public function getRequestURI()
	{  
		//ensure that the router vars are empty
		$this->reset();
		
		//get server REQUEST_URI
	    $this->request_uri = str_ireplace($this->base_uri,'',$_SERVER['REQUEST_URI']);
	    
	    // if url is like index.php?key=val&key2... we use $_GET var instead
	    if(preg_match('#\.php\??#',$this->request_uri)) {
	    	
	    	// fixed: app default controller was called on url rewrited
	    	// with fake path and url ending by .php extension witch is not good.
	    	$request_uri = explode('?',$this->request_uri);
	    	$request_uri = $request_uri[0];
	    	if(strpos($request_uri, '/') !== false) throw new Peak_Exception('ERR_ROUTER_URI_NOT_FOUND');
	    	else {
	    		foreach($_GET as $k => $v) {
	    			$this->request[] = $k;
	    			if(strlen($v) != 0) $this->request[] = $v;
	    		}
	    	}
	    }
	    else {
	    	$this->request = explode('/',$this->request_uri);
	    	foreach($this->request as $key => $value) {
	    		if (strlen($value) == 0) unset($this->request[$key]);
	    	}

	    }
	    $this->resolveCtrlType();
	    $this->resolveRequest();
	}
	
	
	protected function resolveRequest()
	{
	    // extract data from request
	    if (!empty($this->request)) 
	    {	        
	        //preserve unparsed request
	        $request = $this->request;
	        
	        $this->controller = array_shift($request);
	        $this->action = array_shift($request);
	        $this->action = (empty($this->action)) ? '_index' : '_'.$this->action;
	        $this->params = $request;
	        $i = 0;
	        foreach($this->params as $k => $v) {
	        	if($i == 0) { $key = $v; ++$i; }
	        	else { $this->params_assoc[$key] = $v; $i = 0; }
	        }
	    }
	}
	
	/**
	 * @deprecated
	 *
	 */
	protected function resolveCtrlType()
	{
	    if((!empty($this->request[0])) && ($this->request[0] === $this->ctrls_type_base['modules'])) {  
	        $this->controller_type = 'module';
	        array_shift($this->request);
	    }
	    else $this->controller_type = 'controller';
	}

	/**
	 * Reset router vars
	 */
	public function reset()
	{
		$this->request = null;
		$this->controller = null;
		$this->controller_type = null;
		$this->action = null;
		$this->params = array();
		$this->params_assoc = array();
	}
	
}