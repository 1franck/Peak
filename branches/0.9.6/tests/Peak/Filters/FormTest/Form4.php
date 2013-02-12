<?php

class Form4 extends Peak_Filters_Form
{

    public function setup()
    {
        $this->addValidateFilter('my_number', array('if_not_empty', 'int' => array('min' => 0)),
                                              array('Number, if specified, must be an integer of minimum 1'));

    }
}