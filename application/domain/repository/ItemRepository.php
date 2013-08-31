<?php
namespace Application\Domain\Repository;

/**
 * Repository for Item entities.
 */
use Application\Domain\Entity\Type;

class ItemRepository extends \System\Core\Repository
{
	public function __construct(\Application\Database\ItemTable $itemTable)
	{
		$this->table = $itemTable;
	}
	
}