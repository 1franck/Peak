<?php

/**
 * Tests for wyn Application
 */

include(dirname(__FILE__) . '/simpletest/autorun.php');
include(dirname(__FILE__) . '/tests_helpers/showpasses.php');


$test = &new TestSuite('Peak_View tests suite');

$test->addTestFile('classes/testView.php');

$test->run(new ShowPasses());