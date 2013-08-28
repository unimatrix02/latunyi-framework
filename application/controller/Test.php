<?php
namespace Application\Controller;

use Application\Domain\Entity\Item;

use \System\Core\Database\QueryParams;

use \Application\Domain\Entity\ItemList;
use \System\Core;

class Test extends \System\Core\BaseController
{
	/**
	 * ItemTable object
	 * @var \Application\Database\ItemTable
	 */
	protected $table;
	
	public function showHomepage()
	{
		//$data = $this->table->getAllRows();
		//pr($data);
		
		//$data = $this->table->getRow(9);
		//pr($data);
		
// 		$params = new QueryParams();
// 		$params->add('list_id', 2);
// 		$data = $this->table->getRows($params);
// 		pr($data);
		
		$item = new Item();
		$item->listId = 2;
		$item->name = 'Testing';
		$item->photo = 'something.jpg';
		$item->status = 'active';
		$item->url = 'http://www.something.com';
		//$this->table->insertRow($item);
		
		//$item = $this->table->getRow(36);
		//pr($item);
		
		//$item->name = 'Testing ' . date('H:i:s');
		
		//$this->table->updateRow($item);
		//prx($item);
		
		$data = array('name' => 'bla');
		$params = new QueryParams();
		$params->add('id', 30, '>');
		//$this->table->updateRows($data, $params);
		
		$params = new QueryParams();
		$params->add('id', 31, '>');
		//$this->table->deleteRows($params);

		$params = new QueryParams();
		$params->add('list_id', 2);
		$count = $this->table->countRows($params);
		//pr($count);
	}
	
	/**
	 * Sets the table object
	 * 
	 * @param \Application\Database\ItemTable $table
	 */
	public function setTable($table)
	{
		$this->table = $table;
	}
	
}

?>