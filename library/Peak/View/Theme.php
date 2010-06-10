<?php

/**
 * Manage Application Views Themes
 * 
 * @author   Francois Lajoie
 * @version  $Id$
 */
class Peak_View_Theme
{
    /**
     * theme.ini options variables
     *
     * @var array
     */
    private $_options = array();
    
    
    public function __construct()
    {
        $this->setOptions();
    }
    
    /**
     * Get template options array
     *
     * @return array
     */
    public function getOptions($key = null)
    {
        if(isset($key)) {
            if(isset($this->_options[$key])) return $this->_options[$key];
            else return null;
        }
        return $this->_options;
    }
    
    /**
     * Get theme.ini variables if file exists
     * result go to $this->options
     */
    public function setOptions()
    {
        $filepath = THEME_ABSPATH.'/theme.ini';
        if(file_exists($filepath)) {
            $this->_options = parse_ini_file($filepath,true);
            //if(isset($this->options['layouts']['default'])) {
            //    $this->useLayout($this->options['layouts']['default']);
            //}
        }
    
    }
    
    /**
     * Include themes functions.php if exists 
     *
     * @return included file or false
     */
    public function getFunctionsFile()
    {
        $file = THEME_ABSPATH.'/functions.php';
        return (file_exists($file)) ? $file : false;
    }
}