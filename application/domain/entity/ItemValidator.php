<?php
namespace Application\Domain\Entity;

use \System\Core\ValidationRules;
use \System\Core\ValidationRule;
use \System\Core\Helper\Validator;

class ItemValidator extends \System\Core\EntityValidator
{
	/**
	 * Sets up validation rules.
	 */
	public function __construct()
	{
		parent::__construct();
		
		// Use _form to indicate validation that applies to the entity/form as a whole
		$this->addRule(new ValidationRule('_form', 'validateOneThing', 'Something is wrong'));
		
		// Multiple rules can be applied to a field; if one fails, others are skipped
		$this->addRule(new ValidationRule('_form', 'validateAnotherThing', 'Something is very wrong'));
		
		$this->addRule(new ValidationRule('id', 'isInteger', 'Invalid ID'));
		$this->addRule(new ValidationRule('name', 'isNotEmpty', 'Name is empty'));
		$this->addRule(new ValidationRule('value', 'isAmount', 'Value is not valid'));
		$this->addRule(new ValidationRule('typeId', 'validateType', 'Invalid type'));
		
		// Example of a validtor that requires the values of multiple fields 
		$this->addRule(new ValidationRule('startDate', 'isValidPeriod', 'Invalid period', array('startDate', 'endDate')));

	}	

	protected function validateType($type)
	{
		// Do something here to validate the type.
		// If you need something else to do so, like a Repository or the Database,
		// modify the factory method of the controller that uses this validator
		// to pass that object to this validator so you can use it here.

		return true;
	}
}
