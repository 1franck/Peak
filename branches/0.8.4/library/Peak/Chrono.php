<?php
/** 
 * Manage a global timer and/or multiple timers
 * 
 * @author   Francois Lajoie
 * @version  $Id$
 */
class Peak_Chrono
{
    
    /**
     * Global timer, used by default if not timer name is specified
     * @var array
     */
    private static $_global = array('start' => false, 'end' => false);
    
    /**
     * Timers list
     * @var array
     */
    private static $_timers = array();

    /**
     * Start global timer of a specific timer name if set
     * 
     * @param string|null timer name
     */
    public static function start($timer_name = null)
    {
        if(!isset($timer_name)) {
            self::$_global['start'] = self::getMicrotime();
        }
        else {
            self::$_timers[$timer_name] = array('start' => self::getMicrotime(), 'end' => false);
        }
    }

    /**
     * Stop global timer or a specific timer name if set
     * 
     * @param string|null timer name
     */
    public static function stop($timer_name = null)
    {
        if(!isset($timer_name)) {
            self::$_global['end'] = self::getMicrotime();
        }
        else {
            if(self::timerExists($timer_name)) {
                self::$_timers[$timer_name]['end'] = self::getMicrotime();
            }
        }
    }
    
    /**
     * Check if a timer name exists
     *
     * @param  string $name
     * @return bool
     */
    public static function timerExists($name)
    {
        return array_key_exists($name, self::$_timers);
    }
    
    /**
     * Check if chrono is started
     *
     * @return bool
     */
    public static function isOn($timer_name = null)
    {
        if(!isset($timer_name)) {
            if(self::$_global['start'] === false) return false;
            else return true;
        }
        else {
            if(self::timerExists($timer_name)) {
                if(array_key_exists('start', self::$_timers[$timer_name])) {
                    if(self::$_timers[$timer_name]['start'] !== false) return true;
                    else return false;
                }
                return false;
            }
            return false;
        }   
    }

    /**
     * Get current microtime
     *
     * @return integer
     */
    public static function getMicrotime()
    {
        return microtime(true);
    }

    /**
     * Stop chrono if not ended and return the time elapsed in seconds
     *
     * @param  integer $round
     * @return integer
     */
    public static function get($timer_name = null, $decimal_precision = 2)
    {

        if(self::isOn($timer_name)) {
            
            if(self::timerExists($timer_name)) {
                if(self::$_timers[$timer_name]['end'] === false) self::stop($timer_name);
                $end   = self::$_timers[$timer_name]['end'];
                $start = self::$_timers[$timer_name]['start'];
            }
            else {
                if(self::$_global['end'] === false) self::stop();
                $end   = self::$_global['end'];
                $start = self::$_global['start'];
            }
            return round(($end - $start), $decimal_precision);
            
        }
        else return null;
    }

}