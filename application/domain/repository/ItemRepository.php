<?php
/**
 *	Item repository class.
 *
 *	@author      Raymond van Velzen <raymond@latunyi.com>
 *	@package     LatunyiFramework
 **/

namespace Application\Domain\Repository;

use Application\Domain\Entity\Item;

/**
 * Repository for Item entities.
 */
class ItemRepository extends \System\Core\Repository
{
	/**
	 * Constructor, receives and sets the ItemTable object.
	 * 
	 * @param \Application\Database\ItemTable $itemTable
	 */
	public function __construct(\Application\Database\ItemTable $itemTable)
	{
		$this->table = $itemTable;
	}

	/**
	 * Saves the given item to the database, either as new or existing.
	 * 
	 * @param Item $item
	 * @return int For new items, the last inserted ID; for existing items, the number of affected rows.
	 */
	public function save(Item $item)
	{
		if ($item->id == 0)
		{
			return parent::add($item);
		}
		else
		{
			return parent::update($item);
		}
	}
}