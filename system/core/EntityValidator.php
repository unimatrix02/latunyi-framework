<?php
namespace System\Core;

use \System\Helper\Validator;

/**
 * Base class for entity validation. 
 */
class EntityValidator 
{
	/**
	 * List of ValidationRules
	 * @var \System\Core\ValidationRules
	 */
	private $rules;
	
	public function __construct()
	{
		$this->rules = new ValidationRules();	
	}
	
	public function addRule(ValidationRule $rule)
	{
		$this->rules->add($rule);
	}
	
	/**
	 * Validates the contents of the entity
	 * by using the $validation property.
	 * Returns a list of errors.
	 *
	 * @param \System\Core\Entity $entity
	 * @return DataContainer
	 */
	public function validate(\System\Core\Entity $entity)
	{
		$errors = new DataContainer();
	
		$failedFields = array();
		foreach ($this->rules as $rule)
		{
			// If a validation for the same field already failed, skip the rule
			if (in_array($rule->fieldName, $failedFields))
			{
				continue;
			}
			
			// First try current class
			$target = array($this, $rule->method);

			// Check if we can call local method
			if (!is_callable($target))
			{
				// Check if we can call Validator method
				$target = array('\System\Helper\Validator', $rule->method);
				if (!is_callable($target))
				{
					throw new \Exception('Can\'t call validation method ' . $rule->method . ' in class ' . get_class($this) . ' or in Validator class');
				}
			}
			
			// If field name is _form, don't use arguments
			if ($rule->fieldName == '_form')
			{
				$result = $this->{$rule->method}();
			}
			else
			{
				if (empty($rule->inputFields))
				{
					$params = array($entity->{$rule->fieldName});
				}
				else
				{
					$params = array();
					foreach ($rule->inputFields as $field)
					{
						$params[] = $entity->$field;
					}
				} 
				$result = call_user_func_array($target, $params);
			}
				
			if (!$result)
			{
				$errors->{$rule->fieldName} = $rule->errorMessage;
				$failedFields[] = $rule->fieldName;
			}				
		}
		
		return $errors;
	}

	private function validateOneThing()
	{
		return true;
	}

	private function validateAnotherThing()
	{
		return true;
	}
}
