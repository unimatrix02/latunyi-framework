<?php
namespace System\Core;

class BaseApplication
{
	/**
	 * Environment object
	 * @var \System\Core\Environment
	 */
	protected $environment;

	/**
	 * Configuration object
	 * @var \System\Core\Config
	 */
	protected $config;

	/**
	 * Request object
	 * @var \System\Core\Request
	 */
	protected $request;

	protected $response;

	protected $log;

	protected $session;


	/**
	 * Constructor; initializes Environment (using the given environment ID),
	 * Config, Log, Request and Response objects.
	 * The enviroment ID is provided by the
	 *
	 * @param string $envId
	 */
	public function __construct($envId)
	{
		// Setup environment
		$this->environment = new Environment($envId);

		// Setup config
		$configLoader = new ConfigLoader();
		$this->config = new Config();
		$this->config->core = $configLoader->loadFile(SYSTEM_PATH . '/config/config');
		$this->config->app = $configLoader->loadFile(APP_PATH . '/config/config');
		$this->config->actions = $configLoader->loadFile(APP_PATH . '/config/actions');
		
		// Setup logging
		$this->log = new Log($this->config->core->log->dir);
	}

	/**
	 * Magic getter for hidden properties.
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function __get($key)
	{
		if (isset($this->$key))
		{
			return $this->$key;
		}
	}

}
