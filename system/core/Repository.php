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
	 * @var Database\Table
	 */
	protected $table;

	/**
	 * DataMapper
	 * @var Database\DataMapper
	 */
	protected $dataMapper;

	/**
	 * Constructor, receives and sets the Table and DataMapper objects.
	 *
	 * @param Database\Table $table
	 * @param Database\DataMapper $dataMapper
	 */
	public function __construct(Database\Table $table, Database\DataMapper $dataMapper)
	{
		$this->table = $table;
		$this->dataMapper = $dataMapper;
	}

	/**
	 * Returns all rows from the table.
	 *
	 * @param string $orderBy
	 * @param int $limit
	 * @param int $offset
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
	 * @param Database\QueryParams $params
	 * @return mixed
	 */
	public function getSimpleList($valueField, $keyField = null, Database\QueryParams $params = null)
	{
		return $this->table->getSimpleList($valueField, $keyField = null, $params = null);
	}

	/**
	 * Adds a new entity to the database.
	 *
	 * @param \System\Core\Entity $entity
	 * @param bool $replace     Replace instead of insert
	 * @return int Last inserted ID (for auto incremented primary keys)
	 */
	public function add($entity, $replace = false)
	{
		$data = $this->dataMapper->mapEntityToData($entity);
		return $this->table->insertRow($data, $replace);
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