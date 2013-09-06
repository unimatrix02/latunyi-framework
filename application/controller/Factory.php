<?php
/**
 *	Controller Factory class.
 *
 *	@author      Raymond van Velzen <raymond@latunyi.com>
 *	@package     LatunyiFramework
 **/

namespace Application\Controller;

use Application\Domain\Entity\ItemValidator;
use Application\Domain\Service\ItemService;
use Application\Domain\Repository\TypeRepository;
use Application\Domain\Repository\ItemRepository;
use Application\Database\ItemTable;
use Application\Database\TypeTable;

/**
 * Factory class to assemble and inject dependencies for controllers.
 */
class Factory extends \System\Core\ControllerFactory
{
	/**
	 * Customizes the Test controller.
	 *
	 * @param Test $controller
	 */
	public function makeTestController(Test $controller)
	{
		$itemTable = new ItemTable($this->getDatabase());
		$itemRepo = new ItemRepository($itemTable);
		$controller->setItemRepo($itemRepo);

		$typeTable = new TypeTable($this->getDatabase());
		$typeRepo = new TypeRepository($typeTable);
		$controller->setTypeRepo($typeRepo);

		$itemValidator = new ItemValidator();
		$itemService = new ItemService($itemValidator);
		$controller->setItemService($itemService);
	}
	
}
