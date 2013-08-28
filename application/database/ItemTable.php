<?php
namespace Application\Database;

use \System\Core\Database\TableField;
use \System\Core\Database\CustomField;
use \System\Core\Database\ForeignField;
use \System\Core\Database\Table;

class ItemTable extends Table
{
	public function __construct(\System\Core\Database\Database $db)
	{
		parent::__construct($db, 'Item');
		
		$this->setName('item');
		$this->setOrderBy('name');
		$this->setPrimaryKeyIsAutoIncrement(true);

		$this->addField(new TableField('id', true));
		$this->addField(new TableField('list_id'));
		$this->addField(new TableField('name'));
		$this->addField(new TableField('url'));
		$this->addField(new TableField('photo'));
		$this->addField(new TableField('status'));

		$field = new ForeignField('list');
		$field->table = 'list';
		$field->foreignFieldName = 'name';
		$field->joinFrom = 'list_id';
		$field->joinFromTable = '';
		$field->joinTo = 'id';
		$this->addField($field);
		
		$field = new CustomField('custom');
		$field->query = "SELECT COUNT(*) FROM list WHERE id = item.id";
		//$this->addField($field);
	}
}

?>