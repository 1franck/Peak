<?php

//form example 1
class Form1 extends Peak_Filters_Form
{
    public function setValidation()
    {
    	return array(
    	                  
		   'name'  => array('filters' => array('not_empty', 
		                                       'alpha_num',
		                                       'lenght' => array('min' => 4, 'max' => 10),
		                                       'callbacktest'),
		                                       
		                    'errors'  => array('Name is empty',
		                                       'Name contains invalid chars',
		                                       'Name must be between 4 and 10 chars',
		                                       'Callback test fail')),
		                                       
		   'email' => array('filters' => array('email'),
		                                       
		                    'errors'  => array('Email not valid')),
		                                       
		   'password' => array('filters' => array('not_empty', 
		                                          'alpha_num'),
		                                          
		   		               'errors'  => array('Password is empty',
		   		                                  'Password contain invalid characters')), 
		   		                                  
	       'repassword' => array('filters' => array('match_key' => 'password'),
	                              
	                             'errors'  => array('Password mismatch')),

	       'number'     => array('filters' => array('int' => array('min' => 2, 'max' => 6)),
	       
	                             'errors'  => array('Number must be between 2 and 6')),
	                             
	       'answer'     => array('filters' => array('enum' => array('test', 'test3', 'french potatoes')),
	                             
	                             'errors'  => array('Answer should be test, test3 or french potatoes')),
		   
		   'url'        => array('filters' => array('url'), 'errors' => array('Must be a valid url')),
		   
		   'date'       => array('filters' => array('date' => 'YYYY/MM/DD'), 'errors' => array('Must be a valid date')),
		                                       
	    );
    
    }
    
    function _filter_callbacktest($v)
    {
    	//do something
    	//return boolean result, here for the example return true
    	return true;
    }
}