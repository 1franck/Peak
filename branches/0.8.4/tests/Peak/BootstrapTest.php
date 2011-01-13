<?php
/**
 * Test Helper
 */
require_once dirname(__FILE__).'/../TestHelper.php';

/**
 * @see Peak_Bootstrap, Peak_Core
 */
require_once 'Peak/Bootstrap.php';
require_once 'Peak/Core.php';

/**
 * @category   Peak
 * @package    Peak_Bootstrap
 * @subpackage UnitTests
 * @version    $Id$
 */
class Peak_BootstrapTest extends PHPUnit_Framework_TestCase
{
	
	function testBootstrapInstance()
	{
		$boot = new bootstrap();
		$this->assertTrue($boot->i == 2);
	}
	
	function testGetEnv()
	{
		$boot = new bootstrap();		
		$this->assertTrue($boot->getEnv() === 'production');
	}

}

// example bootstrap class
class bootstrap extends Peak_Bootstrap
{
	public $i = 0;
	
	//should be executed on class load
    public function _foo()
    {
        ++$this->i;
    }
    
    //should be executed on class load
    public function _bar()
    {
    	++$this->i;
    }
    
    public function foobar()
    {
    	++$this->i;
    }
}