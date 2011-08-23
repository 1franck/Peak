<?php
/**
 * Get misc infos about current request
 *
 * @author  Francois Lajoie
 * @version $Id$
 */
class Peak_Controller_Helper_Request
{
    /**
     * Check if request is ajax
     * Work with jQuery, not tested for other framework
     */
    public function isAjax()
    {
        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            return true;
        }
        else return false;
    }
    
    /**
     * Get user ip adress
     *
     * @author Yang Yang at http://www.kavoir.com
     */
    public function getIp()
    {
        foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    if (filter_var($ip, FILTER_VALIDATE_IP) !== false) {
                        return $ip;
                    }
                }
            }
        }
    }

}