<?php
namespace System\Core;

/**
 * Base controller class.
 */
class BaseController
{
	/**
	 * Configuration object
	 * @var \System\Core\Config
	 */
	protected $config;
	
	/**
	 * Response object
	 * @var \System\Core\Response
	 */
	protected $response;
	
	/**
	 * Log object
	 * @var \System\Core\Log
	 */
	protected $log;
	
	/**
	 * Session object
	 * @var \System\Core\Session
	 */
	protected $session;

	/**
	 * Constructor, sets config, response, log and session objects.
	 * 
	 * @param Config $config
	 * @param Response $response
	 * @param Log $log
	 * @param Session $session
	 */
	public function __construct(Config $config, Response $response, Log $log, Session $session)
	{
		$this->config = $config;
		$this->response = $response;
		$this->log = $log;
		$this->session = $session;
	}
	
	/**
	 * Runs a method of this controller with the arguments provided,
	 * then returns the response object.
	 * 
	 * @param string $methodName
	 * @param array $arguments
	 * @return \System\Core\Response
	 */
	public function runMethod($methodName, $arguments)
	{
		// Check if method exists
		if (!is_callable(array($this, $methodName)))
		{
			throw new \Exception('Can\'t call method ' . $methodName . ' on controller ' . get_class($this));
		}
		
		$this->$methodName($arguments);
		
		return $this->response;
	}

	/**
	 * Adds a JS file to the response.
	 * 
	 * @param string $fileName
	 */
	public function addScript($fileName)
	{
		if (!$this->response->has('scripts'))
		{
			$this->response->scripts = array();
		}
		$this->response->scripts[] = $fileName;
	}

	/**
	 * Adds a CSS file to the response.
	 * 
	 * @param string $fileName
	 */
	public function addStylesheet($fileName)
	{
		if (!$this->response->has('stylesheets'))
		{
			$this->response->stylesheets = array();
		}
		$this->response->stylesheets[] = $fileName;
	}
	
	/**
	 * Magic method to set response data.
	 * 
	 * @param string $key
	 * @param mixed $value
	 */
	public function __set($key, $value)
	{
		$this->response->$key = $value;
	}
}
