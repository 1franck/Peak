<?php

/**
 * Tests for Peak Standalone classes
 */

include(dirname(__FILE__) . '/simpletest/autorun.php');
include(dirname(__FILE__) . '/tests_helpers/showpasses.php');



$test = &new TestSuite('Peak Standalone classes tests suite');


$test->addTestFile('classes/testPattern.php');

$test->addTestFile('classes/testRegistry.php');


$test->run(new ShowPasses());