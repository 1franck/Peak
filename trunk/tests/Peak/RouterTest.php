<?php
/**
 * Test Helper
 */
require_once dirname(__FILE__).'/../TestHelper.php';

/**
 * @see Peak_Router
 */
require_once 'Peak/Router.php';

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
	function testGetRequestURI()
	{
		//fake $_SERVER['REQUEST_URI'];
		$_SERVER['REQUEST_URI'] = '/controller/action/param1/param2';
		
		$this->peakrouter->getRequestURI();
		//print_r($this->peakrouter);
		
		$this->assertTrue($this->peakrouter->request_uri === 'controller/action/param1/param2');
		//$this->assertTrue($this->peakrouter->controller === 'test');
		//$this->assertTrue($this->peakrouter->request_uri === 'test');
		//$this->assertTrue($this->peakrouter->request_uri === 'test');	
	}
	    
    
}