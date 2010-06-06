<?php

$file_to_test = realpath('./../../library/Peak/Router.php');
include($file_to_test);
echo 'Tested file: '.$file_to_test;

class TestOfRouter extends UnitTestCase
{
    
    function testOfInitRouter()
    {
        
        $router = new Peak_Router('peakdev/tests/router');
        
        
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



class Ro1uter {
  static protected $instance;
  static protected $controller;
  static protected $action;
  static protected $params;
  static protected $rules;
 
  public static function getInstance() {
    if (isset(self::$instance) and (self::$instance instanceof self)) {
      return self::$instance;
    } else {
      self::$instance = new self();
      return self::$instance;
    }
  }
 
  private static function &arrayClean($array) {
    foreach($array as $key => $value) {
      if (strlen($value) == 0) unset($array[$key]);
    }  
  }
 
  private static function ruleMatch($rule, $data) {    
    $ruleItems = explode('/',$rule); self::arrayClean($ruleItems);
    $dataItems = explode('/',$data); self::arrayClean($dataItems);
 
    if (count($ruleItems) == count($dataItems)) {
      $result = array();
 
      foreach($ruleItems as $ruleKey => $ruleValue) {
        if (preg_match('/^:[\w]{1,}$/',$ruleValue)) {
          $ruleValue = substr($ruleValue,1);
          $result[$ruleValue] = $dataItems[$ruleKey];
        }
        else {
          if (strcmp($ruleValue,$dataItems[$ruleKey]) != 0) {
            return false;
          }
        }
      }
 
      if (count($result) > 0) return $result;
      unset($result);
    }
    return false;
  }
 
  private static function defaultRoutes($url) {
    // process default routes
    $items = explode('/',$url);
 
    // remove empty blocks
    foreach($items as $key => $value) {
      if (strlen($value) == 0) unset($items[$key]);
    }
 
    // extract data
    if (count($items)) {
      self::$controller = array_shift($items);
      self::$action = array_shift($items);
      self::$params = $items;
    }
  }
 
  protected function __construct() {
    self::$rules = array();
  }
 
  public static function init() {
    $url = $_SERVER['REQUEST_URI'];
    $isCustom = false;
 
    if (count(self::$rules)) {
      foreach(self::$rules as $ruleKey => $ruleData) {
        $params = self::ruleMatch($ruleKey,$url);
        if ($params) {          
          self::$controller = $ruleData['controller'];
          self::$action = $ruleData['action'];
          self::$params = $params;
          $isCustom = true;
          break;
        }
      }
    }
 
    if (!$isCustom) self::defaultRoutes($url);
 
    if (!strlen(self::$controller)) self::$controller = 'home';
    if (!strlen(self::$action)) self::$action = 'index';
  }
 
  public static function addRule($rule, $target) {
    self::$rules[$rule] = $target;
  }
 
 
  public static function getController() { return self::$controller; }
  public static function getAction() { return self::$action; }
  public static function getParams() { return self::$params; }
  public static function getParam($id) { return self::$params[$id]; }
}