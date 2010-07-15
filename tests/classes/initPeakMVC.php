<?php

$basepath = str_replace('\\','/',realpath(dirname(__FILE__)));
$app = $basepath.'/../temps/application/default';

include($app.'/configs.php');

function peak($parent = false)
{
	//global $basepath;
	if(!$parent) return $basepath.'./../../library/Peak';
	else return $basepath.'./../../library';
}

/* app boot */
include($basepath.'./../../library/Peak/boot.php');

/* app start */
try {

    /* load app obj */
    $app = Peak_Application::getInstance();
    
    //$core = Peak_Registry::obj()->core;
       
    /* run app */    
    //$app->run(APP_DEFAULT_CTRL);   

    /* render controllers view(s) file(s) */
    //$app->controller->render();    
}
/* app exception */
catch (Peak_Exception $e) {
    die($e->getMessage());
}