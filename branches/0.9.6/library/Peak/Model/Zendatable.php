<?php
/**
 * Peak_Model_Zendatable
 * Base for db tables using Zend_Db
 * 
 * @author  Francois Lajoie
 * @version $Id$
 */
abstract class Peak_Model_Zendatable extends Zend_Db_Table_Abstract
{
    /**
	 * Count from the latest fetch
	 * @var integer
	 */
	public $last_count = null;
    
    /**
     * Adapter db link
     * @var object
     */
    protected $_db;
	
	/**
	 * Pagination object
	 * @var object
	 */
	protected $_paging;

    /**
	 * Set db default adpater
	 */
	public function __construct()
	{
		$this->_db = $this->getDefaultAdapter();
	}

	/**
	 * Get table columns
	 *
	 * @return array
	 */
	public function getColumns()
	{
		return $this->_getCols();
	}

	/**
	 * Delete primary key(s)
	 *
	 * @param  integer|array $ids
	 * @return integer
	 */
	public function deleteId($primary_keys)
	{
	    $id_key = $this->getPrimaryKey();
	        
	    if(is_array($primary_keys)) $where = $this->_db->quoteInto($id_key.' IN(?)', $primary_keys);
	    else $where = $this->_db->quoteInto($id_key.' = ?', $primary_keys);

	    return $this->delete($where);
	}

	/**
	 * Check if a specific key exists in table
	 *
	 * @param  misc       $val
	 * @param  string     $key
	 * @param  bool       $return_row if true, the method will return the row found if any instead of returning true
	 * @return bool|array
	 */
	public function exists($val, $key = null, $return_row = false)
	{
	    if(!isset($key)) $key = $this->getPrimaryKey();

	    if(!$return_row) {
	    	
		    $select = $this->select()->from($this->getSchemaName(), $key)
		                             ->where($this->_db->quoteInto($key.' = ?',$val));                     
		    
		    $result = $this->fetchRow($select);
		    
		    return (is_null($result)) ? false : true;
	    }
	    else {

	    	$select = $this->select()->from($this->getSchemaName(), '*')
		                             ->where($this->_db->quoteInto($key.' = ?',$val));                     
		    
		    $result = $this->fetchRow($select);
		    
		    return (is_null($result)) ? false : $result;
	    }
	    
	}

	/**
	 * Find something by its primary key. If $_object_mapper exists
	 * will return data(s) row(s) in form of object mapper
	 *
	 * @param  array|integer $ids
	 * @return array
	 */
	public function findId($ids)
	{
	    $result = parent::find($ids);
	    $this->last_count = $result->count();
	    $data = $result->toArray();
	    
	    if(isset($this->_object_mapper)) {
	        $object_mapper = $this->_object_mapper;
	        foreach($data as $i => $row) {
	            $data[$i] = new $object_mapper($row);
	        }
	    }
	    
	    if(count($data) == 1) $data = $data[0];
	    
	    return $data;
	}

	/**
	 * Count row from a table
	 * 
     * $where ex:
     *  array('id','IN',array(1,2,5))
     *  array('id','=',2)
     *  'id = "2"'
     *  
	 * @param  misc   
	 * @return integer
	 */
	public function count($where = null)
	{
		$key_to_count = (is_array($where) && isset($where[0])) ? $where[0]: $this->getPrimaryKey();
		
	    $select = $this->select()->from($this->getSchemaName(), array('count('.$key_to_count.') as itemcount'));
	    
	    if(is_array($where)) {
	        $where = $this->_db->quoteInto($where[0].' '.$where[1].' (?)',$where[2]);
	        $select->where($where);
	    }
		elseif(!empty($where)) $select->where($where);

        return $this->_db->fetchOne($select);
	}
    
    /**
     * Return the database schema if specified and table name together
     * ex:
     * @use    $_schema and $_name
     * @return string
     */
    public function getSchemaName()
    {
        return (!empty($this->_schema)) ? $this->_schema.'.'.$this->_name : $this->_name;
    }

	/**
	 * Get default primary key string name
	 *
	 * @return string
	 */
	public function getPrimaryKey()
	{
	    if(is_array($this->_primary)) return $this->_primary[1];
	    else return $this->_primary;
	}

	/**
	 * Remove unknow column keyname form an array
	 *
	 * @param  array $data
	 * @return array
	 */
	public function cleanArray($data)
	{
		//just in case zend_db have not describe table yet
		if(empty($this->_metadata)) $this->_getCols();

	    //remove unknow table key
	    foreach($data as $k => $v) {
	        if(!array_key_exists($k, $this->_metadata)) unset($data[$k]);
	    }
	    return $data;
	}

