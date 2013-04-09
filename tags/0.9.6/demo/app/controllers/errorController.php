<?php
/**
 * error Controller
 */
class errorController extends Peak_Controller_Action
{

    /**
     * preAction() - Executed before controller handle any action
     */
    public function preAction()
    {

    }

    /**
     * index Action
     */
    public function _index()
    {
        header('HTTP/1.1 404 Not Found');
    }

    /**
     * postAction() - Executed after controller handle any action
     */
    public function postAction()
    {

    }


}