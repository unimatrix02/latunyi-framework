<?php
namespace Application\Controller;

use Application\Database\ItemTable;

class Factory extends \System\Core\ControllerFactory
{
	/**
	 * Customizes the Test controller.
	 *
	 * @param Test $controller
	 */
	public function makeTestController(Test $controller)
	{
		$table = new ItemTable($this->getDatabase());
		$controller->setTable($table);
	}
	
}
