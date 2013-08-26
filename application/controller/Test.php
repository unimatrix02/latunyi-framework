<?php
namespace Application\Controller;

use System\Core\DbConnData;

use \System\Core;

class Test extends \System\Core\BaseController
{
	public function showHomepage()
	{
		$this->db->getData('bla');
	}
	
}

?>