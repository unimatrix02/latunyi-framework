<?php
namespace Application\Domain\Repository;

/**
 * Repository for Type entities.
 */
class TypeRepository extends \System\Core\Repository
{
	public function __construct(\Application\Database\TypeTable $typeTable)
	{
		$this->table = $typeTable;
	}
	
}