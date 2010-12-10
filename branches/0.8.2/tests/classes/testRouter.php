<?php

$file_to_test = realpath('./../../library/Peak/Router.php');
include($file_to_test);
include(realpath('./../../library/Peak/Exception.php'));
echo 'Tested file: '.$file_to_test.'<br />';

class TestOfRouter extends UnitTestCase
{
    
	    
    function print_router()
    {
    	 echo '<br />Base uri: '.$this->router->base_uri.'<br />
               Request uri: '.$this->router->request_uri.'<br />
               Original Request: '.print_r($this->router->request,true).'<br /><br />
               Controller: '.$this->router->controller.'<br />
               Action: '.$this->router->action.'<br />
               Params: '.print_r($this->router->params,true).'<br />
               Params assoc: '.print_r($this->router->params_assoc,true).'<hr />';
    }
    
    
    function testOfInitRouter()
    {
        define('SVR_URL','http://127.0.0.1');
    	global $curpath;
    	$server = $_SERVER['DOCUMENT_ROOT'];
    	$path = str_replace(SVR_URL,'',$curpath);
        $this->router = new Peak_Router('/peakframework/tests/router');       
        
        $this->assertTrue(is_a($this->router,'Peak_Router') ,'$router is not an object of router class');
        
        $this->router->getRequestURI();
        
        $this->assertTrue(isset($this->router->request_uri),'getRequestUri() fail to set $this->request_uri');
        
       
        
        //print_r($router->request);
        
        /*
        $router = Router::getInstance(); // init router
        $router->addRule('/books/:id/:keyname',array('controller' => 'books', 'action' => 'view')); // add simple rule
        // add some more rules
        $router->init(); // execute router
        
        print_r($router);
        
        echo $router->getController();*/
        
     
        $this->print_router();
        
    }
    
    function testOfRegexRoute()
    {    	
    	$this->router->addRegex('teams/(\w+)/(\d+)', array('controller' => 'myteam', 'action' => 'index'));
    	
    	$this->router->addRegex('news/(\d+)', array('controller' => 'news', 'action' => 'index'));
    	
    	$this->router->getRequestURI();
    	                
    	echo 'after REGEX if apply<br />
    	      2 regex available: <br />
    	      First: teams/(\w+)/(\d+)<br />
    	      Second: news/(\d+)<br /><br />';   
        $this->print_router();    
    }

    
    

}