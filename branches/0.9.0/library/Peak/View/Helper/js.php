<?php

/**
 * View Helper Js
 * 
 * @version $Id$
 */
class Peak_View_Helper_js
{
    /**
     * Render Javascript file(s) with <script> tag(s)
     *
     * @param string|array $filename
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
     * Render Javascript file(s) with <script> tag(s) and jQuery tags
     *
     * @param string|array $filename
     */
    public function renderWithjQueryTag($filename)
    {
        if(is_array($filename)) {
            foreach($filename as $file) {
                if($this->exists($file)) {
                    echo '<script type="text/javascript">$(function() {'."\n";
                    $this->render($file);
                    echo '});</script>';
                }
            }
        }
        elseif($this->exists($filename)) {          
            echo '<script type="text/javascript">$(function() {'."\n";
            $this->render($filename);
            echo '});</script>';
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