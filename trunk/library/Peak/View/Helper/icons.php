<?php

class Peak_View_Helper_Icons extends Peak_View_Helper
{   
    
    protected $icons_path;
    protected $icons_path_url;

    public function __construct()
    {
        $this->icons_path = THEME_PUBLIC_ABSPATH.'/img/icons';
        $this->icons_url  = THEME_URL.'/img/icons';              
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
        return $this->icons_url.'/'.$name.'.png';
    }
    
        
    protected function isIcons($name)
    {
        $icon_file = $name.'.png';
        $icon_path = $this->icons_path.'/'.$icon_file;
        
        if(file_exists($icon_path)) return true;
        return false;
    }
    
}