<?php
/**
 *	Test Admin Controller class.
 *
 *	@author      Raymond van Velzen <raymond@latunyi.com>
 *	@package     LatunyiFramework
 **/

namespace Application\Controller\Admin;

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
class TestController extends Core\BaseController
{
	/**
	 * Method to initialize controller.
	 */
	public function init()
	{
	}
	
	/**
	 * Shows the start page.
	 */
	public function showStartPage()
	{
	}

}
