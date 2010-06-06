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
 * @author  Francois Lajoie
 * @version 20100602
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
        
    public $base_uri;         //default url relative root set with __construct()
    
    public $request_uri;      //$_SERVER['REQUEST_URI'] without base_uri.
    
    public $request;          //original unparsed request array
    
    public $controller;       //controller name
    
    public $controller_type;  //controller type
    
    public $action;           //requested action
    
    public $params;           //action param(s) array
    
    /**
     * Reserved first param keyword for designating the king of controllers we request
     * leaving empty keyword will make the controller type default
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
        //fix '/' missing at left and right of $base_uri
        if(substr($base_uri, 0, 1) !== '/') $base_uri = '/'.$base_uri;
        if(substr($base_uri, -1, 1) !== '/') $base_uri = $base_uri.'/';
        
        $this->base_uri = $base_uri;       
    }

        
    /** 
	 * Retreive request param(s) from url and save them to $request 
	 * Work with rewrited url
	 */
	public function getRequestURI()
	{  
	    $this->request_uri = str_replace($this->base_uri,'',$_SERVER['REQUEST_URI']);
	        
	    // if url is like index.php?key=val&key2... we use $_GET var instead
	    if(preg_match('#\.php\??#',$this->request_uri)) {
	        foreach($_GET as $k => $v) {
	            $this->request[] = $k;
	            if(strlen($v) != 0) $this->request[] = $v;
	        }
	        $this->resolveCtrlType();
	        $this->resolveRequest();
	        return;
	    }

	    $this->request = explode('/',$this->request_uri);
	    
	    //print_r($this->request);
	    	    
	    $this->resolveCtrlType();	
    
	    foreach($this->request as $key => $value) {
	        if (strlen($value) == 0) unset($this->request[$key]);
	    }	    
    
	    $this->resolveRequest();
	}
	
	
	protected function resolveRequest()
	{
	    // extract data from request
	    if (count($this->request)) {
	        //$string, 
	        //$this->controller = filter_var(urldecode(array_shift($this->request)), FILTER_SANITIZE_STRING,FILTER_FLAG_STRIP_HIGH);
	        $this->controller = array_shift($this->request);
	        $this->action = array_shift($this->request);
	        $this->action = (empty($this->action)) ? '_index' : '_'.$this->action;
	        $this->params = $this->request;
	    }
	}
	
	protected function resolveCtrlType()
	{
	    if((!empty($this->request[0])) && ($this->request[0] === $this->ctrls_type_base['modules'])) {  
	        $this->controller_type = 'module';
	        array_shift($this->request);
	    }
	    else $this->controller_type = 'controller';
	}	
	
}