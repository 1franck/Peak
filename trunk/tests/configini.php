<?php

/**
 * Tests for Peak_Config_Ini
 */

include(dirname(__FILE__) . '/simpletest/autorun.php');
include(dirname(__FILE__) . '/tests_helpers/showpasses.php');


$test = &new TestSuite('Peak_Config tests suite');

$test->addTestFile('classes/testConfigini.php');

$test->run(new ShowPasses());