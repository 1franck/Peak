<?php
/**
 * Basic active record using Zend_Db as data source provider
 *
 * @uses     Peak_Model_Zendatable
 * 
 * @author   Francois Lajoie
 * @version  $Id$
 */
class Peak_Model_ActiveRecord
{
    /**
     * Entity data
     * @var array
     */
    protected $_data = [];

    /**
     * Table model
     * @var object
     */
    protected $_tbl;

    /**
     * The model class name
     * @var string
     */
    protected $_model_classname;

    /**
     * Can we overwrite $_data ?
     * @var boolean
     */
    protected $_readonly = false;

    /**
     * This flag tell if the record is a valid existing record of the model
     * @var boolean
     */
    private $_exists = false;

    /**
     * Constructor
     * 
     * @param array|integer|null $data
     * @param null|string        $model if specified, $model is used instead of $this->_model_classname
     */
    public function __construct($data = null, $model = null)
    {
        
        // load model object
        
        if(isset($model)) $classname = $model;
        elseif(!empty($this->_model_classname)) {
            $classname = $this->_model_classname;
        }
        else {
            throw new Exception('ActiveRecord: No model found for '.__CLASS__);
        }

        if(class_exists($classname)) {

            $this->_tbl = new $classname();

            if(!($this->_tbl instanceof Peak_Model_Zendatable)) {
                throw new Exception('ActiveRecord: Model class '.$classname.' is not an instance of Peak_Model_Zendatable in '.__CLASS__);
            }
        }
        else {
            throw new Exception('ActiveRecord: Model class '.$classname.' found for '.__CLASS__);
        }

        // load data
        
        if(is_array($data)) {
            $this->setData($data);
        }
        elseif(is_string($data) || is_numeric($data)) {
            $this->_data = $this->_tbl->findId($data);
        }
        elseif(!is_null($data)) {
            throw new Exception('ActiveRecord: Invalid var format for constructor $data in '.__CLASS__.'. Only array or integer is supported');
        }

        // test the record existence
        
        $pk = $this->_tbl->getPrimaryKey();
        if(array_key_exists($pk, $this->_data)) {

            $test = $this->_tbl->findId($this->_data[$pk]);
            $this->_exists = (empty($test)) ? false : true;
        }
    }

    /**
     * Retreive a column from $_data
     *
     * @param  string $key
     * @return string|null
     */
    public function __get($key)
    {
        if(array_key_exists($key, $this->_data)) return $this->_data[$key];
        else return null;
    }

    /**
     * Set/Overwrite a key in $_data IF $_readonly is true
     * 
     * @param string $key  
     * @param string $name 
     */
    public function __set($key, $name)
    {
        if($this->_readonly === false) $this->_data[$key] = $name;
    }

    /**
     * Isset data keyname
     * 
     * @param  string  $key
     * @return boolean     
     */
    public function __isset($key)
    {
        return array_key_exists($key, $this->_data);
    }

    /**
     * Set data to this entities
     * 
     * @param array   $array 
     * @param boolean $safe  if true, array must be compliant to a record normally used in database model (based on the primary key)
     */
    public function setData($array, $safe = true)
    {
        if(!$safe) {
            $this->_data = $array;
            return;
        }

        if(empty($array) || !array_key_exists($this->_tbl->getPrimaryKey(), $array)) {
            throw new Exception('Models Entities: data submited to setData() is not a valid record of '.__CLASS__);    
        }
        else $this->_data = $array;
    }

    /**
     * Return the _data array
     *
     * @return array
     */
    public function getData()
    {
        return $this->_data;
    }

    /**
     * Check if the entities is valid(exists) and $_data is populated
     * 
     * @return boolean
     */
    public function exists()
    {
        return $this->_exists;
    }

    /**
     * Save data using the $_tbl model object
     */
    public function save()
    {
        $pk = $this->_tbl->getPrimaryKey();

        // force save
        if(!$this->exists()) {

            $new_id           = $this->_tbl->save($this->_data, true);
            $this->_data[$pk] = $new_id;
            $this->_exists    = true;
        }
        else {
            $this->_tbl->save($this->_data);
        }
    }

    /**
     * Delete the record
     * @return bool
     */
    public function delete()
    {
        // don't exists, skip this
        if(!$this->exists()) return false;

        $pk     = $this->_tbl->getPrimaryKey();
        $where  = $this->_tbl->quoteInto($pk.' = ?', $this->_data[$pk]);
        $result = $this->_tbl->delete($where);

        if($result === true) {
            $this->_exists = false;
        }

        return $result;
    }
}