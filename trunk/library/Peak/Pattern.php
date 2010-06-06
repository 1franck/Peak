<?php

/**
 * Check different string pattern validation
 * 
 * @author regex gather from many web site over the internet, other from me(francois lajoie)
 *         all method have been tested with simpletest php tests unit
 * 
 * @version 20100511
 *
 */
class Peak_Pattern
{

    public static function text($str,$space = false) {
        if(!empty($str)) {
            if($space) $regex = '/^[a-zA-Z0-9\s]*$/';
            else $regex = '/^[a-zA-Z0-9]*$/';
            return preg_match($regex,trim($str));
        }
        return false;
    }
    
    public static function range($str,$min,$max) {
        if(!is_numeric($str)) return false;
        elseif(round($str) != $str) return false;
        elseif(((int)$str >= $min) && ((int)$str <= $max)) return true;
        else return false;
    }

    public static function day($str) {
        return self::range($str,1,31);
    }
    
    public static function month($str) {
        return self::range($str,1,12);
    }

    public static function year($str) {
        return self::range($str,1,9999);
    }   

    public static function email($str) {
        $regexp = '/^[a-zA-Z0-9_+-]+(\.[a-zA-Z0-9_+-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*$/';
        return preg_match($regexp, trim($str));
    }

    public static function ip($str) {
        $regexp = '/^((1?\d{1,2}|2[0-4]\d|25[0-5])\.){3}(1?\d{1,2}|2[0-4]\d|25[0-5])$/';
        return preg_match($regexp, trim($str));
    }
    
    public static function url($str, $localhost = false) {
        if($localhost) {
            if(in_array($str,array('http://127.0.0.1','http://localhost'))) return true;
        }
        //$regex = '/^[a-zA-Z]+[:\/\/]+[A-Za-z0-9\-_]+\\.+[A-Za-z0-9\.\/%&=\?\-_]+$/i';
        //$regex = '/^((https?|ftp)\:\/\/)(([\w]+:)?\/\/)?(([\d\w]|%[a-fA-f\d]{2,2})+(:([\d\w]|%[a-fA-f\d]{2,2})+)?@)?([\d\w][-\d\w]{0,253}[\d\w]\.)+[\w]{2,4}(:[\d]+)?(\/([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)*(\?(&amp;?([-+_~.\d\w]|%[a-fA-f\d]{2,2})=?)*)?(#([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)?$/';
        $regex = "/^((https?|ftp)\:\/\/)"; // SCHEME
        $regex .= "([a-z0-9+!*(),;?&=\$_.-]+(\:[a-z0-9+!*(),;?&=\$_.-]+)?@)?"; // User and Pass
        $regex .= "([a-z0-9-.]*)\.([a-z]{2,3})"; // Host or IP
        $regex .= "(\:[0-9]{2,5})?"; // Port
        $regex .= "(\/([a-z0-9+\$_-]\.?)+)*\/?"; // Path
        $regex .= "(\?[a-z+&\$_.-][a-z0-9;:@&%=+\/\$_.-]*)?"; // GET Query
        $regex .= "(#[a-z_.-][a-z0-9+\$_.-]*)?$/"; // Anchor


        return preg_match($regex,trim($str));
    }
    
}