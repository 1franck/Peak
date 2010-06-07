<?php

/**
 * Peak_View_Render Engine base
 * 
 * @author  Francois Lajoie
 * @version 20100527
 */
abstract class Peak_View_Render
{
    
    protected $_scripts_file;  //controller action view path used 
    protected $_scripts_path;  //controller action view file name used
    
    /**
     * Point to Peak_View __get method
     *
     * @param  string $name represent view var name
     * @return misc
     */
    public function __get($name)
    {
        return Peak_Registry::obj()->view->$name;
    }
    
    /**
     * Silent call to unknow method or
     * Throw trigger error when DEV_MODE is activated 
     * 
     * @param string $method
     * @param array  $args
     */
    public function  __call($method, $args)
    {
        if((defined('DEV_MODE')) && (DEV_MODE)) {
            trigger_error('DEV_MODE: View Render method '.$method.'() doesn\'t exists');
        }
    }
    
    /**
     * Return view helpers object from Peak_View::helper()
     *
     * @param  string $name
     * @return object
     */
    public function helper($name = null)
    {
        return Peak_Registry::obj()->view->helper($name);
    }
    
    /**
     * Return public root url of your application
     *
     * @param  string $path Add custom paths/files to the end
     * @return string
     */
    public function baseUrl($path = null)
    {
        return ROOT_URL.'/'.$path;
    }
    
        
}