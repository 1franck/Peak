<?php

/**
 * Simple router URL php rewriting.
 * 
 * @desc     Support regular expression and url like : 
 *            http://example.com/[application php file]?[controller]=[action]&[param1]=[param2]&[...]
 *            http://example.com/index.php?server=php&view=ver  
 *            AND
 *            http://example.com/[controller]/[action]/[param1]/[param2]/[...]
 *            http://example.com/server/php/view/version
 *           
 * 
 * @author   Francois Lajoie
 * @version  $Id$     
 */
class Peak_Router
{
        
	/**
	 * default url relative root set with __construct()
	 * @var string
	 */
    public $base_uri;
    
    /**
     * $_SERVER['REQUEST_URI'] without base_uri.
     * @var string
     */
    public $request_uri;
    
    /**
     * Original unparsed request array
     * @var array
     */
    public $request;
    
    /**
     * Controller name
     * @var string
     */
    public $controller;
    
    /**
     * Requested action
     * @var string
     */
    public $action;

    /**
     * action param(s) array
     * @var array
     */
    public $params = array();

    /**
     * Actions param(s) associative array
     * @var array
     */
    public $params_assoc = array();

    /**
	 * Regex route
	 * @var array
	 */
	protected $_regex = array();


    /**
     * Set base url of your application index.php
     * 
     * @example your application script page url is http://example.com/myapp/index.php
     *          so $base_uri would be : '/myapp/'
     * @param   string $base_uri - Its recommeneded that you use constant 'ROOT' when instantiate this object
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
	    //if its rewrited url
	    else {
	    	//check for regex
	    	if($this->matchRegex()) return;
	    	
	    	$this->request = explode('/',$this->request_uri);
	    	foreach($this->request as $key => $value) {
	    		if (strlen($value) == 0) unset($this->request[$key]);
	    	}

	    }
	    $this->resolveRequest();
	}
	
	/**
	 * Resolve $request
	 */
	protected function resolveRequest()
	{
	    // extract data from request
	    if (!empty($this->request)) 
	    {	        
	        //preserve unparsed request
	        $request = $this->request;
	        
	        $this->controller = array_shift($request);
	        $this->action = array_shift($request);
	        $this->action = (empty($this->action)) ? '' : '_'.$this->action;
	        $this->params = $request;
	        $this->paramsToAssoc();
	    }
	}
	
	/**
	 * Reset router vars
	 */
	public function reset()
	{
		$this->request = null;
		$this->controller = null;
		$this->action = null;
		$this->params = array();
		$this->params_assoc = array();
	}
	
	/**
	 * Set manually a request and resolve it
	 *
	 * @param array $request
	 */
	public function setRequest($request)
	{
	    $this->request = $request;
	    $this->resolveRequest();
	}
	
	/**
	 * Transform params array to params associate array
	 * To work, we need a pair number of params to transform it to key/val array
	 */
	protected function paramsToAssoc()
	{
		$i = 0;
		foreach($this->params as $k => $v) {
			if($i == 0) { $key = $v; ++$i; }
			else { $this->params_assoc[$key] = $v; $i = 0; }
		}
	}
	
	/**
	 * Add a regex route
	 *
	 * @param string $regex
	 * @param array  $route
	 */
	public function addRegex($regex, $route)
	{
		$this->_regex[$regex] = $route;
	}
	
	
	/**
	 * Try to match request uri to a regex
	 *
	 * @return bool
	 */
	public function matchRegex()
	{
		//we got regex
		if(!empty($this->_regex)) {
			
			//check all regex for a match. First in, first out here
			foreach($this->_regex as $regex => $route) {

				$result = preg_match('#'.$regex.'#', $this->request_uri, $matches);

				//we got a positive preg_match
				if($result) {
					//if url match a regexp but end up with additionnal data, the url should be not valid otherwise 
					//we will have url that can ends with anything and still be valid for the application and google, wich its bad
					if($this->request_uri === $matches[0]) {
						$this->controller = $route['controller'];
						$this->action = $route['action'];
						$this->params = array_slice($matches,1);
						$this->paramsToAssoc();
						return true;
						break;
					}
				}
			}
		}
		return false;
	}

	/**
	 * Delete a specific regex or all regex
	 *
	 * @param string $regex
	 */
	public function deleteRegex($regex = null)
	{
		if(isset($regex)) unset($this->_regex[$regex]);
		else $this->_regex = array();
	}
}