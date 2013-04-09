<?php
/**
 * Test Helper
 */
require_once dirname(__FILE__).'/../TestHelper.php';

/**
 * Component(s)
 * @see Peak_Annotations
 */
require_once 'Peak/Annotations.php';

/**
 * Fixture(s)
 */
require_once dirname(__FILE__).'/AnnotationsTest/classA.php';

/**
 * @category   Peak
 * @package    Peak_Annotations
 * @subpackage UnitTests
 * @version    $Id$
 */
class Peak_AnnotationsTest extends PHPUnit_Framework_TestCase
{
	/**
	 * Test a standart docblock parse
	 */
    function testParse()
	{
		$ann = new Peak_Annotations();
		
		$ann->setTags(array('testa', 'version'));

		$m = $ann->parse(
'/**
 * @category   Peak
 * @package    Peak_Annotations
 * @subpackage UnitTests
 * @version    $Id$ "This is a test"
 * testa       test "12345"
 * @testa      "124134234" "324234234" "This is a test" abcdef "fedcba"
 */'
		);
		
		//check tags count
		$this->assertTrue(count($m) == 2);

		//check tags params count
		$this->assertTrue(count($m[0]['params']) == 8);
		$this->assertTrue(count($m[1]['params']) == 5);
	}
	

	/**
	 * Test method getFormClass() without tag to detect
	 */
	public function test_getFromClass()
	{
		$ann = new Peak_Annotations('classA');
			
		$annotations = $ann->getFromClass();
		
		$this->assertTrue(count($annotations) == 0);
	}

	/**
	 * Test method getFromAllMethods() without tag to detect
	 */
	public function test_getFromAllMethods()
	{
		$ann = new Peak_Annotations('classA');
			
		$annotations = $ann->getFromAllMethods();

		//should return 2 methods
		$this->assertTrue(count($annotations) == 2);

		foreach($annotations as $a) {
			//every method should have not tag
			$this->assertTrue(empty($a));
		}
	}

	/**
	 * Test method setTags()
	 */
	public function test_setTags()
	{
		$ann = new Peak_Annotations('classA');
			
		// on tag only string
		$ann->setTags('foobar');
		$annotations = $ann->getFromClass();
		$this->assertTrue(count($annotations) == 1);

		// on tag only string
		$ann->setTags('annotationToGet');
		$annotations = $ann->getFromClass();
		// should return 2 since "annotationToGet" is present twice
		$this->assertTrue(count($annotations) == 2);

		// on tag only string case sensitive. should do the same as "annotationToGet" 
		$ann->setTags('ANNOTATIONTOGET');
		$annotations = $ann->getFromClass();
		// should return 2 since parse regex is case non-sensitive
		$this->assertTrue(count($annotations) == 2);

		// empty string tag
		$ann->setTags('');
		$annotations = $ann->getFromClass();
		$this->assertTrue(count($annotations) == 0);

		// all tags with * token
		$ann->setTags('*');
		$annotations = $ann->getFromClass();
		$this->assertTrue(count($annotations) == 5);

		// not present tags
		$ann->setTags('foobar2');
		$annotations = $ann->getFromClass();
		$this->assertTrue(count($annotations) == 0);

		// multiple tags array
		$ann->setTags(array('annotationToGet', 'foobar'));
		$annotations = $ann->getFromClass();
		// should return 3 since "annotationToGet" is present twice
		$this->assertTrue(count($annotations) == 3);

		// multiple tags array
		$ann->setTags(array('annotationToGet', 'barfoo'));
		$annotations = $ann->getFromClass();
		// should return 2 since "annotationToGet" is present twice and barfoo don't exists
		$this->assertTrue(count($annotations) == 2);

		// empty tags array
		$ann->setTags(array());
		$annotations = $ann->getFromClass();
		$this->assertTrue(count($annotations) == 0);
	}


	/**
	 * Test method getFromMethod() without tag to detect
	 */
	public function test_getFromMethod()
	{
		$ann = new Peak_Annotations('classA');
			
		//no tags
		$annotations = $ann->getFromMethod('funcA');
		//should return 0 methods since no tags have been set
		$this->assertTrue(count($annotations) == 0);

		//one tag
		$ann->setTags('annotationToGet');
		$annotations = $ann->getFromMethod('funcA');
		// should return 2 since "annotationToGet" is present twice
		$this->assertTrue(count($annotations) == 2);

		//one tag
		$ann->setTags('barfoo');
		$annotations = $ann->getFromMethod('funcA');
		// should return 0
		$this->assertTrue(count($annotations) == 0);

		//unknow method
		$ann->setTags('barfoo');
		$annotations = $ann->getFromMethod('funcZ');
		// should return 0
		$this->assertTrue(count($annotations) == 0);

	}

}