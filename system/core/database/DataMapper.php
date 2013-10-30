<?php
/**
 *	DataMapper base class.
 *
 *	@author      Raymond van Velzen <raymond@latunyi.com>
 *	@package     LatunyiFramework
 **/

namespace System\Core\Database;

/**
 * Base class for datamapper classes.
 * Maps entity properties to database fields and vice versa.
 */
class DataMapper
{
	/**
	 * Array with entity properties > database fields
	 * @var array
	 */
	protected $map;

	/**
	 * Entity class
	 * @var string
	 */
	protected $entityClass;

	/**
	 * Constructor, sets the map and entity class.
	 *
	 * @param array $map
	 * @param string $entityClass
	 * @throws \Exception
	 */
	public function __construct($map, $entityClass)
	{
		if (!isset($map) || !is_array($map) || empty($map))
		{
			throw new \Exception('Invalid map');
		}
		$this->map = $map;

		// Prefix entity class with namespace
		$entityClass = 'Application\Domain\Entity\\' . $entityClass;

		if (!class_exists($entityClass))
		{
			throw new \Exception('Entity class ' . $entityClass . ' not found');
		}
		$this->entityClass = $entityClass;
	}

	/**
	 * Takes an entity and maps its data according to the map,
	 * returning an array.
	 *
	 * @param \System\Core\Entity $entity
	 * @throws \Exception
	 * @return array
	 */
	public function mapEntityToData(\System\Core\Entity $entity)
	{
		if (!is_object($entity) || get_class($entity) != $this->entityClass)
		{
			throw new \Exception('Invalid entity');
		}

		return $this->mapData($entity);
	}

	public function mapEntitiesToData($list)
	{
		if (!is_array($list))
		{
			throw new \Exception('No list of entities was supplied');
		}

		foreach ($list as &$item)
		{
			$item = $this->mapEntityToData($item);
		}
		unset($item);

		return $list;
	}

	/**
	 * Takes an array and returns an entity with its data.
	 *
	 * @param array $data
	 * @throws \Exception
	 * @returns \System\Core\Entity
	 */
	public function mapDataToEntity($data)
	{
		if (!is_array($data))
		{
			throw new \Exception('No data was supplied');
		}

		return new $this->entityClass($this->mapData($data, true));
	}

	/**
	 * Iterates the given list of data and maps the data to entities.
	 *
	 * @param array $list
	 * @return array
	 */
	public function mapDataToEntities($list)
	{
		foreach ($list as &$data)
		{
			$data = $this->mapDataToEntity($data);
		}
		unset($data);

		return $list;
	}

	/**
	 * Maps data from entity to database or reverse, as array.
	 *
	 * @param array $data
	 * @param bool $reverse
	 * @return array
	 */
	private function mapData($data, $reverse = false)
	{
		$data = (array)$data;

		$map = $this->map;
		if ($reverse)
		{
			$map = array_flip($this->map);
		}

		foreach ($data as $key => $value)
		{
			if (isset($map[$key]))
			{
				$data[$map[$key]] = $data[$key];
				unset($data[$key]);
			}
		}

		return $data;
	}
}