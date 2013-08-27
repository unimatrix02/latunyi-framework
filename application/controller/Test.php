<?php
namespace Application\Controller;

use \Application\Domain\Entity\ItemList;
use \System\Core;

class Test extends \System\Core\BaseController
{
	public function showHomepage()
	{
		$params = new \StdClass();
		$params->name = 'Testing';
		
		$sql = "SELECT * FROM list WHERE name = :name";
		$result = $this->db->getRow($sql, $params);
		
		$result->name = 'Testing 2';
		
		$sql = "UPDATE list SET name = :name WHERE id = :id";
		$aff = $this->db->runQuery($sql, $result);
		pr($aff);
	}
	
}

?>