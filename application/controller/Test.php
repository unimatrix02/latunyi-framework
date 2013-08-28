<?php
namespace Application\Controller;

use Application\Domain\Entity\Item;

use System\Core\QueryParam;

use System\Core\QueryParams;

use Application\Database\ItemTable;
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
		$item = new Item();
		$item->listId = 2;
		$item->name = 'Testing';
		$item->photo = 'something.jpg';
		$item->status = 'active';
		$item->url = 'http://www.something.com';
		//$this->table->insertRow($item);
		/*
		
		$item = $this->table->getRowById(30);
		pr($item);
		
		$item->name = 'Testing ' . date('H:i:s');
		
		$this->table->UpdateRecord($item);
		prx($item);
		
		$data = array('name' => 'bla');
		$params = new QueryConditionList();
		$params->add(new QueryCondition('id', 30));
		$this->table->updateRows($data, $params);
		

		$params = new QueryConditionList();
		$params->add(new QueryCondition('id', 31, '>'));
		
		$this->table->deleteRows($params);
		*/

		$params = new QueryParams();
		$params->add('list_id', 0);
		$count = $this->table->countRows($params);
		pr($count);
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