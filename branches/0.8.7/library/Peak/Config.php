<?php
/**
 * Variables registry for configs stuff
 * 
 * @author  Francois Lajoie
 * @version $Id$
 * @uses    IteratorAggregate, Countable
 */
class Peak_Config implements IteratorAggregate, Countable
{
	/**
	 * Contains all variables
	 * @var array
	 */
	protected $_vars = array();
	
	/**
	 * Set array of data optionnaly
	 *
	 * @param array $vars
	 */
	public function __construct($vars = null)
	{
	    if(is_array($vars)) $this->setVars($vars);
	}

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
     * @param  string $name
     * @return misc   Will return null if variable keyname is not found
     */
    public function &__get($name)
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

    /**
     * Get all variables
     *
     * @return array
     */
    public function getVars()
    {
    	return $this->_vars;
    }
    
    /**
     * Set an array into object
     *
     * @param array $array
     */
    public function setVars($array)
    {
    	$this->_vars = $array;
    }
    
    /**
     * Flush all variable stored
     */
    public function flushVars()
    {
        $this->_vars = array();
    }
    
    /**
	 * Merge two arrays recursively overwriting the keys in the first array
	 * if such key already exists
	 *
	 * @param  mixed $a Left array to merge right array into
	 * @param  mixed $b Right array to merge over the left array
	 * @return mixed
	 */
	public function arrayMergeRecursive($a, $b)
	{
		// merge arrays if both variables are arrays
		if (is_array($a) && is_array($b)) {
			// loop through each right array's entry and merge it into $a
			foreach ($b as $key => $value) {
				if (isset($a[$key])) {
					$a[$key] = $this->arrayMergeRecursive($a[$key], $value);
				} else {
					if($key === 0) $a= array(0 => $this->arrayMergeRecursive($a, $value));
					else $a[$key] = $value;
				}
			}
		} 
		else $a = $b; // one of values is not an array

		return $a;
	}
}