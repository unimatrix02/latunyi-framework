<?php
namespace Application\Controller;

use Application\Domain\Entity\Item;
use Application\Domain\Entity\Type;
use Application\Domain\Repository\ItemRepository;
use Application\Domain\Repository\TypeRepository;
use Application\Domain\Service\ItemService;

use \System\Core;

class Test extends \System\Core\BaseController
{
	/**
	 * Item repository
	 * @var \Application\Domain\Repository\ItemRepository
	 */
	protected $itemRepo;
	
	/**
	 * Type repository
	 * @var \Application\Domain\Repository\TypeRepository
	 */
	protected $typeRepo;
	
	/**
	 * Item Service
	 * @var \Application\Domain\Service\ItemService
	 */
	protected $itemService;
	
	/**
	 * Shows a list of items.
	 */
	public function showList()
	{
		$this->items = $this->itemRepo->getAll();
	}
	
	/**
	 * 
	 * @param int $itemId	Item ID, 0 for new
	 */
	public function showForm($itemId)
	{
		$this->errors = array();
		
		if ($this->request->hasPostData())
		{
			$item = new Item($this->request->postData);
			
			// Validate item
			$errors = $this->itemService->validateItem($item);
			
			if (empty($errors))
			{
				//$this->itemRepo->save($item);
			}
			else
			{
				$this->item = $item;
				$this->errors = $errors;
			}
		}
		
		if ($itemId == 0)
		{
			$this->item = new Item;
		}
		else
		{
			$this->item = $this->itemRepo->getEntity($itemId);
		}
		
		$this->types = $this->typeRepo->getSimpleList('name');
	}

	public function setItemRepo(ItemRepository $itemRepo)
	{
		$this->itemRepo = $itemRepo;
	}
	
	public function setTypeRepo(TypeRepository $typeRepo)
	{
		$this->typeRepo = $typeRepo;
	}
	
	public function setItemService(ItemService $svc)
	{
		$this->itemService = $svc;
	}
}

?>