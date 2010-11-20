<?php
include(realpath('./../library/Peak/Config.php'));
include(realpath('./../library/Peak/Exception.php'));

$file_to_test = realpath('./../library/Peak/Config/Ini.php');
echo 'Tested file: '.$file_to_test.'<br />';
include($file_to_test);

class TestOfConfig extends UnitTestCase
{
    
    function testOfInstanciate()
    {
       $this->config = new Peak_Config_Ini();
       $this->assertTrue($this->config instanceof Peak_Config_Ini ,'config is not an object of Peak_Config_Ini');    
    }
    
    function testOfLoadIni()
    {
    	$data = $this->config->loadFile('./temps/configs.ini');   	
    	$this->assertTrue(is_array($data) ,'loadFile(\'./temps/configs.ini\') fail to return an array');
    	$this->assertTrue(count($this->config) == 11, 'count configs without sections should return 11');
    	   	
    	//echo '<pre>'.print_r($data,true);
    	
    	$data = $this->config->loadFile('./temps/configs.ini', true, 'production');   	
    	$this->assertTrue(is_array($data) ,'loadFile(\'./temps/configs.ini\') fail to return an array');
    	$this->assertTrue(count($this->config) == 4, 'count configs with sections should return 4');
    	
    	echo '<pre>'.print_r($data,true);
    	
    	//should throw an exception
    	//$data2 = $this->config->loadFile('./temps/unknowconfigs.ini');
    	
    	//should throw an exception
    	//$data2 = $this->config->loadFile('./temps/configs.ini', true, 'unknowsection');
    	
    }
    
    /**
     * FOR PHP 5 >= 5.3.0
     */
    /*
    function testOfLoadIniString()
    {
    	$data = $this->config->loadString('[development]

project_name  = "PeakDemo"
project_descr = "Peak Demo Description"                          
svr_url = "http://127.0.0.1"
application_root = "peakapp/application/demo"
library_root     = "peakframework/library"
root             = "peakapp/public_html/"
zend_lib_root    = "framework/ZendFramework-1.10.8-minimal/library"
app_default_ctrl = "index"
dev_mode = true
enable_peak_controllers = false
db.host      = "localhost"
db.username  = "peakapp"
db.password  = "peakpass"
db.dbname    = "peakdemo"

[testing]
db.host      = "localhost"
db.username  = "app"
db.password  = "pass"
db.dbname    = "demo"

[staging]
db.host      = "localhost"
db.username  = "app"
db.password  = "pass"
db.dbname    = "demo"
[production]
db.host      = "localhost"
db.username  = "admin"
db.password  = "secretpassword"
db.dbname    = "site_demo"',true);   	
    	
    	$this->assertTrue(is_array($data) ,'loadString(\'...\') fail to return an array');
    	echo '<pre>'.print_r($data,true);
    }
  */
}