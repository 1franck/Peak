<?php
/**
 * Generate bootstrap classe
 * 
 * @author  Francois Lajoie
 * @version $Id$
 */
class Peak_Codegen_Bootstrap extends Peak_Codegen
{
	

	public $name    = 'bootstrap';
	public $actions = array('initEnv', 'initView');
    
    /**
     * Generate controller code
     */
	public function generate()
	{
		
$data = 
'<?php 
/**
 * '.$this->name.'
 */
class '.$this->name.' extends Peak_Bootstrap 
{';

//actions
if(!empty($this->actions)) {
	foreach($this->actions as $action) {
$data .= 
''."\n".'
    /**
     * '.$action.' action
     */
    public function _'.$action.'()
    {
    }';		
	}
}

$data .= ''."\n".'}';

			return $data;
	}
	
}