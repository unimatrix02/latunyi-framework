<?php
namespace System\Core;

use \Application\Controller;

/**
 * Class to produce Controller instances.
 */
class ControllerFactory
{
	/**
	 * Action object
	 * @var \System\Core\Action
	 */
	protected $action;
	
	public function __construct(Action $action)
	{
		$this->action = $action;
	}
	
	public function makeController(Config $config, Log $log, Session $session, Response $response)
	{
		$controllerName = '\\Application\\Controller\\' . $this->action->getController();
		$controller = new $controllerName($config, $response, $log, $session);
		return $controller;
	}
}
