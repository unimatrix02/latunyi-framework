<?php
namespace Application\Database;

use System\Core\TableField;
use System\Core\CustomField;
use System\Core\ForeignField;

use \System\Core\Table;

class ListTable extends Table
{
	public function __construct(\System\Core\Database $db)
	{
		parent::__construct($db);
		
		$this->setName('list');
		$this->setOrderBy('name');

		$this->addField(new TableField('id', true));
		$this->addField(new TableField('name'));
		
		$field = new CustomField('custom');
		$field->query = "SELECT 'bla'";
		$this->addField($field);
	}
}

?>