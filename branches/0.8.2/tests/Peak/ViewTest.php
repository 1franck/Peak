<?php
/**
 * Test Helper
 */
require_once dirname(__FILE__).'/../TestHelper.php';

/**
 * @see Peak_View
 */
require_once 'Peak/View.php';

/**
 * @category   Peak
 * @package    Peak_View
 * @subpackage UnitTests
 * @version    $Id$
 */
class Peak_ViewTest extends PHPUnit_Framework_TestCase
{
	
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
	
	
}