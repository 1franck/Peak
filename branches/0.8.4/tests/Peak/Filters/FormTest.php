<?php
/**
 * Test Helper
 */
require_once dirname(__FILE__).'/../../TestHelper.php';

/**
 * @see Peak_Filters, Peak_Filters_Advanced, Peak_Filters_Form, Peak_Exception
 */
require_once 'Peak/Filters.php';
require_once 'Peak/Filters/Advanced.php';
require_once 'Peak/Filters/Form.php';
require_once 'Peak/Exception.php';

/**
 * @category   Peak
 * @package    Peak_Filters_Form
 * @subpackage UnitTests
 * @version    $Id$
 */
class Peak_FiltersFormTest extends PHPUnit_Framework_TestCase
{
	
    function testLoadFiltersClass()
    {   	
    	$f = new Form1();
    	
    	$this->assertInstanceOf('Peak_Filters_Advanced', $f);
    	$this->assertInstanceOf('Peak_Filters_Form', $f);
    	
    	$this->assertEmpty($f->getData());
    	
    	$sf = $f->getSanitizeFilters();
    	
    	$this->assertEmpty($sf);
    	
    	$vf = $f->getValidateFilters();
    	
    	$this->assertArrayHasKey('name',$vf);
    	
    }
    
    function testForm1ValidatePass1()
    {	
    	$_POST = array('namef' => 'mrjohn1', 'email' => 'mrjohn1@hotmail.com', 'password' => 'mypass2', 'repassword' => 'mypass2', 'unknow' => '123abc');
    	
    	$f = new Form1();
    	
     	if($f->validate() === false) $result = false;
    	else $result = true;
    	
    	$this->assertTrue($result);
    }
    
    
    function testForm1ValidateFail1()
    {
    	$_POST = array('name' => 'mrjohn1%', 'email' => 'mrjohn1hotmail.com', 'password' => 'mypass2', 'repassword' => 'mypassss2', 'unknow' => '123abc');
    	
    	$f = new Form1();
    	
     	if($f->validate() === false) $result = false;
    	else $result = true;
    	
    	$this->assertFalse($result);
    	
    	$errors = $f->getErrors();
    	    	
    	$this->assertArrayHasKey('name', $errors);
    	$this->assertTrue($errors['name'] === 'Name contains invalid chars');
    	
    	$this->assertArrayHasKey('email', $errors);
    	$this->assertTrue($errors['email'] === 'Email not valid');
    	    	
    	$this->assertArrayHasKey('repassword', $errors);
    	$this->assertTrue($errors['repassword'] === 'Password mismatch');
    }
    
    /**
	 * @expectedException Peak_Exception
	 */
	function testValidateException()
	{
		//try to validate form with unknow filter
		$_POST = array('name' => 'mrjohn');
		
		$f = new Form2();
		try {
			$f->validate();
		}
		catch (InvalidArgumentException $expected) {
            return;
        }
 
        $this->fail('An expected exception has not been raised.');
        
 	}
    
	  
}

//form example 1
class Form1 extends Peak_Filters_Form
{
    public function setValidation()
    {
    	return array(
    	                  
		   'name'  => array('filters' => array('not_empty', 
		                                       'alpha_num',
		                                       'lenght' => array('min' => 4, 'max' => 10),
		                                       'callbacktest'),
		                                       
		                    'errors'  => array('Name is empty',
		                                       'Name contains invalid chars',
		                                       'Name must between 4 and 10 chars',
		                                       'Callback test fail')),
		                                       
		   'email' => array('filters' => array('email'),
		                                       
		                    'errors'  => array('Email not valid')),
		                                       
		   'password' => array('filters' => array('not_empty', 
		                                          'alpha_num'),
		                                          
		   		               'errors'  => array('Password is empty',
		   		                                  'Password contain invalid characters')), 
		   		                                  
	       'repassword' => array('filters' => array('match_key' => 'password'),
	                              
	                             'errors'  => array('Password mismatch'))                                       
		                                       
	    );
    
    }
    
    function _filter_callbacktest($v)
    {
    	//do something
    	//return boolean result, here for the example return true
    	return true;
    }
}

//form example 2 - validate should throw an exception since filter 'unknowfilter' do not exists
class Form2 extends Peak_Filters_Form
{
    public function setValidation()
    {
    	return array(
		 
		   'name'  => array('filters' => array('unknowfilter'),
		                                       
		                    'errors'  => array('Name is empty')),
                                  
		                                       
	    );   
    }
}
