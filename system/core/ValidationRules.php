<?php
/**
 *	Validation rules class.
 *
 *	@author      Raymond van Velzen <raymond@latunyi.com>
 *	@package     LatunyiFramework
 **/
namespace System\Core;

/**
 * Class to hold a list of ValidateRule objects.
 */
class ValidationRules extends ObjectList
{
	/**
	 * Adds a ValidationRule to the list.
	 * 
	 * @param ValidationRule $rule
	 */
	public function add(ValidationRule $rule)
	{
		parent::add($rule);
	}
}
