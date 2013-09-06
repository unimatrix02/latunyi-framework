<?php
/**
 *	Type repository class.
 *
 *	@author      Raymond van Velzen <raymond@latunyi.com>
 *	@package     LatunyiFramework
 **/

namespace Application\Domain\Repository;

/**
 * Repository for Type entities.
 */
class TypeRepository extends \System\Core\Repository
{
	/**
	 * Constructor, receives and sets TypeTable object.
	 * 
	 * @param \Application\Database\TypeTable $typeTable
	 */
	public function __construct(\Application\Database\TypeTable $typeTable)
	{
		$this->table = $typeTable;
	}
	
}