<?php
/**
 * Test Helper
 */
require_once dirname(__FILE__).'/../TestHelper.php';

/**
 * Component(s)
 * @see Peak_Filters
 */
require_once 'Peak/Config.php';
require_once 'Peak/Core.php';
require_once 'Peak/Registry.php';
require_once 'Peak/autoload.php';

/**
 * Fixture(s)
 */

/**
 * @category   Peak
 * @package    autoload.php
 * @subpackage UnitTests
 * @version    $Id$
 */
class Peak_AutoloadTest extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		if(!defined('LIBRARY_ABSPATH')) {
			define('LIBRARY_ABSPATH', str_replace('\\','/',realpath(dirname(__FILE__).'/../../library')));
		}
		if(!defined('APPLICATION_ABSPATH')) {
			define('APPLICATION_ABSPATH', str_replace('\\','/',dirname(__FILE__).'/AutoloadTest'));
			
		}
		//emule core&configs paths
		Peak_Registry::getInstance();
		Peak_Core::getInstance();
		$config = new Peak_Config(array('path' => Peak_Core::getDefaultAppPaths(APPLICATION_ABSPATH)));
		Peak_Registry::set('config', $config);
	}
	
	public function testLibraryPath()
	{
		$this->assertTrue(class_exists('Peak_Application') === true);
	}
	
	public function testVendorsPath()
	{
		$this->assertTrue(class_exists('Zend_Exception') === true);
	}
	
	public function testControllersPath()
	{
		$this->assertTrue(class_exists('indexController') === true);
	}
	
	public function testAppPath()
	{
		$this->assertTrue(class_exists('App_Misc_Test') === true);
	}
}