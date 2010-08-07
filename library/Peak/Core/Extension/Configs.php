<?php

/**
 * Peak_Core_Extension_Configs
 * 
 * @author  Francois Lajoie
 * @version $Id$
 */
class Peak_Core_Extension_Configs
{
    
    /**
     * Parse constants of file configs.php
     *
     * @return array
     */
    public function get($file, $nologin = false)
    {
        $lines = file($file);
        $configs_vars = array();
        foreach($lines as $line_num => $line)
        {
            if(preg_match('.^(define\().',ltrim($line)))
            {
                $temp = explode(';',$line);
                $temp[0] = str_replace(array('define(',')'),'',$temp[0]);
                $define = explode(',',$temp[0]);
                
                $param = str_replace(array('"','\''),'',$define[0]);
                $value = $define[1];
                
                $info = (isset($temp[1])) ? str_replace(array('#( )','#(!)','#(X)'),'',$temp[1]) : '?';                
                
                $configs_vars[$line_num] = array('original_line' => trim($line),
                                                 'param' => $param,
                                                 'value' => $value,
                                                 'line_num'  => $line_num,
                                                 'eval'  => constant($param),
                                                 'info'  => trim($info));
                                                 
                if(($nologin) && (($param === 'APP_LOGIN_NAME') || ($param === 'APP_LOGIN_PASS') )) {
                    unset($configs_vars[$line_num]);
                }
                                                 
            }
        }
        return $configs_vars;
    }    
}