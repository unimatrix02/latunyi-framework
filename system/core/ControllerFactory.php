<?php
namespace System\Core;

use Application\Database\ItemTable;

use \Application\Controller;

/**
 * Class to produce Controller instances.
 */
class ControllerFactory
{
	/**
	 * Config object
	 * @var \System\Core\Config
	 */
	protected $config;
	
	public function __construct(Config $config)
	{
		$this->config = $config;
	}
	
	public function makeController($controllerName, Log $log, Session $session, Response $response)
	{
		$fullControllerName = '\\Application\\Controller\\' . $controllerName;
		$controller = new $fullControllerName();
		$controller->setConfig($this->config);
		$controller->setResponse($response);
		$controller->setLog($log);
		$controller->setSession($session);
		
		// Execute additional factory method, if available
		$method = 'make' . $controllerName . 'Controller';
		if (is_callable(array($this, $method)))
		{		
			$this->$method($controller);
		}
		
		return $controller;
	}
	
	/**
	 * Customizes the Test controller.
	 * 
	 * @param Controller $controller
	 */
	public function makeTestController(&$controller)
	{
		$table = new ItemTable($this->getDatabase());
		$controller->setTable($table);
	}
	
	/**
	 * Utility method to make Database object.
	 * 
	 * @return Database
	 * @throws \Exception
	 */
	private function getDatabase()
	{
		if (!$this->config->app->has('database'))
		{
			throw new \Exception('Failed to find config for database connection');
		}
		
		$config = new DbConnData();
		$config->username = $this->config->app->database->username;
		$config->password = $this->config->app->database->password;
		$config->database = $this->config->app->database->name;
		
		return new Database($config, $this->config->app->database->default_object_namespace);
	}
}
