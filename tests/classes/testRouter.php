<?php

$file_to_test = realpath('./../../library/Peak/Router.php');
include($file_to_test);
echo 'Tested file: '.$file_to_test.'<br />';

class TestOfRouter extends UnitTestCase
{
    
    function testOfInitRouter()
    {
        
    	global $curpath;
    	$server = $_SERVER['DOCUMENT_ROOT'];
    	$path = str_replace($server,'',$curpath);
        $router = new Peak_Router($path);
        
        
        $this->assertTrue(is_a($router,'Peak_Router') ,'$router is not an object of router class');
        
        $router->getRequestURI();
        
        $this->assertTrue(isset($router->request_uri),'getRequestUri() fail to set $this->request_uri');
        
        echo 'Base uri: '.$router->base_uri.'<br />
              Request uri: '.$router->request_uri.'<br />
              Type: '.$router->controller_type.'<br /><br />
              Controller: '.$router->controller.'<br />
              Action: '.$router->action.'<br />
              Params: '.print_r($router->params,true).'<br />';
        
        //print_r($router->request);
        
        /*
        $router = Router::getInstance(); // init router
        $router->addRule('/books/:id/:keyname',array('controller' => 'books', 'action' => 'view')); // add simple rule
        // add some more rules
        $router->init(); // execute router
        
        print_r($router);
        
        echo $router->getController();*/
        
     
        
        
    }
    
    

}