<?php
/**
 * Test Helper
 */
require_once dirname(__FILE__).'/../TestHelper.php';

/**
 * @see Peak_View, Peak_View_Render, Peak_View_Render_Layouts, Peak_Exception, Peak_Registry, Peak_Config
 */
require_once 'Peak/View.php';
require_once 'Peak/View/Render.php';
require_once 'Peak/View/Render/Layouts.php';
require_once 'Peak/Exception.php';
require_once 'Peak/Registry.php';
require_once 'Peak/Config.php';
require_once 'Peak/Config/Ini.php';


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
	
	function tearDown()
	{
		unset($this->peakview);
	}
	
	function testCreateInstance()
	{
		$view = new Peak_View();		
		$this->assertInstanceOf('Peak_View', $view); 
	}
	
	function testCreateInstanceWithArray()
	{
		$view = new Peak_View(array('test' => 'value'));		
		$this->assertInstanceOf('Peak_View', $view);
		
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

	function testSetVars()
	{
		$this->assertTrue($this->peakview->countVars() == 0);

		$this->peakview->setVars(array('test' => 'test1', 'name' => 'john'));

		$this->assertTrue($this->peakview->countVars() == 2);
	}

	function testAddVars()
	{
		$this->assertTrue($this->peakview->countVars() == 0);

		$this->peakview->addVars(array('test' => 'test1', 'name' => 'john'));

		$this->assertTrue($this->peakview->countVars() == 2);

		$this->peakview->addVars(array('test2' => 'test1', 'name' => 'john'));

		$this->assertTrue($this->peakview->countVars() == 3);
	}

	function testSetEngine()
	{
		$name = $this->peakview->getEngineName();

		$this->assertTrue($name === null);

		$this->peakview->engine('layouts');

		$name = $this->peakview->getEngineName();

		$this->assertTrue($name === 'layouts');
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
	
	function testIniVar()
	{
		$this->peakview->iniVar('viewvars.ini', dirname(__FILE__).'/../tmp/');
				
		$this->assertTrue(isset($this->peakview->name));
		
		$this->assertTrue(isset($this->peakview->hobby));
				
		$this->assertTrue(isset($this->peakview->city));
	}
	
	function testRegistryConfig()
	{
		$config = new Peak_Config();
		
		$config->view = array('set' => array('test' => 'value',
		                                     'test2' => 'value2'),
		                      'engine' => 'Layouts');
		                                     
	    Peak_Registry::set('config', $config);
	    $view = new Peak_View();
	    
	    $this->assertTrue(isset($view->test));
	    $this->assertTrue(isset($view->test2));
	    $this->assertFalse(isset($view->test3));
	    
	    $this->assertType('Peak_View_Render_Layouts', $view->engine());
	}
	
}