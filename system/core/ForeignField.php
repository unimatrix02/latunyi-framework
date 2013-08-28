<?php
namespace System\Core;

class ForeignField extends DataField
{
	/**
	 * Name of the target field in the foreign table
	 * @var string
	 */
	public $foreignFieldName;

	/**
	 * Table name
	 * @var string
	 */
	public $table;

	/**
	 * Field to join from to foreign table
	 * @var string
	 */
	public $joinFrom;

	/**
	 * Field to join to (primary key in foreign table), default 'id'
	 * @var string
	 */
	public $joinTo = 'id';

	/**
	 * Join from table (second-level table for joins)
	 * @var string
	 */
	public $joinFromTable;

	/**
	 * Join type (INNER, LEFT), default inner
	 * @var string
	 */
	public $joinType = 'inner';
	
}