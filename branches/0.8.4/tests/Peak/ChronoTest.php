<?php
/**
 * Test Helper
 */
require_once dirname(__FILE__).'/../TestHelper.php';

/**
 * @see Peak_Chrono
 */
require_once 'Peak/Chrono.php';

/**
 * @category   Peak
 * @package    Peak_Chrono
 * @subpackage UnitTests
 * @version    $Id$
 */
class Peak_ChronoTest extends PHPUnit_Framework_TestCase
{
	
    function testGlobalChrono()
    {   	
        Peak_Chrono::start();
        usleep(149992);
        Peak_Chrono::stop();
        //echo Peak_Chrono::getMs(null,20);
        $this->assertTrue(Peak_Chrono::get() >= 0.10);
    }
    
    function testGlobalChrono2()
    {   	
        Peak_Chrono::start();
        usleep(149992);
        $this->assertTrue(Peak_Chrono::get() >= 0.10);
        $this->assertTrue(Peak_Chrono::isCompleted());
    }
    
    function testGlobalChronoChecks()
    {
        $this->assertFalse(Peak_Chrono::isOn());
        $this->assertTrue(Peak_Chrono::isCompleted());
        
        Peak_Chrono::start();
        $this->assertTrue(Peak_Chrono::isOn());
        $this->assertFalse(Peak_Chrono::isCompleted());
        
        Peak_Chrono::resetAll();
        $this->assertFalse(Peak_Chrono::isOn());
        $this->assertFalse(Peak_Chrono::isCompleted());
        
        Peak_Chrono::start();
        Peak_Chrono::stop();
        $this->assertTrue(Peak_Chrono::isCompleted());
        $this->assertFalse(Peak_Chrono::isOn()); 
        
        Peak_Chrono::start();
        $time = Peak_Chrono::get();
        $this->assertTrue(Peak_Chrono::isCompleted());
        $this->assertFalse(Peak_Chrono::isOn());

               
    }
    
    function testCustomChrono2()
    {
        Peak_Chrono::start('timer1');
        usleep(100000);
        Peak_Chrono::stop('timer1');
        $this->assertTrue(Peak_Chrono::get('timer1') >= 0.10);
        $this->assertTrue(Peak_Chrono::isCompleted('timer1'));     
    }
    
    function testCustomChrono()
    {
        Peak_Chrono::start('timer1');
        usleep(100000);
        $this->assertTrue(Peak_Chrono::get('timer1') >= 0.10);        
    }
    
    function testCustomChronoChecks()
    {
        $this->assertFalse(Peak_Chrono::isOn('timser1'));
        $this->assertTrue(Peak_Chrono::isCompleted('timer1'));
        
        Peak_Chrono::start('timer1');
        $this->assertTrue(Peak_Chrono::isOn('timer1'));
        $this->assertFalse(Peak_Chrono::isCompleted('timer1'));
        
        Peak_Chrono::resetAll();
        $this->assertFalse(Peak_Chrono::isOn('timer1'));
        $this->assertFalse(Peak_Chrono::isCompleted('timer1'));
        
        Peak_Chrono::start('timer1');
        Peak_Chrono::stop('timer1');
        $this->assertTrue(Peak_Chrono::isCompleted('timer1'));
        
        
    }
    	  
}