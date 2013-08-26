<?php
namespace System\Core;

class BaseApplication
{
	/**
	 * Web request or CLI
	 * @var bool
	 */
	protected $isWebRequest;
	
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
	 * Action object
	 * @var \System\Core\Action
	 */
	protected $action;

	/**
	 * Controller object
	 * @var \Application\Controller
	 */
	protected $controller;
	
	/**
	 * Output string
	 * @var string
	 */
	protected $output;

	/**
	 * Constructor
	 * 
	 * @param bool $isWebRequest
	 */
	public function __construct($isWebRequest)
	{
		$this->isWebRequest = $isWebRequest;
	}
	
	/**
	 * Initializes Environment (using the given environment ID),
	 * Config, Log, Request and Response objects.
	 * 
	 * @param string $envId
	 */
	public function initialize($envId)
	{
		// Setup environment
		$this->environment = new Environment($envId);
		
		// Setup config
		$configLoader = new ConfigLoader();
		$this->config = new Config();
		$this->config->core 	= $configLoader->loadFile(SYSTEM_PATH . '/config/config');
		$this->config->app 		= $configLoader->loadFile(APP_PATH . '/config/config');
		$this->config->actions 	= $configLoader->loadFile(APP_PATH . '/config/actions');
		
		// Setup logging
		$this->log = new Log(DOC_ROOT . $this->config->core->log->dir);

		// Initialize Request object
		$this->request = new Request();
		$this->request->path = $_SERVER['REQUEST_URI'];

		// Initialize Response object
		$this->response = new Response($this->config->app->templating->default);
		
		// Initialize session
		if (true === $this->isWebRequest)
		{
			$this->session = new Session();
		}
	}

	/**
	 * Uses a Router object and the actions config
	 * to find an Action for the current request.
	 */
	public function routeRequest()
	{
		$router = new Router($this->config->actions);
		$this->action = $router->findAction($this->request);
		
		// Copy variables, CSS/JS files from action into response
		$this->response->setStylesheets($this->action->getStylesheets());
		$this->response->setScripts($this->action->getScripts());
		$this->response->setData($this->action->getVariables());
	}

	/**
	 * Uses the ControllerFactory to create a suitable Controller
	 * for the given Action.
	 */
	public function createController()
	{
		$controllerFactory = new ControllerFactory($this->config);
		$this->controller = $controllerFactory->makeController($this->action->getController(), $this->log, $this->session, $this->response);
	}
	
	/**
	 * Runs the method from the controller as indicated in the Action
	 * and receives the modified response object. 
	 */
	public function runControllerMethod()
	{
		$this->response = $this->controller->runMethod($this->action->getMethod(), $this->action->getArguments());
	}
	
	public function renderResponse()
	{
		$renderer = new Renderer($this->response);
		$this->output = $renderer->getOutput($this->action->getOutputType());
	}
	
	/**
	 * Sends the output to the browser with appropriate headers.
	 */
	public function sendOutput()
	{
		switch ($this->action->getOutputType())
		{
			case OutputType::TYPE_JSON:
				$contentType = 'application/json';
				break;
			case OutputType::TYPE_TEXT:
				$contentType = 'text/plain';
				break;
			default:
				$contentType = 'text/html';
				break;
		}
		header('Content-Type: ' . $contentType);
		echo $this->output;
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
