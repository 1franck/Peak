<?php
/**
 * Peak View Render Engine: Virtual Layout
 * 
 * @desc     Work like Layout but without files
 * 
 * @author   Francois Lajoie
 * @version  $Id$
 * 
 */
class Peak_View_Render_VirtualLayouts extends Peak_View_Render
{

    /**
     * Layout content
     * @var string
     */
    private $_layout = null;
    
    /**
     * Script content
     * @var string
     */
    private $_content = null;
    
    /**
     * Set layout content
     *
     * @param string $name
     */
    public function setLayout($layout)
    {        
        $this->_layout = $layout;
    }

    /**
     * Set script content
     *
     * @param string $content
     * @param bool   $overwrite if false, content will be added at the end
     */
    public function setContent($content, $overwrite = false)
    {
        if($overwrite) $this->_content = $content;
        else $this->_content .= $content;
    }
    
    /**
     * Render virtual layout(s)
     * 
     * <layout_content> tag inside layout will be replaced by $_content
     *
     * @param  string $file
     * @param  string $path
     * @return string
     */
    public function render($file,$path)
    {       
        //CONTROLLER FILE VIEW       
        $this->scripts_file = $file;
        $this->scripts_path = $path;

        if(is_null($this->_layout)) {
            $output = $this->_content;
        }
        else {
            $output = str_ireplace('<layout_content>', $this->_content, $this->_layout);
        }

        $this->output($output);
    }
    
    /**
     * Output rendering result
     *
     * @param string $data
     */
    private function output($data)
    {
        echo $data;    
    }
    
}