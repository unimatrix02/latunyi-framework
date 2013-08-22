<?php
namespace System\Core;

/**
 * Class for environment-related data.
 */
class Environment
{
	const DEV = 'dev';
	const TEST = 'test';
	const PROD = 'prod';

	protected $id;

	public function __construct($id)
	{
		$this->id = $id;
	}

}
