<?php
/**
 * Test Helper
 */
require_once dirname(__FILE__).'/../TestHelper.php';

/**
 * @see Peak_Router, Peak_Exception
 */
require_once 'Peak/Router.php';
require_once 'Peak/Exception.php';

/**
 * @category   Peak
 * @package    Peak_Router
 * @subpackage UnitTests
 * @version    $Id$
 */
class Peak_RouterTest extends PHPUnit_Framework_TestCase
{
	
	function setUp()
	{
		$this->peakrouter = new Peak_Router('');
	}

	function testCreateInstance()
	{
		$rt = new Peak_Router('');		
		$this->assertType('Peak_Router', $rt); 
	}
	
	
	function testBaseUri()
	{
		//check $base_uri
		$this->assertTrue($this->peakrouter->base_uri === '/');
	}
	
	/**
     * @backupGlobals enabled
     */
	function testBasicGetRequestURI()
	{
		//fake $_SERVER['REQUEST_URI'];
		$_SERVER['REQUEST_URI'] = '/mycontroller/myaction/param1/param2';
		
		$this->peakrouter->getRequestURI();
		//print_r($this->peakrouter);
		
		//request uri , controller , action
		$this->assertTrue($this->peakrouter->request_uri === 'mycontroller/myaction/param1/param2');
		$this->assertTrue($this->peakrouter->controller === 'mycontroller');
		$this->assertTrue($this->peakrouter->action === 'myaction');
				
		//params
		$this->assertTrue(is_array($this->peakrouter->params));
		$this->assertTrue($this->peakrouter->params[0] === 'param1');
		$this->assertTrue($this->peakrouter->params[1] === 'param2');
		
		//params assoc
		$this->assertTrue(is_array($this->peakrouter->params_assoc));
		$this->assertArrayHasKey('param1', $this->peakrouter->params_assoc);
		
		//request
		$this->assertTrue(is_array($this->peakrouter->request));
		$this->assertTrue(count($this->peakrouter->request) == 4);		
	}
	
	function testResetRouter()
	{
		$this->peakrouter->reset();
		//print_r($this->peakrouter);
		
		//base uri , request uri , controller , action
		$this->assertTrue($this->peakrouter->base_uri === '/');
		$this->assertEmpty($this->peakrouter->request_uri);
		$this->assertEmpty($this->peakrouter->controller);
		$this->assertEmpty($this->peakrouter->action);
				
		//params
		$this->assertEmpty($this->peakrouter->params);
		
		//params assoc
		$this->assertEmpty($this->peakrouter->params_assoc);
		
		//request
		$this->assertEmpty($this->peakrouter->request);
	}
	
	/**
     * @backupGlobals enabled
     */
	function testBasicOldStyleGetRequestURI()
	{
		//fake $_SERVER['REQUEST_URI'];
		$_SERVER['REQUEST_URI'] = 'index.php?mycontroller=myaction&param1=param2';
		
		//fake $_GET
		$_GET = array('mycontroller' => 'myaction', 'param1' => 'param2');
		
		$this->peakrouter->getRequestURI();
		//print_r($this->peakrouter);
		
		//request uri , controller , action
		$this->assertTrue($this->peakrouter->request_uri === 'index.php?mycontroller=myaction&param1=param2');
		$this->assertTrue($this->peakrouter->controller === 'mycontroller');
		$this->assertTrue($this->peakrouter->action === 'myaction');
				
		//params
		$this->assertTrue(is_array($this->peakrouter->params));
		$this->assertTrue($this->peakrouter->params[0] === 'param1');
		$this->assertTrue($this->peakrouter->params[1] === 'param2');
		
		//params assoc
		$this->assertTrue(is_array($this->peakrouter->params_assoc));
		$this->assertArrayHasKey('param1', $this->peakrouter->params_assoc);
		
		//request
		$this->assertTrue(is_array($this->peakrouter->request));
		$this->assertTrue(count($this->peakrouter->request) == 4);
	}
	
	/**
	 * @expectedException Peak_Exception
	 */
	function testGetRequestURIException()
	{
		try {
			//fake $_SERVER['REQUEST_URI'];
		    $_SERVER['REQUEST_URI'] = '/mycontroller/index.php';	    
			$this->peakrouter->getRequestURI();
		}
		catch (InvalidArgumentException $expected) {
            return;
        }
        $this->fail('An expected exception has not been raised.');
        
        
        $this->peakrouter->reset();
        
        try {
			//fake $_SERVER['REQUEST_URI'];
		    $_SERVER['REQUEST_URI'] = '/index.php/mycontroller';	    
			$this->peakrouter->getRequestURI();
		}
		catch (InvalidArgumentException $expected) {
            return;
        }
        $this->fail('An expected exception has not been raised.');
	}
	    
    
}