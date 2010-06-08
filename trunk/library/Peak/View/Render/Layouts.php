<?php

/**
 * Peak View Render Engine: Layout
 * 
 * @author  Francois Lajoie
 * @version 20100527
 * 
 */
class Peak_View_Render_Layouts extends Peak_View_Render
{
        
    protected $_layout_file;   //current layout filename       
    protected $_layouts_path;  //application layouts path   
    
    /**
     * Load Layouts engine 
     *
     * @param array $path
     */
    public function __construct($path)
    {
        $this->_layouts_path = $path;   
    }
       
    /**
     * Set layout filename to render
     *
     * @param string $layout
     */
    public function useLayout($layout)
    {
        if(file_exists($this->_layouts_path.'/'.$layout.'.php')) $this->_layout_file = $layout.'.php';
    }

    /**
     * Desactivate layout
     * No layout means only the controller action view file is rendered
     */
    public function noLayout()
    {
        $this->_layout_file = null;
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
        $filepath = $path.'/'.$file;

        if(!file_exists($filepath)) {
            $filepath = str_replace(SVR_ABSPATH,'',$filepath);
            throw new Peak_Exception('ERR_VIEW_TPL_NOT_FOUND', $filepath); //echo $filepath.' view not found'; //
        }
                       
        //LAYOUT FILES VIEW IF EXISTS
        if(isset($this->_layout_file)) {
            $filepath = $this->_layouts_path.'/'.$this->_layout_file;
            $this->_scripts_file = $file;
            $this->_scripts_path = $path;
        }

        $this->preOutput($filepath);        
    }
    
     
    /**
     * Output the main layout
     *
     * @param string $viewfile
     */
    protected function output($layout)
    {
        // remove layout
        // so we can use render() to include a partial file inside view scripts
        $this->noLayout();
                
        // shorcut for view var
        // Will be deprecated in v0.8
        // use $this->var_name instead of $view->var_name inside views file
        $view = Peak_Registry::obj()->view;        
        
        // include theme functions.php    
        if($view->theme()->getFunctionsFile()) include_once($view->theme()->getFunctionsFile());

        // include controller action view with or without partials groups
        include($layout);     
    }
    
    /**
     * Output Controller view content in layout
     * @example in your layout page, use $this->layoutContent() to display where controller action view should be displayed
     *
     */
    public function layoutContent()
    {
        include($this->_scripts_path.'/'.$this->_scripts_file);
    }
        
}