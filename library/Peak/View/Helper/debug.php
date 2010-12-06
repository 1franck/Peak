<?php

/**
 * Debug array display
 *
 * @author  Francois Lajoie
 * @version $Id$
 */
class Peak_View_Helper_Debug
{   
	
    /**
     * Print view and session vars + list of loaded php files * Resursive
     *
     * @param array $array
     */
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
    
    /**
     * Print registry object list
     */
    public function registry()
    {
    	$object_list = Peak_Registry::getObjectList();
    	echo '<pre class="peak_debug_tree">';
    	foreach($object_list as $obj) {
    		
    		print_r(Peak_Registry::o()->$obj);
    	}
    	echo '</pre>';
    }
    
    /**
     * Get current controller content
     *
     * @return string/false
     */
    public function getControllerSource()
	{
		$app = Peak_Registry::o()->app;
		$cfile_name = $app->front->controller->name;
		$cfile = Peak_Core::getPath('controllers').'/'.$cfile_name.'.php';
		if(file_exists($cfile)) {
			$cfile_content = file_get_contents($cfile);
			return $cfile_content;
		}
		else return false;
	}
	
	/**
	 * Get current script view content
	 *
	 * @return string/false
	 */
	public function getScriptSource()
	{
		$app = Peak_Registry::o()->app;
		$sfile_name = $app->front->controller->file;
		$sfile = $app->front->controller->path.'/'.$sfile_name;
		
		if(file_exists($sfile)) {
			$sfile_content = file_get_contents($sfile);
			return $sfile_content;
		}
		else return false;
	}
	
	/**
	 * Get Memory usage
	 *
	 * @return string
	 */
	public function getMemoryUsage()
	{
		$size = memory_get_peak_usage(true);
		$unit = array('b','kb','mb','gb','tb','pb');
		return @round($size/pow(1024,($i=floor(log($size,1024)))),4).' '.$unit[$i];
	}
    
}