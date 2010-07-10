<?php

/**
 * Tests for wyn Application
 */

include(dirname(__FILE__) . '/simpletest/autorun.php');
include(dirname(__FILE__) . '/tests_helpers/showpasses.php');


$test = &new TestSuite('Peak_Pattern tests suite');

$test->addTestFile('classes/testPattern.php');

$test->run(new ShowPasses());