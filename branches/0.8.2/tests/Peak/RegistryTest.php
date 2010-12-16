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
	
	/**
	 * Getting instance of Peak_Registry
	 *
	 */
	function testOfgetInstance()
	{
		$reg = Peak_Registry::getInstance();
		
		$this->assertType('Peak_Registry', $reg); 
	}
	
	function testOfIsRegistered()
	{
		$result = Peak_Registry::isRegistered('unregistered_object');
		
		$this->assertFalse($result);	
	}
	
	function testOfRegisteringObjects()
    {
    	$obj = Peak_Registry::set('test_obj', new RegisteredClass());
    	
    	$this->assertType('RegisteredClass',$obj);
        
        $object_list = Peak_Registry::getObjectList();
                
        $this->assertTrue(is_array($object_list),'getObjectList() doen\'t return a valid array');           
    }
    
    
}

class RegisteredClass
{
    
    public function foo()
    {
        echo 'bar';
    }
}