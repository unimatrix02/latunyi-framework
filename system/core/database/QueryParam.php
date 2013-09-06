<?php
namespace System\Core\Database;

/**
 * Class to represent a query parameter.
 */
class QueryParam
{
	/**
	 * Field name
	 * @var string
	 */
	public $fieldName;
	
	/**
	 * Operator, such as =, !=, <, etc
	 * @var string
	 */
	public $operator = '=';
	
	/**
	 * Field value
	 * @var string
	 */
	public $fieldValue;

	/**
	 * Constructor, sets the field name, field value, and operator.
	 * 
	 * @param string $fieldName
	 * @param string $fieldValue
	 * @param string $operator
	 */
	public function __construct($fieldName, $fieldValue, $operator = '=')
	{
		$this->fieldName = $fieldName;
		$this->fieldValue = $fieldValue;
		$this->operator = $operator;
	}
}