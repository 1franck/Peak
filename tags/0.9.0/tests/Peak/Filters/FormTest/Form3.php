<?php

class Form3 extends Peak_Filters_Form
{
    
    public function setValidation()
    {
        return array(
        
          'name' => array('filters' => array('if_not_empty', 'email'),
                          'errors'  => array('should be an email')),
                          
          /*'lastname' => array('filters' => array('if_isset','alpha'),
                              'errors'  => array('should be alpha num')),*/
        
        );
    }
}