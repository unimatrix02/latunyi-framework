<?php
/**
 *	Environment identifier class.
 *
 *	@author      Raymond van Velzen <raymond@latunyi.com>
 *	@package     LatunyiFramework
 **/

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
	 * Constant for usage from webbrowser
	 */
	const TYPE_WEB = 'web';

	/**
	 * Constant for usage from command line
	 */
	const TYPE_CLI = 'cli';

	/**
	 * Identifier of the current environment
	 * @var string
	 */
	protected $id;

	/**
	 * Type of environment (web request or CLI command)
	 * @var string
	 */
	protected $type;

	/**
	 * Constructor, sets the environment ID using detection based on hostname.
	 */
	public function __construct()
	{
		$this->setEnvironmentId();
		$this->detectEnvType();
	}

	/**
	 * Sets the ID for the currently detected environment, such as "dev", "test", "prod".
	 */
	private function setEnvironmentId()
	{
		$hostname = getServerHostName();

		// Logic to determine env based on hostname, now simply dev
		if ($hostname == 'qingdao')
		{
			$env = self::DEV;
		}
		else
		{
			$env = self::PROD;
		}

		$this->id = $env;
	}

	/**
	 * Detects the environment type. If HTTP_HOST and REMOTE_ADDR are set in $_SERVER,
	 * the type is assumed to be 'web', otherwise CLI.
	 */
	private function detectEnvType()
	{
		if (isset($_SERVER['HTTP_HOST']) && isset($_SERVER['REMOTE_ADDR']))
		{
			$this->type = self::TYPE_WEB;
		}
		else
		{
			$this->type = self::TYPE_CLI;
		}
	}

	/**
	 * Returns true if the environment type is web.
	 *
	 * @return bool
	 */
	public function isWeb()
	{
		return $this->type == self::TYPE_WEB;
	}
}
