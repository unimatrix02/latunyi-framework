<?php
/**
 *	Validation exception class.
 *
 *	@author      Raymond van Velzen <raymond@latunyi.com>
 *	@package     LatunyiFramework
 **/

namespace System\Core\Exception;

use \System\Core\DataContainer;

/**
 * Exeption class for validation errors.
 */
class Validation extends \Exception
{
	/**
	 * List of fields with error messages.
	 * @var \System\Core\DataContainer
	 */
	public $errors;

	/**
	 * Constructor, sets list of errors
	 * 
	 * @param \System\Core\DataContainer $errors
	 */
	public function __construct(\System\Core\DataContainer $errors)
	{
		$this->errors = $errors;
		parent::__construct('Validation failed');
	}
	
}
