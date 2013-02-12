<?php
/**
 * Test Helper
 */
require_once dirname(__FILE__).'/../../TestHelper.php';

/**
 * Component(s)
 * @see Peak_Filters, Peak_Filters_Advanced, Peak_Filters_Form, Peak_Exception
 */
require_once 'Peak/Filters.php';
require_once 'Peak/Filters/Advanced.php';
require_once 'Peak/Filters/Form.php';
require_once 'Peak/Exception.php';

/**
 * Fixture(s)
 */
require_once dirname(__FILE__).'/FormTest/Form1.php';
require_once dirname(__FILE__).'/FormTest/Form2.php';
require_once dirname(__FILE__).'/FormTest/Form3.php';
require_once dirname(__FILE__).'/FormTest/Form4.php';

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
    	'answer' => 'test3',
		'url'    => 'http://www.test',
		'date'   => '2013/12/30');
    	
    	$f = new Form1();
    	
     	$result = ($f->validate() === false) ? false : true;
    	
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
    	'answer' => 'test2rwerwe',
		'url'    => 'www.google.ca',
		'date'   => '2013-12-30');
    	
    	$f = new Form1();
    	
     	$result = ($f->validate() === false) ? false : true;
    	
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
		
		$this->assertArrayHasKey('url', $errors);
    	$this->assertTrue($errors['url'] === 'Must be a valid url');
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

		$result = ($f->validate() === false) ? false : true;
   	
    	$this->assertTrue($result);	
 	}
 	
 	// conditionnal filter fail
 	function testForm3ValidateFail1()
 	{
 	    //try to validate form with unknow filter
		$_POST = array('name' => 'g');
		
		$f = new Form3();

		$result = ($f->validate() === false) ? false : true;
    	
    	//print_r($f->getErrors());
    	
    	$this->assertFalse($result);
		
 	}
	
 	function testAddValidate()
 	{
		$_POST = array('my_number' => '235');
		
		$f = new Form4();
		
		//validate if number is good
		$result = ($f->validate() === false) ? false : true;

		$this->assertTrue($result);
		
		
		$_POST = array('my_number' => -36);
		//validate if number is good
		$f = new Form4();
		$result = ($f->validate() === false) ? false : true;
		$this->assertFalse($result);
		
		
		$_POST = array();
		//validate if number is good
		$f = new Form4();
		$result = ($f->validate() === false) ? false : true;
		$this->assertTrue($result);
 	}
}