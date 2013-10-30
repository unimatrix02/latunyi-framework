<?php
/**
 *	Entity repository base class.
 *
 *	@author      Raymond van Velzen <raymond@latunyi.com>
 *	@package     LatunyiFramework
 **/

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
	 * DataMapper
	 * @var \System\Core\Database\DataMapper
	 */
	protected $dataMapper;

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
		$data = $this->table->getAllRows($orderBy, $limit, $offset);
		return $this->dataMapper->mapDataToEntities($data);
	}

	/**
	 * Returns a single entity by primary key.
	 * 
	 * @param mixed $id
	 * @return object
	 */
	public function get($id)
	{
		$data = $this->table->getRow($id);
		return $this->dataMapper->mapDataToEntity($data);
	}

	/**
	 * Returns a simple key/value list.
	 *
	 * @param string $valueField
	 * @param string $keyField
	 * @param QueryParams $params
	 * @return mixed
	 */
	public function getSimpleList($valueField, $keyField = null, QueryParams $params = null)
	{
		return $this->table->getSimpleList($valueField, $keyField = null, $params = null);
	}
	
	/**
	 * Adds a new entity to the database.
	 * 
	 * @param \System\Core\Entity $entity
	 * @return int Last inserted ID (for auto incremented primary keys)
	 */
	public function add($entity)
	{
		$data = $this->dataMapper->mapEntityToData($entity);
		return $this->table->insertRow($data);
	}

	/**
	 * Updates an existing entity in the database.
	 * 
	 * @param \System\Core\Entity $entity
	 * @return int Number of affected rows
	 */
	public function update($entity)
	{
		$data = $this->dataMapper->mapEntityToData($entity);
		return $this->table->updateRow($data);
	}

	/**
	 * Removes the entity with the given ID.
	 *
	 * @param int $entityId
	 * @return int Number of affected rows
	 */
	public function remove($entityId)
	{
		return $this->table->deleteRow($entityId);
	}
}