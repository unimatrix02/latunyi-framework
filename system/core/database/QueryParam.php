<?php
namespace System\Core\Database;

/**
 * Class to represent a query parameter.
 */
class QueryParam
{
	public $fieldName;
	
	public $operator = '=';
	
	public $fieldValue;

	public function __construct($fieldName, $fieldValue, $operator = '=')
	{
		$this->fieldName = $fieldName;
		$this->fieldValue = $fieldValue;
		$this->operator = $operator;
	}
}