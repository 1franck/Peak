<?php
/**
 * Test Helper
 */
require_once dirname(__FILE__).'/../../TestHelper.php';

/**
 * Component(s)
 * @see Peak_Bootstrap, Peak_Core
 */
require_once 'Peak/Application/Bootstrap.php';
require_once 'Peak/Core.php';

/**
 * Fixture(s)
 */
require_once dirname(__FILE__).'/BootstrapTest/Bootstrap.php';

/**
 * @category   Peak
 * @package    Peak_Bootstrap
 * @subpackage UnitTests
 * @version    $Id$
 */
class Peak_BootstrapTest extends PHPUnit_Framework_TestCase
{
	
	function testBootstrapInstance()
	{
		$boot = new Bootstrap();
		$this->assertTrue($boot->i == 2);
	}
}