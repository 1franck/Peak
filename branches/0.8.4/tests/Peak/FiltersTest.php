<?php
/**
 * Test Helper
 */
require_once dirname(__FILE__).'/../TestHelper.php';

/**
 * @see Peak_Filters
 */
require_once 'Peak/Filters.php';

/**
 * @category   Peak
 * @package    Peak_Filters
 * @subpackage UnitTests
 * @version    $Id$
 */
class Peak_FiltersTest extends PHPUnit_Framework_TestCase
{
	
    public function testLoadFiltersClass()
    {   	
    	$f = new ValidateClass(array());
    	
    	$this->assertInstanceOf('Peak_Filters', $f);
    	
    	$this->assertEmpty($f->getData());
    	
    	$sf = $f->getSanitizeFilters();
    	
    	$this->assertEmpty($sf);
    	
    	$vf = $f->getValidateFilters();
    	
    	$this->assertArrayHasKey('mynumber',$vf);
    	
    }
    
    // class FiltersClass
    public function testValidatePass1()
    {
    	$data_to_validate = array('mynumber' => 7);
    	
    	$f = new ValidateClass($data_to_validate);
    	
    	$result = $f->validate();
    	
    	$this->assertTrue($result);
    	
    	$data = $f->getData();
    }
    
    // class FiltersClass
    public function testValidateFail1()
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
    public function testSanitize1()
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

// example class that will be registered by Peak_Registry
class ValidateClass extends Peak_Filters
{
    public function setValidate()
    {
        return array(
        
        'mynumber' => array('filter' => FILTER_VALIDATE_INT,
                            'options' => array('min_range' => 1, 'max_range' => 10),
                            'error'   => 'mynumber should be an integer between 1 and 10')
        
        );
    }
}

class SanitizeClass extends Peak_Filters 
{
	public function setSanitize()
	{
		return array(
		  
		  'number' => FILTER_SANITIZE_NUMBER_INT,
		  'cap'    => array('filter'  => FILTER_CALLBACK,
		                    'options' => array($this, 'sanitizeCap'))
		
		);
	}
	
	public function sanitizeCap($value)
	{
		return strtoupper($value);
	}
}