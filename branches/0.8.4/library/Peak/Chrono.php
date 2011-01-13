<?php

/**
 * Peak Chrono
 * 
 * @author   Francois Lajoie
 * @version  $Id$
 */
class Peak_Chrono
{
    public static $start = false;
    public static $end = false;

    /**
     * Start the chrono
     */
    public static function start()
    {
        self::$start = self::get_microtime();
    }

    /**
     * Stop the chrono
     */
    public static function end()
    {
        self::$end = self::get_microtime();
    }
    
    /**
     * Check if chrono is started
     *
     * @return bool
     */
    public static function is_on()
    {
        if(self::$start === false) return false;
        else return true;
    }

    /**
     * Get current microtime
     *
     * @return integer
     */
    public static function get_microtime()
    {
        list( $usec, $sec ) = explode( ' ', microtime() );
        $time = (float) $usec + (float) $sec;
        return $time;
    }

    /**
     * Stop chrono if not ended and return the time elapsed
     *
     * @param  integer $round
     * @return integer
     */
    public static function get_result($round = 2)
    {
        if(self::is_on()) {
            if(self::$end === false) self::end();
            return round((self::$end - self::$start), $round);
        }
        else return null;
    }

}