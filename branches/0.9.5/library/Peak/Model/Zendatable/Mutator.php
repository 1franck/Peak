<?php
/**
 * Peak_Model_Zendatable_Mutator
 * 
 * This class is almost the same as Peak_Model_Zendatable BUT :
 * - Class is not abstract so it can be used directly
 * - Table name, primary and schema name can be changed on the fly
 * 
 * @author  Francois Lajoie
 * @version $Id$
 */
class Peak_Model_Zendatable_Mutator extends Peak_Model_Zendatable
{
    
    /**
     * Constructor
     */
    public function __construct($tablename = null, $primary = null, $schema = null)
    {
        $this->change($tablename, $primary, $schema );
        parent::__construct();
    }
    
    /** 
     * Change the table, primary key and schema name(database name)
     *
     * @param  string|null $tablename
     * @param  string|null $primary
     * @param  string|null $schema
     * @return object      $this
     */
    public function change($tablename = null, $primary = null, $schema = null)
    {
        if(isset($tablename)) $this->_name    = $tablename;
        if(isset($primary))   $this->_primary = $primary;
        if(isset($schema))    $this->_schema  = $schema;
        
        return $this;
    }
}