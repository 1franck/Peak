<?php

class ValidateClass extends Peak_Filters_Basic
{
    public function setValidation()
    {
        return array(
        
        'mynumber' => array('filter' => FILTER_VALIDATE_INT,
                            'options' => array('min_range' => 1, 'max_range' => 10),
                            'error'   => 'mynumber should be an integer between 1 and 10')
        
        );
    }
}