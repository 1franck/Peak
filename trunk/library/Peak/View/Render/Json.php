<?php

/**
 * Peak View Render Engine: Json
 * 
 * @desc     Output view vars as json 
 * 
 * @author   Francois Lajoie
 * @version  $Id$ 
 */
class Peak_View_Render_Json extends Peak_View_Render
{
                     
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

        $viewvars = Peak_Registry::obj()->view->getVars();
        
        $json = json_encode($viewvars);

        $this->output($json);
    }
    
    /**
     * Output Json
     *
     * @param string $json
     */
    private function output($json)
    {
        echo $json;    
    }
    
}