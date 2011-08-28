<?php
/**
 * Test Helper
 */
require_once dirname(__FILE__).'/../../TestHelper.php';

/**
 * Component(s)
 * @see Peak_Config, Peak_Config_Ini, Peak_Exception
 */
require_once 'Peak/Config.php';
require_once 'Peak/Config/Ini.php';
require_once 'Peak/Exception.php';

/**
 * @category   Peak
 * @package    Peak_Config_Ini
 * @subpackage UnitTests
 * @version    $Id$
 */
class Peak_ConfigIniTest extends PHPUnit_Framework_TestCase
{
	
	function setUp()
	{		
		$this->peakconfig = new Peak_Config_Ini();
	}
		    
	function testCreateInstance()
	{
		$cf = new Peak_Config_Ini();		
		$this->assertInstanceOf('Peak_Config_Ini', $cf);
		$this->assertObjectHasAttribute('_vars', $cf);
	}
	
	function testCreateInstanceWithFile()
	{	
	    //with sections
		$cf = new Peak_Config_Ini(TESTS_PATH.'/tmp/app.ini', true);
		
		$this->assertTrue(isset($cf->all));
		$this->assertTrue(is_array($cf->all));
		$this->assertTrue(is_array($cf->development));
		$this->assertTrue(is_array($cf->testing));
		$this->assertTrue(is_array($cf->staging));
		$this->assertTrue(is_array($cf->production));
		
		//whitout sections
		$cf = new Peak_Config_Ini(dirname(__FILE__).'/IniTest/app.ini', false);
		
		$this->assertFalse(isset($cf->all));
		$this->assertTrue(isset($cf->php));
		
		//with a specific section
		$cf = new Peak_Config_Ini(dirname(__FILE__).'/IniTest/app.ini', true,'development');

		$this->assertFalse(isset($cf->all));
		$this->assertTrue(isset($cf->front));
		$this->assertTrue(isset($cf->db));	
	}
	
	/**
	 * @expectedException Peak_Exception
	 */
	function testLoadFileException()
	{
		//try to load a file that don't exists
		try {
			$this->peakconfig->loadFile('foo.ini');
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

        //try to load a file with syntax error(s)
        try {
			$this->peakconfig->loadFile(dirname(__FILE__).'/IniTest/appwitherror.ini', true);
		}
		catch (InvalidArgumentException $expected) {
            return;
            
        }
        
		$this->fail('An expected exception has not been raised.');
	}
	
  
}