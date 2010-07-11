<?php

/**
 * Debug tree
 *
 */
class Peak_View_Helper_Debug extends Peak_View_Helper
{   
    
    public function __construct()
    {
        $this->getViewVars();
    }
        
    public function display($array = null)
    {
        if(!isset($array)) {
            $array = array('VIEW' => null);
            $array = array_merge($array,$this->view->getVars());
            $array['SESSION'] = null;
            $array = array_merge($array,$_SESSION);
            $array['PHP'] = null;
            $included_files = get_included_files();
            sort($included_files);
            $array = array_merge($array, array('included files' => $included_files));
            //$array = array_merge($array, array('required files' => get_required_files()));
        }

        echo '<br /><ul class="tree">';
        foreach($array as $param => $value)
        {
            echo '<li>';
            if(is_array($value)) {
                echo '[<i>'.$param.'</i>] = <strong>array(</strong>';
                $this->display($value);
                echo '<strong>)</strong>';
            }
            else {
                if(is_object($value)) echo '[<i>'.$param.'</i>] => [@object]';
                elseif(is_resource($value)) echo '[<i>'.$param.'</i>] => [@resource]';
                else echo '[<i>'.$param.'</i>] => '.strip_tags($value);
            }
            echo '</li>';
        }
        echo '</ul>';
        
    }
    
}