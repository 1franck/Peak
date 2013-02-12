<?php
/**
 * Test Helper
 */
require_once dirname(__FILE__).'/../TestHelper.php';

/**
 * Component(s)
 * @see Peak_Helpers, Peak_Exception
 */
require_once 'Peak/Helpers.php';
require_once 'Peak/Exception.php';

/**
 * Fixture(s)
 */
require_once dirname(__FILE__).'/HelpersTest/MyHelpers.php';

/**
 * @category   Peak
 * @package    Peak_Helpers
 * @subpackage UnitTests
 * @version    $Id$
 */
class Peak_HelpersTest extends PHPUnit_Framework_TestCase
{
	
	function setUp()
	{
		$this->peakhelpers = new myHelpers();
	}

	function testCreateHelpersClass()
	{		
		$helper = new MyHelpers();		
		$this->assertInstanceOf('Peak_Helpers', $helper);		
	}
	
	function testLoadHelpers()
	{		
		$this->peakhelpers->test;		
		$txt = $this->peakhelpers->test->getHello();	
		$this->assertTrue($txt === 'Hello');
		
		$this->peakhelpers->misc;		
		$txt = $this->peakhelpers->misc->getHello();	
		$this->assertTrue($txt === 'Hello');
	}
	
	function testHelpersIssetUnset()
	{
		$this->peakhelpers->test;
		$this->assertTrue(isset($this->peakhelpers->test));
		$this->assertFalse(isset($this->peakhelpers->misc));
		
		unset($this->peakhelpers->test);
		$this->assertFalse(isset($this->peakhelpers->test)); 
	}
	
	function testHelpersExists()
	{
		$result = $this->peakhelpers->exists('misc');
		$this->assertTrue($result);
		$result = $this->peakhelpers->exists('html');
		$this->assertFalse($result);
	}
	
	/**
	 * @expectedException Peak_Exception
	 */
	function testException()
	{
		//try to load an unknow helpers
		try {
			$this->peakhelpers->unknowhelper;
		}
		catch (InvalidArgumentException $expected) {
            return;
        }
        $this->fail('An expected exception has not been raised.');
	}

}