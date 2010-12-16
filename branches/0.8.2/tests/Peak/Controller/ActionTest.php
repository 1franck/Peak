<?php
/**
 * Test Helper
 */
require_once dirname(__FILE__).'/../TestHelper.php';

/**
 * @see Peak_Controller_Action, Peak_Registry, Peak_Core
 */
require_once 'Peak/Controller/Action.php';
require_once 'Peak/Registry.php';
require_once 'Peak/Core.php';

/**
 * @category   Peak
 * @package    Peak_Controller_Action
 * @subpackage UnitTests
 * @version    $Id$
 */
class Peak_Controller_ActionTest extends PHPUnit_Framework_TestCase
{
	
	public function testControllerNewInstance()
    {
        // getting instance initializes instance       
        $controller = new testController();
        $this->assertType('Peak_Controller_Action', $controller);
    }
    
}


/**
 * Controller class test
 */
class testController extends Peak_Controller_Action 
{ 
	
	public function preAction() { }
	
	public function _index() { }
		
	public function _contact()	{ }
	
	public function postAction() { }

}