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
        $this->view->arraytest = array('test1', 'test2' => array('4','re','fd'));
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