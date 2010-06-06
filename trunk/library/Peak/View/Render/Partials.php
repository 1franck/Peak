<?php

/**
 * Peak View Render Engine: Partials
 * 
 * @author  Francois Lajoie
 * @version 20100526
 * 
 * support groups alias from theme.ini
 * if no groups, only the controller action view file will be render
 * 
 */
class Peak_View_Render_Partials extends Peak_View_Render
{   
    
    protected $_groups;      //available partials groups   
    protected $_group;       //current partials group file to render   
    protected $_group_name;  //current partials group name
   
    
    /**
     * Load Partials engine with groups alias 
     *
     * @param array $groups
     */
    public function __construct($groups = null)
    {
        $this->_groups = $groups;
        if(isset($this->_groups['default'])) $this->useGroup('default');        
    }
    
    /**
     * Submit array of files or point to $groups array keyname for rendering
     * 
     * @example useGroup( array('header.php','[CONTENT]','footer.php') )
     * @example useGroup('content_left') will push $this->options['layouts'][$layout] array to $this->layout
     *
     * @param array,string $array
     */
    public function useGroup($group)
    {
        if(is_array($group)) {
            $this->_group = $group;
            $this->_group_name = 'custom';
        }
        elseif(isset($this->_groups[$group])) {
            $this->_group = $this->_groups[$group];
            $this->_group_name = $group;
        }
    }

    /**
     * Erase current partials rendering group
     * No group means only the controller action view file is render
     */
    public function noGroup()
    {
        $this->_group = null;
    }
    
    
    /**
     * Render view(s)
     *
     * @param string $file
     * @param string $path
     * @return array/string
     */
    public function render($file,$path)
    {    
        //CONTROLLER FILE VIEW       
        $this->_scripts_file = $file;
        $this->_scripts_path = $path;
        
        $filepath = $path.'/'.$file;

        if(!file_exists($filepath)) {
            $filepath = str_replace(SVR_ABSPATH,'',$filepath);
            throw new Peak_Exception('ERR_VIEW_TPL_NOT_FOUND', $filepath); //echo $filepath.' view not found'; //
            
        }
        
        //Partials group FILES VIEW IF EXISTS
        if(is_array($this->_group))
        {          
            $group_filespath = array();
            
            foreach($this->_group as $theme_partial) {
                if($theme_partial !== '[CONTENT]') {
                    if(basename($theme_partial) === $theme_partial) {
                        if(file_exists(THEME_PARTIALS_ABSPATH.'/'.$theme_partial)) $group_filespath[] = THEME_PARTIALS_ABSPATH.'/'.$theme_partial;
                    }
                    elseif(file_exists($theme_partial)) $group_filespath[] = $theme_partial;
                }
                else $group_filespath[] = $filepath;
            }
            
            //return $group_filespath;
            $this->output($group_filespath);
            
        }
        else {
            //return $filepath;
            $this->output($filepath);
        }
    }
    
    private function output($viewfiles)
    {
        // remove partials group for Peak_View_Render_Partials
        // so we can use render() to include a partial file inside view scripts
        $this->noGroup();
                
        // shorcut for view var
        // Will be deprecated in v0.8
        // use $this->var_name instead of $view->var_name inside views file
        $view = Peak_Registry::obj()->view;        
        
        // include theme functions.php    
        if($view->theme()->getFunctionsFile()) include_once($view->theme()->getFunctionsFile());
        
        // include controller action view with or without partials groups
        if(is_array($viewfiles)) foreach($viewfiles as $file) include($file);
        else include($viewfiles);    
    }
    
}