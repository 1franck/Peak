<?php
/**
 * Test Helper
 */
require_once dirname(__FILE__).'/../TestHelper.php';

/**
 * Component(s)
 * @see Peak_Codegen
 */
require_once 'Peak/Codegen.php';

/**
 * Fixture(s)
 */
require_once dirname(__FILE__).'/CodegenTest/SimpleClass.php';

/**
 * @category   Peak
 * @package    Peak_Codegen
 * @subpackage UnitTests
 * @version    $Id$
 */
class Peak_CodegenTest extends PHPUnit_Framework_TestCase
{
	
	public function setUp()
	{
		$this->simpleClass = new SimpleClass();
	}
	
    public function testSimpleClass()
    {   	    	
    	$this->assertInstanceOf('Peak_Codegen', $this->simpleClass);
    	$this->assertInstanceOf('SimpleClass', $this->simpleClass);
    }
	
	public function testPreview()
	{
    	$content = $this->simpleClass->preview();
		
		$this->assertTrue($content === '<?php'."\n".'echo "hello!";');
	}
	
	public function testSave()
	{
		$filepath = dirname(__FILE__).'/CodegenTest/test.php';
		$result = $this->simpleClass->save($filepath);
		
		$this->assertTrue((bool)$result);
		$this->assertTrue(file_exists($filepath));
		
		$content = file_get_contents($filepath);
		$this->assertTrue($content === '<?php'."\n".'echo "hello!";');
		unlink($filepath);
	}
}