	/**
	 * Insert/Update depending of the presence of primary key or not
	 * Support only one primary key
	 *
	 * @param  array $data
	 * @return null|integer
	 */
	public function save($data)
	{
	    $data = $this->cleanArray($data);
	    
	    $pm = $this->getPrimaryKey();

	    if(array_key_exists($pm, $data) && !empty($data[$pm])) {

	    	//before update
	    	$this->beforeUpdate($data);

	        //update
	        $where = $this->_db->quoteInto($pm.' = ?',$data[$pm]);
	        $this->_db->update($this->getSchemaName(), $data, $where);

	        //after update
	        $this->afterUpdate($data);

			return $data[$pm];
	    }
	    else {

	    	//before insert
	    	$this->beforeInsert($data);

	        //insert
	        $this->_db->insert($this->getSchemaName(), $data);
	        $id = $this->_db->lastInsertId();

	        //after insert
	        $this->afterInsert($data, $id);

	        //return the last insert id
	        return $id;
	    }
	}

	/**
	 * This method allow inserting/updating multiple row using transaction
	 *
	 * @uses   method save()
	 * 
	 * @param  array $multiple_data
	 * @return array|object Return ids inserted/updated. If a query fail, it will return the exception object
	 */
	public function saveTransaction($multiple_data)
	{
		$ids = array();
		$this->_db->beginTransaction();

		try {

			// insert/update each data set with method save()
			foreach($multiple_data as $data) $ids[] = $this->save($data);

			// commit
			$this->_db->commit();
		}
		catch(Exception $e) {

			//rollback the changes
			$this->_db->rollback();

			// return exception object
			return $e;
		}

		return $ids;
	}

	/**
	 * Execute stuff before insert data with method save()
	 * Do nothing by default. Can be overloaded by child class.
	 * 
	 * @param  array $data Data to be insert
	 */
	public function beforeInsert(&$data) {}

	/**
	 * Execute stuff after insert data with method save()
	 * Do nothing by default. Can be overloaded by child class.
	 * 
	 * @param  array          $data Data inserted
	 * @param  string|interer $id   Represent lastInsertId() if any. 
	 *                              Passed by reference, you can modify what "insert" save() method will return.
	 */
	public function afterInsert($data, &$id) {}

	/**
	 * Execute stuff before update data with method save()
	 * Do nothing by default. Can be overloaded by child class.
	 * 
	 * @param  array $data Data to be updated
	 */
	public function beforeUpdate(&$data) {}

	/**
	 * Execute stuff after update data with method save()
	 * Do nothing by default. Can be overloaded by child class.
	 * 
	 * @param  array          $data Data update
	 * @param  string|interer $id   Represent lastInsertId() if any. 
	 *                              Passed by reference, you can modify what "insert" save() method will return.
	 */
	public function afterUpdate(&$data) {}

	/**
	 * Shortcut for $_db->query()
	 *
	 * @param  string   $query
	 * @param  array    $bind
	 * @return resource
	 */
	protected function query($query, $bind = array())
	{
		return $this->_db->query($query, $bind);
	}
	
	/**
	 * Shortcut for $_db->quote()
	 * @see Zend_Db_Adapter_Abstract
	 */
	public function quote($value, $type = null)
	{
		return $this->_db->quote($value, $type);
	}
	
	/**
	 * Shorcut for $_db->quoteInto()
	 * @see Zend_Db_Adapter_Abstract
	 */
	public function quoteInto($text, $value, $type = null, $count = null)
	{
		return $this->_db->quoteInto($text, $value, $type, $count);
	}
	
	/**
	 * Shortcut for $_db->quoteIdentifier()
	 * @see Zend_Db_Adapter_Abstract
	 */
	public function quoteIdentifier($ident, $auto=false)
	{
		$this->_db->quoteIdentifier($ident, $auto);
	}
	
	/**
	 * Shortcut for $_db->quoteColumnAs()
	 * @see Zend_Db_Adapter_Abstract
	 */
	public function quoteColumnAs($ident, $alias, $auto=false)
	{
		$this->_db->quoteColumnAs($ident, $alias, $auto);
	}
	
	/**
	 * Instanciate and return instance of Peak_Model_Pagination
	 * @see Zend_Db_Adapter_Abstract
	 */
	public function paging()
	{
		if(!isset($this->_paging)) $this->_paging = new Peak_Model_Pagination($this);
		return $this->_paging;
	}
}