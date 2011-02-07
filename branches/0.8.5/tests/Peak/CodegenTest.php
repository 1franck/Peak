<?php
/**
 * Test Helper
 */
require_once dirname(__FILE__).'/../TestHelper.php';

/**
 * @see Peak_Codegen
 */
require_once 'Peak/Codegen.php';

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

class SimpleClass extends Peak_Codegen
{
    
    public function generate()
    {
        return 'echo "HI!";';
    }
	
}