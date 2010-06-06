<?php

class Peak_Controller_Internal_Pkcodegen extends Peak_Controller
{
      
    public function preAction()
    {
        $this->view->setRenderEngine('Virtual');       
    }
    
    public function _index()
    {
        $this->configForm();
    }
    
    public function configForm()
    {
        $form = 'PROJECT_NAME: <input type="edit" name="PROJECT_NAME". value="" />';
        $this->view->add('create_config',$form);

    }
    
    
}