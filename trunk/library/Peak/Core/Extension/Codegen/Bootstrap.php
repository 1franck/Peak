<?php

return '<?php 

class bootstrap extends Peak_Bootstrap 
{
    
    public function _initEnv()
    {
        date_default_timezone_set(\'America/Montreal\');
    }
    
    public function _initView()
    {
        $view = Peak_Registry::o()->view;
                
        //Render view as layouts        
        $view->setRenderEngine(\'Layouts\');        
    }
    
}';