<?php

/**
 * Chorno
 * 
 * @author   Francois Lajoie
 * @version  $Id$
 */
class Peak_Chrono
{
    public static $start = false;
    public static $end = false;

    public static function start()
    {
        self::$start = self::get_microtime();
    }

    public static function end()
    {
        self::$end = self::get_microtime();
    }
    
    public static function is_on()
    {
        if(self::$start === false) return false;
        else return true;
    }

    public static function get_microtime()
    {
        list( $usec, $sec ) = explode( ' ', microtime() );
        $time = (float) $usec + (float) $sec;
        return $time;
    }

    public static function get_result($round = 2)
    {
        if(self::is_on()) {
            if(self::$end === false) self::end();
            return round((self::$end - self::$start), $round);
        }
        else return null;
    }

}