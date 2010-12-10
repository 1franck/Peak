<?php

/**
 * Extends HtmlReporter to show pass test at bottom of tests 
 * @uses HtmlReporter > tests/reporter.php
 */
class ShowPasses extends HtmlReporter
{ 
    
    public $_ob_paintPass = '';
    public $_last_progress = 0;
    public $_last_breadcrumb = null;
    
    function paintPass($message) {

        ob_start();
        
        if($this->_progress != $this->_last_progress) { echo "<br />"; }

        parent::paintPass($message);
        $breadcrumb = $this->getTestList();
        array_shift($breadcrumb);
        $breadcrumb = implode(" -> ", $breadcrumb);

        if(!isset($this->_last_breadcrumb)) $this->_last_breadcrumb = $breadcrumb;
        if($this->_last_breadcrumb !== $breadcrumb) echo '<br />';
        echo "<span class=\"pass\">Pass</span>: ";
        echo $breadcrumb;
        echo " -> $message<br />\n";
        
        $this->_ob_paintPass .= ob_get_clean();
        $this->_last_progress = $this->_progress;
        $this->_last_breadcrumb = $breadcrumb;
    }
    
    function paintFooter($test_name)
    {
        parent::paintFooter($test_name);
        echo '<p>'.$this->_ob_paintPass.'</p>';
        $this->_ob_paintPass = '';
        
    }  
            
    function getCss() {
        echo 'body { font:12px Consolas; } '.parent::getCss() . ' .pass { color: green; }';
    }
}

?>