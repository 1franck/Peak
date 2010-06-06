<?php

/**
 * View Helper base
 * @version 20100503
 */
abstract class Peak_View_Helpers
{
    protected $view;
        
    public function getViewVars()
    {
        $this->view = Peak_Registry::obj()->view;
    }
    
}