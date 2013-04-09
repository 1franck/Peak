<?php
/**
 * Test Helper
 */
require_once dirname(__FILE__).'/../../TestHelper.php';

/**
 * Component(s)
 * @see Peak_Spl_Dirinfo
 */
require_once 'Peak/Spl/Dirinfo.php';

/**
 * Fixture(s)
 */


/**
 * @category   Peak
 * @package    Peak_Spl_Dirinfo
 * @subpackage UnitTests
 * @version    $Id$
 */
class Peak_FiltersDirinfoTest extends PHPUnit_Framework_TestCase
{
	
    function setUp()
    {
        $this->dir = new Peak_Spl_Dirinfo(dirname(__FILE__).'/../../');
    }
    
    function testgetNbFiles()
    {
        $this->assertTrue($this->dir->getNbfiles() > 0);
    }
    
    function testgetSize()
    {
        echo $this->dir->getSize(true);
        $this->assertTrue(is_string($this->dir->getSize(true)));
    }
}