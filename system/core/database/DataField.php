<?php
namespace System\Core\Database;

/**
 * Base class for data fields, such as TableField, CustomField and ForeignField.
 */
class DataField
{
	/**
	 * Name of the table column
	 * @var string
	 */
	public $name;

	/**
	 * Constructor, sets the name.
	 * 
	 * @param string $name
	 */
	public function __construct($name)
	{
		$this->name = $name;
	}
	
}