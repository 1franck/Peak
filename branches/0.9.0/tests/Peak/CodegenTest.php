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
	
    public function testSimpleClass()
    {   	
    	$cg = new SimpleClass();
    	
    	$this->assertInstanceOf('Peak_Codegen', $cg);
    	$this->assertInstanceOf('SimpleClass', $cg);
    	
    	
    }
    	  
}

