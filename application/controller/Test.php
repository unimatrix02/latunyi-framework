<?php
namespace Application\Controller;

use System\Core\OutputType;

use System\Core\DataContainer;
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
	 * Shows a page with container for list items.
	 * Items are loaded via Ajax.
	 */
	public function showListAjax()
	{
	}

	/**
	 * Shows a list of items.
	 */
	public function showListItems()
	{
		$this->items = $this->itemRepo->getAll();
	}
	
	/**
	 * Shows the form via Ajax.
	 * @param int $itemId
	 */
	public function showAjaxForm($itemId)
	{
		$this->errors = array();
		
		if ($this->request->hasPostData())
		{
			try
			{
				$item = new Item($this->request->postData);
		
				// Validate item
				$this->itemService->validateItem($item);
		
				$this->itemRepo->save($item);
		
				$this->response->setOutputType(new OutputType(OutputType::TYPE_TEXT));
				$this->result = 'OK';
			}
			catch (\System\Core\Exception\Validation $ex)
			{
				$this->item = $item;
				$this->errors = $ex->errors;
			}
			catch (\Exception $ex)
			{
				$this->item = $item;
				$errors = new DataContainer();
				$errors->_form = $ex->getMessage();
				$this->errors = $errors;
			}
		}
		else
		{
			if ($itemId == 0)
			{
				$this->item = new Item;
				$this->item->name = 'Test 1';
				$this->item->value = 25.50;
				$this->item->typeId = 2;
				$this->item->startDate = date('Y-m-d');
				$this->item->endDate = date('Y-m-d', strtotime('+1 week'));
		
			}
			else
			{
				$this->item = $this->itemRepo->get($itemId);
			}
		}
		
		$this->types = $this->typeRepo->getSimpleList('name');
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
			try
			{
				$item = new Item($this->request->postData);

				// Validate item
				$this->itemService->validateItem($item);

				$this->itemRepo->save($item);

				redirect('/');
			}
			catch (\System\Core\Exception\Validation $ex)
			{
				$this->item = $item;
				$this->errors = $ex->errors;
			}
			catch (\Exception $ex)
			{
				$this->item = $item;
				$errors = new DataContainer();
				$errors->_form = $ex->getMessage();
				$this->errors = $errors;
			}
		}
		else
		{
			if ($itemId == 0)
			{
				$this->item = new Item;
				$this->item->name = 'Test 1';
				$this->item->value = 25.50;
				$this->item->typeId = 2;
				$this->item->startDate = date('Y-m-d');
				$this->item->endDate = date('Y-m-d', strtotime('+1 week'));
				
			}
			else
			{
				$this->item = $this->itemRepo->get($itemId);
			}
		}
		
		$this->types = $this->typeRepo->getSimpleList('name');
	}
	
	public function removeItem($itemId)
	{
		try
		{
			$this->itemRepo->remove($itemId);
			redirect('/');
		}
		catch (\Exception $ex)
		{
			$this->error = $ex->getMessage();
			$this->showList();
		}
	}
	
	public function removeItemAjax($itemId)
	{
		try
		{
			$this->itemRepo->remove($itemId);
			$this->result = 'OK';
		}
		catch (\Exception $ex)
		{
			$this->result = $ex->getMessage();
		}
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