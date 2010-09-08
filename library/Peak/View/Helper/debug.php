<?php

/**
 * Debug array display
 *
 * @author  Francois Lajoie
 * @version $Id$
 */
class View_Helper_Debug
{   
	
            
    public function display($array = null)
    {
        if(!isset($array)) {
            $array = array('[VIEW]' => null);
            $array = array_merge($array,$this->view->getVars());
            if(session_id()) {
            	$array['[SESSION]'] = null;
            	$array = array_merge($array,$_SESSION);
            }
            $array['[PHP]'] = null;
            $included_files = get_included_files();
            sort($included_files);
            $array = array_merge($array, array('[included files]' => $included_files));
            //$array = array_merge($array, array('required files' => get_required_files()));
        }

        echo '<ul class="peak_debug_tree">';
        foreach($array as $param => $value)
        {
            echo '<li>';
            if(is_array($value)) {
                echo '<i>'.$param.'</i> = <strong>array(</strong>';
                $this->display($value);
                echo '<strong>)</strong>';
            }
            else {
                if(is_object($value)) echo '<i>'.$param.'</i> => [@object]';
                elseif(is_resource($value)) echo '<i>'.$param.'</i> => [@resource]';
                elseif(is_null($value))  echo '<strong><i>'.$param.'</i></strong>';
                else echo '<i>'.$param.'</i> => '.strip_tags($value);
            }
            echo '</li>';
        }
        echo '</ul>';
        
    }
    
    public function registry()
    {
    	$object_list = Peak_Registry::getObjectList();
    	echo '<pre class="peak_debug_tree">';
    	foreach($object_list as $obj) {
    		
    		print_r(Peak_Registry::o()->$obj);
    	}
    	echo '</pre>';
    }
    
}