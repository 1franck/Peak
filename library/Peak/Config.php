<?php

class Peak_Config implements IteratorAggregate, Countable
{
	
	private $config = array();
    
    public function __set($name,$val)
    {
    	$this->config[$name] = $val;
    }
    
    public function __get($name)
    {
    	if(isset($this->config[$name])) return $this->config[$name];
    	else return null;
    }
    
    public function __isset($name)
    {
    	return (isset($this->config[$name])) ? true : false;
    }
    
    public function __unset($name)
    {
    	if(isset($this->config[$name])) unset($this->config[$name]);
    }
    
    /**
	 * Create iterator for $config
	 *
	 * @return iterator
	 */
	public function getIterator()
    {
        return new ArrayIterator($this->config);
    }
    
    /**
     * Implement Countable func
     *
     * @return integer
     */
    public function count()
    {
    	return count($this->config);
    }

	
	
	
}