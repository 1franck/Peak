<?php

/**
 * Tests for Peak_Router
 */

include(dirname(__FILE__) . './../simpletest/autorun.php');
include(dirname(__FILE__) . './../tests_helpers/showpasses.php');


$test = &new TestSuite('Peak_Router tests suite');

$test->addTestFile('./../classes/testRouter.php');

$curpath = str_replace('\\','/',dirname(__FILE__));

$test->run(new ShowPasses());