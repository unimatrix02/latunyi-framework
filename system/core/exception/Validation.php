<?php
namespace System\Core\Exception;

use \System\Core\DataContainer;

class Validation extends \Exception
{
	/**
	 * List of fields with error messages.
	 * @var \System\Core\DataContainer
	 */
	public $errors;
	
	public function __construct(\System\Core\DataContainer $errors)
	{
		$this->errors = $errors;
		parent::__construct('Validation failed');
	}
	
}
