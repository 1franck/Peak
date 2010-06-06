<?php

class View_Helpers_Test extends Peak_View_Helpers
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