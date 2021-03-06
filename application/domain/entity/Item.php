<?php
/**
 *	Item entity class.
 *
 *	@author      Raymond van Velzen <raymond@latunyi.com>
 *	@package     LatunyiFramework
 **/

namespace Application\Domain\Entity;

/**
 * Item entity.
 */
class Item extends \System\Core\Entity
{
	/**
	 * ID, autoincremented, 0 = new item
	 * @var int
	 */
	public $id = 0;
	
	/**
	 * Name
	 * @var string
	 */
	public $name;
	
	/**
	 * Value, decimal amount
	 * @var float
	 */
	public $value;
	
	/**
	 * Type ID, foreign key, refers to ID in Type
	 * @var int
	 */
	public $typeId;
	
	/**
	 * Start date
	 * @var string
	 */
	public $startDate;
	
	/**
	 * End date
	 * @var string
	 */
	public $endDate;
}
