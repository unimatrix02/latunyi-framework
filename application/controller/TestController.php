<?php
/**
 *	Test Controller class.
 *
 *	@author      Raymond van Velzen <raymond@latunyi.com>
 *	@package     LatunyiFramework
 **/

namespace Application\Controller;

use System\Core\OutputType;

use System\Core\DataContainer;
use Application\Domain\Entity\Item;
use Application\Domain\Entity\Type;
use Application\Domain\Repository\ItemRepository;
use Application\Domain\Repository\TypeRepository;
use Application\Domain\Service\ItemService;

use \System\Core;

/**
 * Test controller class.
 */
class TestController extends \System\Core\BaseController
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
	 * Method to initialize controller. Sets up the menu
	 */
	public function init()
	{
		$this->response->reqPath = $this->request->path;
		$this->menu = array(
			'/' => 'Normal list',
			'/list' => 'Ajax list',
			'/admin' => 'Admin',
		);
	}
	
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
	 * Shows a form to edit an item.
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
	
	/**
	 * Removes an item, shows list with error when it fails,
	 * otherwise reloads list.
	 * 
	 * @param int $itemId
	 */
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
	
	/**
	 * Removes an item, called via Ajax.
	 * Returns OK or an error.
	 * 
	 * @param int $itemId
	 */
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

	/**
	 * Sets the ItemRepository.
	 * 
	 * @param ItemRepository $itemRepo
	 */
	public function setItemRepo(ItemRepository $itemRepo)
	{
		$this->itemRepo = $itemRepo;
	}
	
	/**
	 * Sets the type repository.
	 * 
	 * @param TypeRepository $typeRepo
	 */
	public function setTypeRepo(TypeRepository $typeRepo)
	{
		$this->typeRepo = $typeRepo;
	}
	
	/**
	 * Sets the ItemService.
	 * 
	 * @param ItemService $svc
	 */
	public function setItemService(ItemService $svc)
	{
		$this->itemService = $svc;
	}
}
