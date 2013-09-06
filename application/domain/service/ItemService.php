<?php
/**
 *	Item service class.
 *
 *	@author      Raymond van Velzen <raymond@latunyi.com>
 *	@package     LatunyiFramework
 **/

namespace Application\Domain\Service;

use System\Core\Service;
use Application\Domain\Entity\Item;
use Application\Domain\Entity\ItemValidator;

/**
 * Class that provides additional operations not covered by Repository.
 */
class ItemService extends Service
{
	/**
	 * ItemValidator object
	 * @var \Application\Domain\Entity\ItemValidator
	 */
	protected $itemValidator;
	
	/**
	 * Constructor, receives itemValidator
	 * 
	 * @param ItemValidator $itemValidator
	 */
	public function __construct(ItemValidator $itemValidator)
	{
		$this->itemValidator = $itemValidator;
	}
	
	/**
	 * Validates the given item using the ItemValidator
	 * 
	 * @param Item $item
	 */
	public function validateItem(Item $item)
	{
		$this->itemValidator->validate($item);
	}
}
