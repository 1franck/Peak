<?php
/**
 * Generate controllers classes
 * 
 * @author  Francois Lajoie
 * @version $Id$
 */
class Peak_Codegen_Controller extends Peak_Codegen
{
	
	public $name = 'index';
    public $add_preaction  = false;
    public $add_postaction = false;
    public $add_postrender = false;
    public $actions = array();
	
    
    /**
     * Generate controller code
     */
	public function generate()
	{
		
$data = 
'<?php
/**
 * '.$this->name.'Controller
 */
class '.$this->name.'Controller extends Peak_Controller_Action
{';

//preAction
if($this->add_preaction) {
$data .= 
''."\n".'
    /**
     * Action before controller handle action
     */
    public function preAction()
    {
    }';
}

//postAction
if($this->add_postaction) {
$data .= 
''."\n".'
    /**
     * Action after controller handle action
     */
    public function postAction()
    {
    }';
}

//postRender
if($this->add_postrender) {
$data .= 
''."\n".'
    /**
     * Action after view rendering
     */
    public function postRender()
    {
    }';
}

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