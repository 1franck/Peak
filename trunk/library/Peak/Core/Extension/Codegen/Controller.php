<?php

$data = 
'<?php
/**
 * '.$data['ctrl_name'].'Controller
 */
class '.$data['ctrl_name'].'Controller extends Peak_Controller_Action
{
';

//preAction
if(isset($data['add_preaction'])) {
$data .= 
'	
	/**
     * Action before controller handle action
     */
	public function preAction()
	{
	
	}';
}

//postAction
if(isset($data['add_postaction'])) {
$data .= 
'	
	/**
     * Action after controller handle action
     */
	public function postAction()
	{
	
	}';
}

//postRender
if(isset($data['add_postrender'])) {
$data .= 
'	
	/**
     * Action after view rendering
     */
	public function postRender()
	{
	
	}';
}


return $data;