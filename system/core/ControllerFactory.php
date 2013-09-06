<?php
/**
 *	Controller Factory class.
 *
 *	@author      Raymond van Velzen <raymond@latunyi.com>
 *	@package     LatunyiFramework
 **/

namespace System\Core;

use System\Core\Database\Database;
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
	
	/**
	 * Registry object from Application
	 * @var \System\Core\Registry
	 */
	protected $registry;

	/**
	 * Constructor, sets config and registry.
	 * 
	 * @param Config $config
	 * @param Registry $registry
	 */
	public function __construct(Config $config, Registry $registry)
	{
		$this->config = $config;
		$this->registry = $registry;
	}

	/**
	 * Initializes the given controller class and injects the given objects, then returns it.
	 * Also executes the make<ControllerName>Controller method in the Application's Factory class.
	 * 
	 * @param string $controllerName
	 * @param Log $log
	 * @param Session $session
	 * @param Request $request
	 * @param Response $response
	 * @return \System\Core\BaseController
	 */
	public function makeController($controllerName, Log $log, Session $session, Request $request, Response $response)
	{
		$fullControllerName = '\\Application\\Controller\\' . $controllerName;
		$controller = new $fullControllerName();
		$controller->setConfig($this->config);
		$controller->setRequest($request);
		$controller->setResponse($response);
		$controller->setLog($log);
		$controller->setSession($session);

		// Execute additional factory method, if available (exists in \Application\Controller\Factory)
		$method = 'make' . $controllerName . 'Controller';
		if (is_callable(array($this, $method)))
		{
			$this->$method($controller);
		}
		
		return $controller;
	}
	
	/**
	 * Utility method to make Database object.
	 * 
	 * @return Database
	 * @throws \Exception
	 */
	protected function getDatabase()
	{
		if (!$this->registry->has('database'))
		{
			if (!$this->config->app->has('database'))
			{
				throw new \Exception('Failed to find config for database connection');
			}
			
			$connData = new \System\Core\Database\ConnectionData();
			$connData->username = $this->config->app->database->username;
			$connData->password = $this->config->app->database->password;
			$connData->database = $this->config->app->database->name;
			
			$this->registry->database = new Database($connData, $this->config->app->database->default_object_namespace);
		}
		
		return $this->registry->database;
	}
}
