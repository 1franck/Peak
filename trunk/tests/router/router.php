<?php

/**
 * Tests for wyn Application
 */

include(dirname(__FILE__) . './../simpletest/autorun.php');
include(dirname(__FILE__) . './../tests_helpers/showpasses.php');


$test = &new TestSuite('class.router.php tests suite');

$test->addTestFile('./../classes/testRouter.php');

$test->run(new ShowPasses());