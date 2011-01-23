<?php
/**
 * Test Helper
 */
require_once dirname(__FILE__).'/../TestHelper.php';

/**
 * @see Peak_Filters
 */
require_once 'Peak/Filters.php';

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
    	$f = new FiltersTest(array());
    	
    	$this->assertInstanceOf('Peak_Filters', $f);
    	
    	$this->assertEmpty($f->getData());

    	$this->assertEmpty($f->getErrors());
    	
    	$this->assertEmpty($f->getSanitizeFilters());
    	
    	$this->assertEmpty($f->getValidateFilters());  	
    }
    	  
}

class FiltersTest extends Peak_Filters
{
	
}