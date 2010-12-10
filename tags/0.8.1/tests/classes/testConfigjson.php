<?php
include(realpath('./../library/Peak/Config.php'));
include(realpath('./../library/Peak/Exception.php'));

$file_to_test = realpath('./../library/Peak/Config/Json.php');
echo 'Tested file: '.$file_to_test.'<br />';
include($file_to_test);

class TestOfConfig extends UnitTestCase
{
    
    function testOfInstanciate()
    {
       $this->config = new Peak_Config_Json();
       $this->assertTrue($this->config instanceof Peak_Config_Json ,'config is not an object of Peak_Config_Json');    
       
       /*echo json_encode(array(0 => array('id' => 10, 'FIRST' => 'Jack', 'LAST' => 'Smith'),
                         1 => array('id' => 15, 'FIRST' => 'Mary', 'LAST' => 'Black'),
                         2 => array('id' => 42, 'FIRST' => 'John', 'LAST' => 'Green'))); exit();*/
    }
    
    function testOfLoadJson()
    {
    	$data = $this->config->loadFile('./temps/json.txt');   	
    	$this->assertTrue(is_array($data) ,'loadFile(\'./temps/json.txt\') fail to return an array');
    	//$this->assertTrue(count($this->config) == 11, 'count configs without sections should return 11');
    	   	
    	
    }
  
}