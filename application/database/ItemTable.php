<?php
/**
 *	Item Table class.
 *
 *	@author      Raymond van Velzen <raymond@latunyi.com>
 *	@package     LatunyiFramework
 **/

namespace Application\Database;

use \System\Core\Database\TableField;
use \System\Core\Database\CustomField;
use \System\Core\Database\ForeignField;
use \System\Core\Database\Table;

/**
 * Child class for the Item table.
 */
class ItemTable extends Table
{
	/**
	 * Constructor, receives the Database object, sets properties and fields.
	 * 
	 * @param \System\Core\Database\Database $db
	 */
	public function __construct(\System\Core\Database\Database $db)
	{
		parent::__construct($db, 'Item');
		
		$this->setName('item');
		$this->setOrderBy('name');
		$this->setPrimaryKeyIsAutoIncrement(true);

		$this->addField(new TableField('id', true));
		$this->addField(new TableField('name'));
		$this->addField(new TableField('value'));
		$this->addField(new TableField('type_id'));
		$this->addField(new TableField('start_date'));
		$this->addField(new TableField('end_date'));

		$field = new ForeignField('type');
		$field->table = 'type';
		$field->foreignFieldName = 'name';
		$field->joinFrom = 'type_id';
		$field->joinFromTable = '';
		$field->joinTo = 'id';
		$this->addField($field);
	}
}