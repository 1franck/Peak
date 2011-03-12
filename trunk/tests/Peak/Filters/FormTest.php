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
    	$_POST = array(
    	'namef' => 'mrjohn1', 
    	'email' => 'mrjohn1@hotmail.com',
    	'password' => 'mypass2', 
    	'repassword' => 'mypass2', 
    	'unknow' => '123abc',
    	'number' => 3,
    	'answer' => 'test3');
    	
    	$f = new Form1();
    	
     	if($f->validate() === false) $result = false;
    	else $result = true;
    	
    	$this->assertTrue($result);
    }
    
    
    function testForm1ValidateFail1()
    {
    	$_POST = array(
    	'name' => 'mrjohn1%', 
    	'email' => 'mrjohn1hotmail.com', 
    	'password' => 'mypass2', 
    	'repassword' => 'mypassss2', 
    	'unknow' => '123abc', 
    	'number' => 9,
    	'answer' => 'test2rwerwe');
    	
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
    	
    	$this->assertArrayHasKey('number', $errors);
    	$this->assertTrue($errors['number'] === 'Number must be between 2 and 6');
    	
    	$this->assertArrayHasKey('answer', $errors);
    	$this->assertTrue($errors['answer'] === 'Answer should be test, test3 or french potatoes');
    }
    
    /**
	 * @expectedException Peak_Exception
	 */
	function testForm2ValidateException()
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
 	
 	// conditionnal filter pass
 	function testForm3ValidatePass()
 	{
 	    //try to validate conditionnal filter
		$_POST = array('name' => 'g@hotmail.com');
		
		$f = new Form3();

		if($f->validate() === false) $result = false;
    	else $result = true;

    	$this->assertTrue($result);
    	
    	
    	//try to validate conditionnal filter
		$_POST = array('name' => '');
		
		$f = new Form3();

		if($f->validate() === false) $result = false;
    	else $result = true;
   	
    	$this->assertTrue($result);	
 	}
 	
 	// conditionnal filter fail
 	function testForm3ValidateFail1()
 	{
 	    //try to validate form with unknow filter
		$_POST = array('name' => 'g');
		
		$f = new Form3();

		if($f->validate() === false) $result = false;
    	else $result = true;
    	
    	//print_r($f->getErrors());
    	
    	$this->assertFalse($result);
		
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
		                                       'Name must be between 4 and 10 chars',
		                                       'Callback test fail')),
		                                       
		   'email' => array('filters' => array('email'),
		                                       
		                    'errors'  => array('Email not valid')),
		                                       
		   'password' => array('filters' => array('not_empty', 
		                                          'alpha_num'),
		                                          
		   		               'errors'  => array('Password is empty',
		   		                                  'Password contain invalid characters')), 
		   		                                  
	       'repassword' => array('filters' => array('match_key' => 'password'),
	                              
	                             'errors'  => array('Password mismatch')),

	       'number'     => array('filters' => array('int' => array('min' => 2, 'max' => 6)),
	       
	                             'errors'  => array('Number must be between 2 and 6')),
	                             
	       'answer'     => array('filters' => array('enum' => array('test', 'test3', 'french potatoes')),
	                             
	                             'errors'  => array('Answer should be test, test3 or french potatoes'))                             
		                                       
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

class Form3 extends Peak_Filters_Form
{
    
    public function setValidation()
    {
        return array(
        
          'name' => array('filters' => array('if_not_empty', 'email'),
                          'errors'  => array('should be an email')),
                          
          /*'lastname' => array('filters' => array('if_isset','alpha'),
                              'errors'  => array('should be alpha num')),*/
        
        );
    }
}