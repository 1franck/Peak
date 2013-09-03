<?php
/**
 * custome app front
 */
class front extends Peak_Controller_Front
{
    /**
     * Load debugbar for all controllers
     */
    public function postRender()
    {
        $this->controller->view->debugbar()->show(false, '');
    }
}