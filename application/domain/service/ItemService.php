<?php
namespace Application\Domain\Service;

use System\Core\Service;
use Application\Domain\Entity\Item;
use Application\Domain\Entity\ItemValidator;

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
	 * @return array List of errors
	 */
	public function validateItem(Item $item)
	{
		return $this->itemValidator->validate($item);
	}
}
