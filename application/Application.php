<?php
namespace Application;

use System\Core\Environment as Env;

class Application extends \System\Core\BaseApplication
{
	public function __construct()
	{
		parent::__construct($this->getEnvironmentId());
	}

	/**
	 * Returns the ID for the currently detected environment,
	 * such as "dev", "test", "prod".
	 *
	 * @return string
	 */
	private function getEnvironmentId()
	{
		$hostname = getServerHostName();

		// Logic to determine env based on hostname
		$env = Env::DEV;

		return $env;
	}
}
