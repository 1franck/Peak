<?php
// example bootstrap class
class Bootstrap extends Peak_Application_Bootstrap
{
	public $i = 0;
	
	//should be executed on class load
    public function initfoo()
    {
        ++$this->i;
    }
    
    //should be executed on class load
    public function initbar()
    {
    	++$this->i;
    }
    
    public function foobar()
    {
    	++$this->i;
    }
}