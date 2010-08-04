<?php

/**
 * Check for icons and get url path
 *
 * @author  Francois Lajoie
 * @version $Id$
 */
class View_Helper_Icons
{   
    
    public $path;
    public $path_url;
    public $ext = 'png';

    public function __construct()
    {
        $this->path = ROOT_ABSPATH.'/img/icons';
        $this->path_url  = ROOT_URL.'/img/icons';          
    }
    
    public function display($name,$attrs = '')
    {
        if($this->isIcons($name)) {
            if(empty($attrs)) $attrs = 'alt=""';
            return '<img '.$attrs.' src="'.$this->getUrl($name).'" />';
        }

    }
    
    public function getUrl($name)
    {
        return $this->path_url.'/'.$name.'.'.$this->ext;
    }
           
    protected function isIcons($name)
    {
        $icon_file = $name.'.'.$this->ext;
        $icon_path = $this->path.'/'.$icon_file;
        
        return (file_exists($icon_path)) ? true : false;
    }
    
}