<?php

/**
 * Tests for wyn Application
 */

include(dirname(__FILE__) . '/simpletest/autorun.php');
include(dirname(__FILE__) . '/tests_helpers/showpasses.php');


$test = &new TestSuite('Peak_Controller tests suite');

$test->addTestFile('classes/testController.php');

$test->run(new ShowPasses());