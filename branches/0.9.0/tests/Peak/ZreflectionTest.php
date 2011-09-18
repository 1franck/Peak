<?php
/**
 * Test Helper
 */
require_once dirname(__FILE__).'/../TestHelper.php';

/**
 * Component(s)
 * @see Peak_Helpers, Peak_Exception
 */
require_once 'Peak/Zreflection.php';

/**
 * Fixture(s)
 */
require_once dirname(__FILE__).'/ZreflectionTest/class1.php';

/**
 * Include path
 */
set_include_path(implode(PATH_SEPARATOR,array(realpath(dirname(__FILE__).'/../../library/Peak/Vendors'),
						                      get_include_path())));

/**
 * Autoload for Zend
 */
spl_autoload_register('_autoloadZendVendor');

function _autoloadZendVendor($cn) {
    $file = realpath(dirname(__FILE__).'/../../library/Peak/Vendors').'/'.str_replace('_','/',$cn).'.php';
    if(!file_exists($file)) return false;
    include $file;
}

/**
 * @category   Peak
 * @package    Peak_Zreflection
 * @subpackage UnitTests
 * @version    $Id$
 */
class Peak_ZreflectionTest extends PHPUnit_Framework_TestCase
{
	
	function setUp()
	{
		$this->zref = new Peak_Zreflection();

	}

	function testLoadClass()
	{
		$this->zref->loadClass('class1', false);
		$this->assertTrue($this->zref->class instanceof Zend_Reflection_Class);
		$this->assertTrue($this->zref->class->getName() === 'class1');
	}
	
	function testgetClassDoc()
	{
		$this->zref->loadClass('class1', false);
		$this->assertTrue($this->zref->getClassDoc() === 'Example Class1');
		$this->assertTrue($this->zref->getClassDoc('short') === 'Example Class1');
		$long_desc = trim($this->zref->getClassDoc('long'));
		//file_put_contents('test.txt',(string)$long_desc);
		
		//$this->assertTrue(trim($this->zref->getClassDoc('long')) === 'Long description
	}

    function testgetClassDocTags()
	{
		$this->zref->loadClass('class1', false);
		$doctags = $this->zref->getClassDocTags();
		
		$this->assertTrue(is_array($doctags));
	    $this->assertTrue(count($doctags) == 2);

		$this->assertTrue($doctags[0]['name'] === 'author');
		$this->assertTrue($doctags[0]['description'] === 'FooBar');
	}
	
	function testgetClassDeclaration()
	{
		$this->zref->loadClass('class1', false);
		
		$declaration = $this->zref->getClassDeclaration();
		$this->assertTrue(is_array($declaration));
		//print_r($declaration);
	}

}