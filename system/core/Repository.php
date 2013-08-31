<?php
namespace System\Core;

/**
 * Base class for entity repositories.
 */
class Repository
{
	/**
	 * Table object for retrieving/storing entities.
	 * @var \System\Core\Database\Table
	 */
	protected $table;

	/**
	 * Returns all rows from the table.
	 * 
	 * @param string $orderBy
	 * @param string $limit
	 * @param string $offset
	 * @return array
	 */
	public function getAll($orderBy = '', $limit = 0, $offset = 0)
	{
		return $this->table->getAllRows($orderBy, $limit, $offset);
	}

	/**
	 * Returns a single entity by primary key.
	 * 
	 * @param mixed $id
	 * @return object
	 */
	public function getEntity($id)
	{
		return $this->table->getRow($id);
	}
	
	/**
	 * Returns a simple key/value list.
	 * 
	 * @param string $valueField
	 * @param string $keyField
	 * @param QueryParams $params
	 */
	public function getSimpleList($valueField, $keyField = null, QueryParams $params = null)
	{
		return $this->table->getSimpleList($valueField, $keyField = null, $params = null);
	}
}