<?php

//form example 2 - validation should throw an exception since filter 'unknowfilter' do not exists
class Form2 extends Peak_Filters_Form
{
    public function setValidation()
    {
    	return array(
		 
		   'name'  => array('filters' => array('unknowfilter'),
		                                       
		                    'errors'  => array('Name is empty')),
                                  
		                                       
	    );   
    }
}