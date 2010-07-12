<?php

class Peak_View_Helper_Test extends Peak_View_Helper
{

    public function calc($a,$b)
    {        
        return $a + $b;       
    }
    
    public function changeviewvar()
    {
        $this->view->changeviewvar = 'this the SEcond test!';
    }
    
    
    
}