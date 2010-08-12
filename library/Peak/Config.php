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
    
	/**
	 * Set a new variable
	 *
	 * @param string $name
	 * @param misc   $val
	 */
    public function __set($name,$val)
    {
    	$this->_vars[$name] = $val;
    }
    
    /**
     * Get a variable
     *
     * @param string $name
     * @return  misc Will return null if variable keyname is not found
     */
    public function __get($name)
    {
    	if(isset($this->_vars[$name])) return $this->_vars[$name];
    	else return null;
    }
    
    /**
     * Isset varaible
     *
     * @param  string $name
     * @return bool
     */
    public function __isset($name)
    {
    	return (isset($this->_vars[$name])) ? true : false;
    }
    
    /**
     * Unset variable
     *
     * @param string $name
     */
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