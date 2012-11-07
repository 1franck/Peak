<?php
/**
 * Test Helper
 */
require_once dirname(__FILE__).'/../TestHelper.php';

/**
 * Component(s)
 * @see Peak_Filters
 */
require_once 'Peak/Filters.php';

/**
 * Fixture(s)
 */
require_once dirname(__FILE__).'/FiltersTest/SimpleFilters.php';

/**
 * @category   Peak
 * @package    Peak_Filters
 * @subpackage UnitTests
 * @version    $Id$
 */
class Peak_FiltersTest extends PHPUnit_Framework_TestCase
{
	
    function testLoadFiltersClass()
    {   	
    	$f = new SimpleFilters(array());
    	
    	$this->assertInstanceOf('Peak_Filters', $f);
    	
    	$this->assertEmpty($f->getData());

    	$this->assertEmpty($f->getErrors());
    	
    	$this->assertEmpty($f->getSanitizeFilters());
    	
    	$this->assertEmpty($f->getValidateFilters());  	
    }
    	  
}