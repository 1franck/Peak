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

    function testParse()
	{
		$ann = new Peak_Annotations();
		
		$ann->add('testa')
		    ->add('version');

		$m = $ann->parse(
'/**
 * @category   Peak
 * @package    Peak_Annotations
 * @subpackage UnitTests
 * @version    $Id$ This is a test
 * test        test "12345"
 * @testa      "124134234" "324234234" "This is a test" abcdef "fedcba"
 */'
		);
		
		$this->assertTrue(count($m) == 2);
	}
	
	public function testClass()
	{
		$ann = new Peak_Annotations('classA');

		$ann->add('annotationToGet')
		    ->add('foobar');
			
		$annotations = $ann->getFromClass();
		
		$this->assertTrue(count($annotations) == 3);
		
		//print_r($annotations);
		
		$annotations = $ann->getFromAllMethods();
		
		$this->assertTrue(count($annotations) == 1);
		
		//print_r($annotations);
		    
	}
}