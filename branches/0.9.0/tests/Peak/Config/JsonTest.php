<?php
/**
 * Test Helper
 */
require_once dirname(__FILE__).'/../../TestHelper.php';

/**
 * Component(s)
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
		$this->assertInstanceOf('Peak_Config_Json', $cf);
		$this->assertObjectHasAttribute('_vars', $cf);
	}
	
	function testCreateInstanceWithFile()
	{	
	    //with sections
		$cf = new Peak_Config_Json(dirname(__FILE__).'/JsonTest/app.json');
		
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
			$this->peakconfig->loadFile(dirname(__FILE__).'/JsonTest/app2.json');
		}
		catch (InvalidArgumentException $expected) {
            return;
        }
 
        $this->fail('An expected exception has not been raised.');
        
 	}
 	
 	function testFor5_3()
 	{
 	    if((version_compare(PHP_VERSION, '5.3.0') >= 0)) {
 	        $this->LoadFileException2forPHP_5_3();
 	    }
 	}

	/**
	 * @expectedException Peak_Exception
	 */
	function LoadFileException2forPHP_5_3()
	{

		//PHP 5 >= 5.3
		if(function_exists('json_last_error')) {
			//try to load a file with syntax error(s)
			try {
				$this->peakconfig->loadFile(dirname(__FILE__).'/JsonTest/appwitherror.json', true);
			}
			catch (InvalidArgumentException $expected) {
				return;

			}

			$this->fail('An expected exception has not been raised.');
		}
	}

}