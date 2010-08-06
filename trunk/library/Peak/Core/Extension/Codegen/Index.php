<?php
$app_config;
return '<?php 

// app configs
include(\'./../application/wyn/configs.php\');

// session
session_name(PROJECT_NAME);
session_start();

// app boot
include(\'./../../peakframework/library/Peak/boot.php\');

// start chrono
Peak_Chrono::start();

// app start
try {
    // load app obj
    $app = Peak_Application::getInstance();
       
    // run app  
    $app->run(APP_DEFAULT_CTRL);   

    // render controllers view(s) file(s)
    $app->controller->render();    
}
// app exception
catch (Peak_Exception $e) {
    header("HTTP/1.0 404 Not found");
    die($e->getMessage());
}';