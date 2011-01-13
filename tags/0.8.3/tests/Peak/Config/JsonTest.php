<?php
/**
 * Test Helper
 */
require_once dirname(__FILE__).'/../../TestHelper.php';

/**
 * @see Peak_Config, Peak_Config_Json, Peak_Exception
 */
require_once 'Peak/Config.php';
require_once 'Peak/Config/Json.php';
require_once 'Peak/Exception.php';

/**
 * @category   Peak
 * @package    Peak_Config_Json
 * @subpackage UnitTests
 * @version    $Id$
 */
class Peak_ConfigJsonTest extends PHPUnit_Framework_TestCase
{
	
	function setUp()
	{		
		$this->peakconfig = new Peak_Config_Json();
	}
		    
	function testCreateInstance()
	{
		$cf = new Peak_Config_Json();		
		$this->assertType('Peak_Config_Json', $cf);
		$this->assertObjectHasAttribute('_vars', $cf);
	}
	
	function testCreateInstanceWithFile()
	{	
	    //with sections
		$cf = new Peak_Config_Json(TESTS_PATH.'/tmp/app.json');
		
		$this->assertTrue(isset($cf->all));
		$this->assertTrue(is_array($cf->all));
		$this->assertTrue(is_array($cf->development));
		$this->assertTrue(is_array($cf->testing));
		$this->assertTrue(is_array($cf->staging));
		$this->assertTrue(is_array($cf->production));
		
	}
	
	/**
	 * @expectedException Peak_Exception
	 */
	function testLoadFileException()
	{
		//try to load a file that don't exists
		try {
			$this->peakconfig->loadFile(TESTS_PATH.'/tmp/app2.json');
		}
		catch (InvalidArgumentException $expected) {
            return;
        }
 
        $this->fail('An expected exception has not been raised.');
        
 	}

	/**
	 * @expectedException Peak_Exception
	 */
	function testLoadFileException2()
	{

		//PHP 5 >= 5.3
		if(function_exists('json_last_error')) {
			//try to load a file with syntax error(s)
			try {
				$this->peakconfig->loadFile(TESTS_PATH.'/tmp/appwitherror.json', true);
			}
			catch (InvalidArgumentException $expected) {
				return;

			}

			$this->fail('An expected exception has not been raised.');
		}
	}
	
  
}