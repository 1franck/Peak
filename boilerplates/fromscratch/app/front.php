<?php
/**
 * App Front Controller
 *
 * @version $Id$
 */
class front extends Peak_Controller_Front
{
    
    /**
     * Load debugbar for all controllers
     */
    public function postRender()
    {
        $this->controller->view->debugbar()->show();
    }

}