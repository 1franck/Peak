<?php

/**
 * Tests for Peak_Registry
 */

include(dirname(__FILE__) . '/simpletest/autorun.php');
include(dirname(__FILE__) . '/tests_helpers/showpasses.php');


$test = &new TestSuite('Peak_Config tests suite');

$test->addTestFile('classes/testConfig.php');

$test->run(new ShowPasses());