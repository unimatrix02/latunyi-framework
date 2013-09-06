<?php
namespace System\Core;

/**
 * Class for environment-related data.
 */
class Environment
{
	/**
	 * Constant for development
	 * @var string
	 */
	const DEV = 'dev';
	
	/**
	 * Constant for test
	 * @var string
	 */
	const TEST = 'test';
	
	/**
	 * Constant for production
	 * @var string
	 */
	const PROD = 'prod';

	/**
	 * Identifier of the current environment
	 * @var string
	 */
	protected $id;

	/**
	 * Constructor, sets the environment ID.
	 * 
	 * @param string $id
	 */
	public function __construct($id)
	{
		$this->id = $id;
	}

}
