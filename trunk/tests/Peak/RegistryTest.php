<?php
/**
 * Test Helper
 */
require_once dirname(__FILE__).'/../TestHelper.php';

/**
 * @see Peak_Registry
 */
require_once 'Peak/Registry.php';

/**
 * @category   Peak
 * @package    Peak_Registry
 * @subpackage UnitTests
 * @version    $Id$
 */
class Peak_RegistryTest extends PHPUnit_Framework_TestCase
{
	
	public static function setUpBeforeClass()
	{		
		Peak_RegistryTest::tearDownAfterClass();
	}
	
	//clean object registered inside registry
	public static function tearDownAfterClass()
    {
    	$list = Peak_Registry::getObjectsList();

    	if(!empty($list)) {
    		foreach($list as $obj_name) {
    			Peak_Registry::unregister($obj_name);
    		}
    	}
    }
    
	function testGetInstance()
	{
		$reg = Peak_Registry::getInstance();
		
		$this->assertType('Peak_Registry', $reg); 
	}
	
	function testIsRegistered()
	{
		$result = Peak_Registry::isRegistered('unregistered_object');
		
		$this->assertFalse($result);	
	}
	
	function testRegisteringObject()
    {
    	$obj = Peak_Registry::set('test_obj', new RegisteredClass());
    	
    	$this->assertType('RegisteredClass',$obj);

        $this->assertTrue(Peak_Registry::isRegistered('test_obj'),'test_obj should be registered');    
    }
    
    function testGetObject()
    {
    	$obj = Peak_Registry::get('test_obj');
    	
    	$this->assertType('RegisteredClass',$obj);
    	
    	unset($obj);
    	
    	$obj = Peak_Registry::o()->test_obj;
    	
    	$this->assertType('RegisteredClass',$obj);
    }
    
    function testGetObjectsList()
    {
    	$list = Peak_Registry::getObjectsList();
    	
    	$this->assertTrue(is_array($list));
    	               
        $this->assertTrue(count($list) == 1);     
    }
    
    function testGetObjectClassname()
    {
    	$classname = Peak_Registry::getClassName('test_obj');
    	
    	$this->assertTrue($classname === 'RegisteredClass');
    }
    
    function testIsInstanceOf()
    {
    	$this->assertTrue(Peak_Registry::isInstanceOf('test_obj', 'RegisteredClass'));
    	
    	$this->assertFalse(Peak_Registry::isInstanceOf('test_obj', 'Unknowclass'));
    	
    	$this->assertFalse(Peak_Registry::isInstanceOf('test_obj2', 'RegisteredClass'));
    }
    
    function testUnregisteringObject()
    {
    	Peak_Registry::unregister('test_obj');
    	
    	$this->assertFalse(Peak_Registry::isRegistered('test_obj'),'test_obj should be unregistered');
    }
  
}

// example class that will be registered by Peak_Registry
class RegisteredClass
{
    public function foo()
    {
        echo 'bar';
    }
}