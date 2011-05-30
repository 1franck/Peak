<?php

/**
 * View Helper Js
 * 
 * @version $Id$
 */
class View_Helper_js
{
    /**
     * Render Javascript file(s) with <script> tag(s)
     *
     * @param string $filename
     */
    public function renderWithTag($filename) 
    {
        if(is_array($filename)) {
            foreach($filename as $file) {
                if($this->exists($file)) {
                    echo '<script type="text/javascript">';
                    $this->render($file);
                    echo '</script>';
                }
            }
        }
        elseif($this->exists($filename)) {          
            echo '<script type="text/javascript">';
            $this->render($filename);
            echo '</script>';
        }
    }
    
    /**
     * Render javascript 
     *
     * @param string $filename
     */
    public function render($filename)
    {
        if($this->exists($filename)) { 
            $this->view->render($filename, $this->_getPath());
        }
    }
    
    /**
     * Get path for js files
     *
     * @return string
     */
    protected function _getPath()
    {
        return Peak_Core::getPath('views').'/js';
    }
    
    /**
     * Check if javascript file exists
     *
     * @param  string $file
     * @return bool
     */
    public function exists($file)
    {
        $filepath = $this->_getPath().'/'.$file;
        return file_exists($filepath);
    }
}