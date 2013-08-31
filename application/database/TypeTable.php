<?php
namespace Application\Database;

use System\Core\Database\TableField;
use System\Core\Database\CustomField;
use System\Core\Database\ForeignField;

use \System\Core\Database\Table;

class TypeTable extends Table
{
	public function __construct(\System\Core\Database\Database $db)
	{
		parent::__construct($db, 'Type');
		
		$this->setName('type');
		$this->setOrderBy('name');
		$this->setPrimaryKeyIsAutoIncrement(true);

		$this->addField(new TableField('id', true));
		$this->addField(new TableField('name'));

		/*
		// Example of a custom field
		$field = new CustomField('custom');
		$field->query = "SELECT 'bla'";		// Can be any kind of subselect
		$this->addField($field);
		*/
	}
}

?>