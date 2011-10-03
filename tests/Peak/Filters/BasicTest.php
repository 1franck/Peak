<?php
/**
 * Test Helper
 */
require_once dirname(__FILE__).'/../../TestHelper.php';

/**
 * Component(s)
 * @see Peak_Filters, Peak_Filters_Basic
 */
require_once 'Peak/Filters.php';
require_once 'Peak/Filters/Basic.php';

/**
 * Fixture(s)
 */
require_once dirname(__FILE__).'/BasicTest/ValidateClass.php';
require_once dirname(__FILE__).'/BasicTest/SanitizeClass.php';

/**
 * @category   Peak
 * @package    Peak_Filters_Basic
 * @subpackage UnitTests
 * @version    $Id$
 */
class Peak_FiltersBasicTest extends PHPUnit_Framework_TestCase
{
	
    function testLoadFiltersClass()
    {   	
    	$f = new ValidateClass(array());
    	
    	$this->assertInstanceOf('Peak_Filters_Basic', $f);
    	
    	$this->assertEmpty($f->getData());
    	
    	$sf = $f->getSanitizeFilters();
    	
    	$this->assertEmpty($sf);
    	
    	$vf = $f->getValidateFilters();
    	
    	$this->assertArrayHasKey('mynumber',$vf);
    	
    }
    
    // class FiltersClass
    function testValidatePass1()
    {
    	$data_to_validate = array('mynumber' => 7);
    	
    	$f = new ValidateClass($data_to_validate);
    	
    	$result = $f->validate();
    	
    	$this->assertTrue($result);
    	
    	$data = $f->getData();
    }
    
    // class FiltersClass
    function testValidateFail1()
    {
    	$data_to_validate = array('mynumber' => 13);
    	
    	$f = new ValidateClass($data_to_validate);
    	
    	$result = $f->validate();
    	
    	$this->assertFalse($result);
    	
    	$errors = $f->getErrors();
    	
    	$this->assertArrayHasKey('mynumber', $errors);
    	
    	$this->assertTrue($errors['mynumber'] === 'mynumber should be an integer between 1 and 10');
    }
    
    //class SanitizeClass
    function testSanitize1()
    {
    	$data_to_sanitize = array('number' => '324fthy', 'cap' => 'i am a title', 'removed' => '');
    	
    	$f = new SanitizeClass($data_to_sanitize);
    	
    	$data_sanitized = $f->sanitize();
    	
    	$this->assertArrayHasKey('number', $data_sanitized);
    	$this->assertArrayHasKey('cap', $data_sanitized);
    	$this->assertFalse(isset($data_sanitized['removed']));
    	
    	$this->assertTrue($data_sanitized['number'] == 324);
    	$this->assertTrue($data_sanitized['cap'] === 'I AM A TITLE');
    }
	  
}