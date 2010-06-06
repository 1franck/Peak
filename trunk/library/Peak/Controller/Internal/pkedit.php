<?php

class Peak_Controller_Internal_Pkedit extends Peak_Controller
{
    
    public function preAction()
    {
        $this->view->setRenderEngine('Virtual');    
    }
    
    public function _index()
    {
        $this->view->addGroup('header');
        $this->view->addGroup('content');
        $this->view->addGroup('footer');
        
        $this->view->add('header', '<h1>I am Header</h1>');        
        $this->view->add('content', 'I am an peak internal controller test');
        $this->view->add('footer', '<h4>I am Footer</h4>');
    }
    
}