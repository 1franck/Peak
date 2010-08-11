<?php
$file_to_test = realpath('./../library/Peak/Bootstrap.php');
echo 'Tested file: '.$file_to_test.'<br />';
include($file_to_test);

class TestOfBootstrap extends UnitTestCase
{
    
    function testOfInstanciate()
    {
        $this->bootstrap = new myboot();
        $this->assertTrue($this->bootstrap instanceof Peak_Bootstrap ,'bootstrap is not an object of Peak_Bootstrap');
        
        
    }
    
    function testOfEnv()
    {
    	$env = $this->bootstrap->getEnvironment();
    	$this->assertFalse($env,'$env should bet not set');
    	
    	
    }
      
  

}



class myboot extends Peak_Bootstrap 
{
	
	
}