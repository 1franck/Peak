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
		$this->peakrouter = new Peak_Router();
	}

	function testCreateInstance()
	{
		$rt = new Peak_Router();
		
		$this->assertType('Peak_Registry', $reg); 
	}
	
	    
    
}