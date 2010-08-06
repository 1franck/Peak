<?php

/**
 * Peak Config variables registry
 * 
 * @author  Francois Lajoie
 * @version $Id$
 * @uses    IteratorAggregate, Countable
 *
 */
class Peak_Config implements IteratorAggregate, Countable
{
	
	private $_vars = array();
    
    public function __set($name,$val)
    {
    	$this->_vars[$name] = $val;
    }
    
    public function __get($name)
    {
    	if(isset($this->_vars[$name])) return $this->_vars[$name];
    	else return null;
    }
    
    public function __isset($name)
    {
    	return (isset($this->_vars[$name])) ? true : false;
    }
    
    public function __unset($name)
    {
    	if(isset($this->_vars[$name])) unset($this->_vars[$name]);
    }
    
    /**
	 * Create iterator for $config
	 *
	 * @return iterator
	 */
	public function getIterator()
    {
        return new ArrayIterator($this->_vars);
    }
    
    /**
     * Implement Countable func
     *
     * @return integer
     */
    public function count()
    {
    	return count($this->_vars);
    }
	
}