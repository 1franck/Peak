<?php
/**
 * Example Class1
 *
 * Long description text...
 *
 * @author  FooBar
 * @version 1.0
 */
abstract class class1 implements Countable
{
    /**
     * Constant example
     */
    const MY_CONSTANT1 = 'foobar';
    
    /**
     * Constant example
     */
    const MY_CONSTANT2 = 'bar foo';
    
    /**
     * Misc array of data
     * 
     * Very long description
     * 
     * @var array
     */
    protected $_misc_data = array('key1' => 'val1',
                                  'key2' => 'val2');
    
    /**
     * Return the count of $_misc_data 
     *
     * @return integer
     */
    public function count()
    {
        return count($this->_misc_data);
    }
}