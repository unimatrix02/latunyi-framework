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
	 * Request object
	 * @var \System\Core\Request
	 */
	protected $request;
	
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
	 * Database object
	 * @var \System\Core\Database
	 */
	protected $db;

	/**
	 * Constructor.
	 */
	public function __construct()
	{
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

		call_user_func_array(array($this, $methodName), $arguments);
		
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
	
	/**
	 * Magic method to get response data.
	 * 
	 * @param string $key
	 * @return mixed $value
	 */
	public function __get($key)
	{
		return $this->response->$key;
	}
	
	/**
	 * Sets the config.
	 * 
	 * @param \System\Core\Config $config
	 */
	public function setConfig($config)
	{
		$this->config = $config;
	}

	/**
	 * Sets the response.
	 * 
	 * @param \System\Core\Response $response
	 */
	public function setResponse(Response $response)
	{
		$this->response = $response;
	}

	/**
	 * Sets the logs.
	 * 
	 * @param \System\Core\Log $log
	 */
	public function setLog(Log $log)
	{
		$this->log = $log;
	}

	/**
	 * Sets the session.
	 * 
	 * @param \System\Core\Session $session
	 */
	public function setSession(Session $session)
	{
		$this->session = $session;
	}

	/**
	 * Sets the database object.
	 * 
	 * @param \System\Core\Database $db
	 */
	public function setDatabase(Database $db)
	{
		$this->db = $db;
	}
	
	/**
	 * Returns the request object.
	 * 
	 * @return \System\Core\Request
	 */
	public function getRequest()
	{
		return $this->request;
	}

	/**
	 * Sets the Request object.
	 * 
	 * @param \System\Core\Request $request
	 */
	public function setRequest($request)
	{
		$this->request = $request;
	}

	
}
