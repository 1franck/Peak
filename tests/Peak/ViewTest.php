<?php
/**
 * Test Helper
 */
require_once dirname(__FILE__).'/../TestHelper.php';

/**
 * @see Peak_View, Peak_Exception
 */
require_once 'Peak/View.php';
require_once 'Peak/Exception.php';

/**
 * @category   Peak
 * @package    Peak_View
 * @subpackage UnitTests
 * @version    $Id$
 */
class Peak_ViewTest extends PHPUnit_Framework_TestCase
{
	
	function setUp()
	{
		$this->peakview = new Peak_View();
	}
	
	function testCreateInstance()
	{
		$view = new Peak_View();		
		$this->assertType('Peak_View', $view); 
	}
	
	function testCreateInstanceWithArray()
	{
		$view = new Peak_View(array('test' => 'value'));		
		$this->assertType('Peak_View', $view);
		
		$vars = $view->getVars();		
		$this->assertArrayHasKey('test', $vars);
	}
		
	function testManipulateVars()
	{
		//__isset
		$this->assertFalse(isset($this->peakview->unknowvar));
		
		//__set, __isset, __get
		$this->peakview->test = 'value';		
		$this->assertTrue(isset($this->peakview->test));
		$this->assertTrue($this->peakview->test === 'value');
				
		//__unset, __isset
		unset($this->peakview->test);
		$this->assertFalse(isset($this->peakview->test));
		
		//set
		$this->peakview->set('test', 'value');		
		$this->assertTrue(isset($this->peakview->test));
		$this->assertTrue($this->peakview->test === 'value');
		unset($this->peakview->test);
		
	}
	
	function testCountVars()
	{
		$this->assertTrue($this->peakview->countVars() == 0);
		
		$this->peakview->test = 'value';
		$this->assertTrue($this->peakview->countVars() == 1);
		
		unset($this->peakview->test);
	}
	
	function testGetVars()
	{
		$this->assertTrue(is_array($this->peakview->getVars()));
	}
	
	function testResetVars()
	{
		//set a variable
		$this->peakview->test = 'value';
		
		//reset vars
		$this->peakview->resetVars();
		
		//check is vars array is empty
		$vars = $this->peakview->getVars();
		$this->assertTrue(empty($vars));
	}
	
	/**
	 * @expectedException Peak_Exception
	 */
	function testRenderException()
	{
		//try render a script file when no rendering engine have been set before
		try {
			$this->peakview->render('test','test');
		}
		catch (InvalidArgumentException $expected) {
            return;
        }
 
        $this->fail('An expected exception has not been raised.');
	}
	
}