<?php
/**
 *	Admin Controller Factory class.
 *
 *	@author      Raymond van Velzen <raymond@latunyi.com>
 *	@package     LatunyiFramework
 **/

namespace Application\Controller\Admin;

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
	}
	
}
