<?php
/**
 * Example Class2
 *
 * Long description text...
 *
 * @author  BarFoo
 * @version 2.5
 */
class class2 extends class1
{
    /**
     * Name
     * @var string
     */ 
    protected $_name;
    
    /**
     * Set name
     * 
     * Long description text...
     *
     * @param string $name
     */ 
    public function setName($name)
    {
        $this->_name = $name;
    }
    
    /**
     * Get name
     *
     * @return string
     */ 
    public function getName()
    {
        return $this->_sanitizeName();
    }
    
    /**
     * Sanitize name
     *
     * @return string
     */
    protected function _sanitizeName($name)
    {
        return strip_tags($name);
    }
}