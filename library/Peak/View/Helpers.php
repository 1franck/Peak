<?php

/**
 * View Helper base
 *  
 * @author   Francois Lajoie
 * @version  $Id$
 */
abstract class Peak_View_Helpers
{
    protected $view;
        
    public function getViewVars()
    {
        $this->view = Peak_Registry::obj()->view;
    }
    
}