<?php
namespace System\Core;

class TableField extends DataField
{
	/**
	 * If this is a primary key.
	 * @var bool
	 */
	public $isPrimaryKey = false;

	/**
	 * Constructor, sets the name and primary key field
	 * @param unknown_type $name
	 * @param unknown_type $isPrimaryKey
	 */
	public function __construct($name, $isPrimaryKey = false)
	{
		$this->name = $name;
		$this->isPrimaryKey = $isPrimaryKey;
	}
	
}