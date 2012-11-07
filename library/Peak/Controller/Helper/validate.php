<?php
/**
 * Validate Filters extension wrapper
 * @see Peak_Filters_Advanced
 *
 * @author  Francois Lajoie
 * @version $Id$
 */
class Peak_Controller_Helper_Validate extends Peak_Filters_Advanced
{

    public function __call($method, $args = null)
    {
        if($this->_filterExists($method)) {
            return call_user_func_array(array($this, $this->_filter2method($method)), $args);
        }
    }

}