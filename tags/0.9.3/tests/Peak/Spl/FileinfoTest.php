<?php
/**
 * Test Helper
 */
require_once dirname(__FILE__).'/../../TestHelper.php';

/**
 * Component(s)
 * @see Peak_Spl_Fileinfo
 */
require_once 'Peak/Spl/Fileinfo.php';

/**
 * Fixture(s)
 */


/**
 * @category   Peak
 * @package    Peak_Spl_Fileinfo
 * @subpackage UnitTests
 * @version    $Id$
 */
class Peak_FileinfoTest extends PHPUnit_Framework_TestCase
{
	
    function setUp()
    {
        $this->dir = new Peak_Spl_Fileinfo(__FILE__);
    }
	
	function testgetExtension()
	{
		$this->assertTrue($this->dir->getExtension() === 'php');
	}

}