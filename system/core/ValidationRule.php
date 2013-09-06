<?php
namespace System\Core;

/**
 * Class to specify validation rule for a field
 */
class ValidationRule
{
	/**
	 * Name of the field to validate
	 * @var string
	 */
	public $fieldName;
	
	/**
	 * Name of the method to apply. Either a fully qualified ClassName::StaticMethod or only a method name, in which case it is assumed to exist in the entity itself.
	 * @var string
	 */
	public $method;
	
	/**
	 * Names of the fields to provide as arguments to the validation method. 
	 * If empty, the value of the $fieldName field is provided.
	 * @var array
	 */
	public $inputFields;
	
	/**
	 * Error message when validation fails.
	 * @var string
	 */
	public $errorMessage;

	/**
	 * Constructor, sets field name, method, error message and input fields.
	 * 
	 * @param string $fieldName
	 * @param string $method
	 * @param string $errorMessage
	 * @param array $inputFields
	 */
	public function __construct($fieldName, $method, $errorMessage, $inputFields = null)
	{
		$this->fieldName = $fieldName;
		$this->method = $method;
		$this->errorMessage = $errorMessage;
		$this->inputFields = $inputFields;
	}
}
