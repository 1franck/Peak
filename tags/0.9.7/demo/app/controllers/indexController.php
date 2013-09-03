<?php
/**
 * index Controller
 */
class indexController extends Peak_Controller_Action
{

    /**
     * preAction() - Executed before controller handle any action
     */
    public function preAction()
    {
        $this->view->debugbar()->log('Test');
    }

    /**
     * index Action
     */
    public function _index()
    {
    }

    /**
     * postAction() - Executed after controller handle any action
     */
    public function postAction()
    {
    }
}