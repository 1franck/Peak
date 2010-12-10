<?php

/**
 * Tests for Peak_Core
 */

include(dirname(__FILE__) . '/simpletest/autorun.php');
include(dirname(__FILE__) . '/tests_helpers/showpasses.php');


$test = &new TestSuite('Peak_Core tests suite');

$test->addTestFile('classes/testCore.php');

$test->run(new ShowPasses());