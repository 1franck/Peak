<?php

/**
 * Peak_Core_Configs object extension 
 *
 */
class Peak_Core_Configs
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
    
            
    /**
     * Check different config @todo move to a core extension
     *
     * @return array
     */
    public function check()
    {
        $warnings = array();
        
        /* check DEV_MODE */
        if((defined('DEV_MODE')) && (DEV_MODE === true)) { 
            $warnings[] = 'DEV_MODE is enabled!';
        }
        
        //@deprecated
        /* check W_LOGIN and W_PASS */
        /*
        if(!defined('W_LOGIN')) $warnings[] = 'W_LOGIN config doesn\'t exists!';
        elseif(W_LOGIN === '') $warnings[] = 'W_LOGIN config found but empty';
        else {
            if(!defined('W_PASS')) $warnings[] = 'W_PASS config doesn\'t exists!';
            elseif(W_PASS === '') $warnings[] = 'W_PASS config found but empty';
        }
        
        if(empty($warnings)) $warnings = null;
        */
        return $warnings;
    }
    
    
}