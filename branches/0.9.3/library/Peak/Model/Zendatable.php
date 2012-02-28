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
	 * Pagination object
	 * @var object
	 */
	protected $_pagination;

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
	 * @param  misc $val
	 * @param  string $key
	 * @return bool
	 */
	public function exists($val, $key = null)
	{
	    if(!isset($key)) $key = $this->getPrimaryKey();
	    
	    $select = $this->select()->from($this->_name, $key)
	                             ->where($this->_db->quoteInto($key.' = ?',$val));                     
	    
	    $result = $this->fetchRow($select);
	    
	    return (is_null($result)) ? false : true;
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
	 * @param  array|null ex: array('id','IN',array(1,2,5)) or array('id','=',2)
	 * @return integer
	 */
	public function count($where = null)
	{
		$key_to_count = (isset($where[0])) ? $where[0]: $this->getPrimaryKey();
		
	    $select = $this->select()->from($this->_name, array('count('.$key_to_count.') as itemcount'));
	    
	    if(is_array($where)) {
	        $where = $this->_db->quoteInto($where[0].' '.$where[1].' (?)',$where[2]);
	        $select->where($where);
	    }
	    	    
        $rows = $this->fetchAll($select);      
        return($rows[0]->itemcount);
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
	        if(!isset($this->_metadata[$k])) unset($data[$k]);
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

	    if(isset($data[$pm]) && !empty($data[$pm])) {
	        //update
	        $where = $this->_db->quoteInto($pm.' = ?',$data[$pm]);
	        $this->_db->update($this->_name, $data, $where);
			return $data[$pm];
	    }
	    else {
	        //insert
	        $this->_db->insert($this->_name, $data);
	        return $this->_db->lastInsertId();
	    }
	}

	/**
	 * Shorcut for $_db->query()
	 *
	 * @param  string   $query
	 * @return resource
	 */
	public function query($query)
	{
		return $this->_db->query($query);
	}
	
	/**
	 * Shorcut for $_db->quote()
	 * @see Zend_Db_Adapter_Abstract
	 */
	public function quote($value, $type = null)
	{
		return $this->_db->query($query);
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
	 * Shorcut for $_db->quoteIdentifier()
	 * @see Zend_Db_Adapter_Abstract
	 */
	public function quoteIdentifier($ident, $auto=false)
	{
		$this->_db->quoteIdentifier($ident, $auto);
	}
	
	/**
	 * Shorcut for $_db->quoteColumnAs()
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
	public function pagination()
	{
		if(!isset($this->_pagination)) $this->_pagination = new Peak_Model_Pagination($this);
		return $this->_pagination;
	}
}