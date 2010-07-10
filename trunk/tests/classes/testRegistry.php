<?php
/**
 * test for /PEAK/REGISTRY.PHP
 * @version 20100528
 */
$file_to_test = realpath('./../library/Peak/Registry.php');
include($file_to_test);
echo 'Tested file: '.$file_to_test.'<br />';

class TestOfRegistry extends UnitTestCase
{
    
    function testOfRegisteringObjects()
    {
     
        $this->assertTrue(class_exists('Peak_Registry',false),'Class registry not found in the file'); 
        
        $this->assertIsA(Peak_Registry::getObjectList(),'array','getObjectList() doen\'t return a valid array');      
        
        $reg = Peak_Registry::getInstance();
              
        
        Peak_Registry::set('teststatic',new RegisteredClass());
        
        $loadedclass = $reg->set('test',new RegisteredClass());
        
        // = $reg->test;
        
        $this->assertTrue(is_a($reg->teststatic,'RegisteredClass'),'$reg->teststatic is not an object of class "RegisteredClass"');
        $this->assertTrue(is_a(Peak_Registry::obj()->teststatic,'RegisteredClass'),'$reg->teststatic is not an object of class "RegisteredClass"'); 
        
        //Peak_Registry::obj()->teststatic->foo();       
               
        $this->assertTrue(is_a($reg->test,'RegisteredClass'),'$reg->test is not an object of class "RegisteredClass"');        
        $this->assertTrue(is_a($loadedclass,'RegisteredClass'),'$loadedclass is not an object of class "RegisteredClass"');
        
        $this->assertFalse(is_a($reg->test2,'RegisteredClass'),'$reg->test2 is an object of class "RegisteredClass"');
        $this->assertNull($reg->test2,'$reg->test2 is not null');

        $this->assertReference($loadedclass,$reg->test,'object not passed by reference');
        
        $this->assertTrue(method_exists($reg->test,'foo'),'foo() methods not found in object $reg->test');   
        $this->assertTrue(method_exists($loadedclass,'foo'),'foo() methods not found in object $loadedclass');
        
        $this->assertTrue($reg->isRegistered('test'),'$reg->isRegistered("test") failed to return true');
        $this->assertTrue(Peak_Registry::getInstance()->isRegistered('test'),'Peak_Registry::getInstance()->isRegistered("test") failed to return true');
        
        $this->assertFalse($reg->isRegistered('test2'),'$reg->isRegistered("test2") failed to return false');

        $reg->unregister('test');
        $this->assertFalse(is_a($reg->test,'RegisteredClass'),'$reg->test is an object of class "RegisteredClass" even if we have unregistered it');   
        $this->assertFalse($reg->isRegistered('test'),'$reg->isRegistered("test") failed to return false');
        
        //this line is supposed to throw a fatal error because __clone visibility is private and its what we want!
        //comment this line if you want to run other tests
        //$clone = clone Peak_Registry::obj();

        $this->assertIsA($reg->getObjectList(),'array','getObjectList() doen\'t return a valid array');
        
        //print_r(Peak_Registry::getObjectList());

        
        //$this->assertFalse(is_a($loadedclass,'RegisteredClass'),'$loadedclass is an object of class "RegisteredClass" even if we have unregistered it');   
        
        //$loadedclass->foo();
        
        
        if(Peak_Registry::isRegistered('test')) {
            Peak_Registry::obj()->test->foo();
        }

        
    }
    
    
    function testOfInstanceOf()
    {
        $this->assertTrue(Peak_Registry::isInstanceOf('teststatic', RegisteredClass),'test object is suppose to be an RegisteredClass object');
    }
    
    

}


class RegisteredClass
{
    
    public function foo()
    {
        echo 'bar';
    }
}