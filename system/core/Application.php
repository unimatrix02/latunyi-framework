<?php
namespace System\Core;

class Application
{
	protected $config;

	protected $request;

	protected $response;

	protected $log;

	protected $session;

	protected $environment;

	public function __construct()
	{
		// Setup environment
		$this->environment = new Environment();

		// Setup config
		$this->config = new Config();

		// Setup logging
		$this->log = new Log();
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
