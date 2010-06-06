<?php

/**
 * Manage Application Views Themes
 * 
 * @author  Francois Lajoie
 * @version 20100527
 *
 */
class Peak_View_Theme
{
    /**
     * theme.ini options variables
     *
     * @var array
     */
    private $options = array();
    
    
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
            if(isset($this->options[$key])) return $this->options[$key];
            else return null;
        }
        return $this->options;
    }
    
    /**
     * Get theme.ini variables if file exists
     * result go to $this->options
     */
    public function setOptions()
    {
        $filepath = THEME_ABSPATH.'/theme.ini';
        if(file_exists($filepath)) {
            $this->options = parse_ini_file($filepath,true);
            if(isset($this->options['layouts']['default'])) {
                $this->useLayout($this->options['layouts']['default']);
            }
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
        if(file_exists($file)) { 
            return $file;
        }
        else return false;
    }
}