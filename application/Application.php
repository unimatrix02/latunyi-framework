<?php
/**
 *	Application class.
 *
 *	@author      Raymond van Velzen <raymond@latunyi.com>
 *	@package     LatunyiFramework
 **/

namespace Application;

use System\Core\Environment as Env;

/**
 * Class that represents a specific application.
 */
class Application extends \System\Core\BaseApplication
{
	/**
	 * Constructor
	 */
	public function __construct($isWebRequest)
	{
		parent::__construct($isWebRequest);
	}
	
	/**
	 * Initializes the application.
	 * 
	 * @see System\Core.BaseApplication::initialize()
	 */
	public function initialize()
	{
		parent::initialize($this->getEnvironmentId());
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

		// Logic to determine env based on hostname, now simply dev
		$env = Env::DEV;

		return $env;
	}
}
