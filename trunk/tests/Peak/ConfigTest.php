<?php
/**
 * Test Helper
 */
require_once dirname(__FILE__).'/../TestHelper.php';

/**
 * @see Peak_Config
 */
require_once 'Peak/Config.php';

/**
 * @category   Peak
 * @package    Peak_Config
 * @subpackage UnitTests
 * @version    $Id$
 */
class Peak_ConfigTest extends PHPUnit_Framework_TestCase
{
	
	function setUp()
	{		
		$this->peakconfig = new Peak_Config();
	}
		    
	function testCreateInstance()
	{
		$cf = new Peak_Config();		
		$this->assertType('Peak_Config', $cf);
		$this->assertObjectHasAttribute('_vars', $cf);
	}
	
	function testSetIssetUnset()
	{
		$this->peakconfig->myvar = 'value';		
		$this->assertTrue(isset($this->peakconfig->myvar));
		
		unset($this->peakconfig->myvar);		
		$this->assertFalse(isset($this->peakconfig->myvar));		
	}
	
	function testCount()
	{
		$this->assertTrue(count($this->peakconfig) == 0);		
		$this->peakconfig->myvar = 'value';
		$this->assertTrue(count($this->peakconfig) == 1);	
	}
	
	function testSetVars()
	{
		$array = array('myvar' => 'value', 'test', 'test2' => 'value2');
		$this->peakconfig->setVars($array);
		$this->assertTrue(isset($this->peakconfig->myvar));
	}
	
	function testIterator()
	{
		$this->peakconfig->setVars(array('myvar' => 'value', 'test', 'test2' => 'value2'));
		
		$count = 0;
		foreach($this->peakconfig as $key) ++$count;
		
		$this->assertTrue(count($this->peakconfig) == 3);
	}
	
	
  
}