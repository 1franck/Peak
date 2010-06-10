<?php

/**
 * View rendering engine base @deprecated
 * 
 * @author   Francois Lajoie
 * @version  $Id$
 */
interface Peak_View_RenderInterface
{
    
    //public $scripts_file;  //controller views path used 
    //public $scripts_path;  //controller view file name used
    
    function render($file,$path);
    private function output($files);
       
}