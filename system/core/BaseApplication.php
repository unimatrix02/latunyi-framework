<?php
/**
 *	Base application class.
 *
 *	@author      Raymond van Velzen <raymond@latunyi.com>
 *	@package     LatunyiFramework
 **/

namespace System\Core;

/**
 * Base application class, runs the application lifecycle while delegating operations to other classes.
 */
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
	 * List of reusable objects
	 * @var \System\Core\Registry
	 */
	protected $registry;

	/**
	 * Constructor
	 */
	public function __construct()
	{
	}

	/**
	 * Initializes Environment (using the given environment ID),
	 * Config, Log, Request and Response objects.
	 */
	public function initialize()
	{
		$this->registry = new Registry();
		
		// Setup environment
		$this->environment = new Environment();

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
		$this->request->postData = $_POST;
		$this->request->pathParameters = $_GET;

		if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')
		{
			$this->request->setIsAjaxRequest(true);
		}

		// Initialize Response object
		$this->response = new Response($this->config->app->templating->default);
		
		// Initialize session
		if ($this->environment->isWeb())
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
		$this->response->setstyles($this->action->getstyles());
		$this->response->setScripts($this->action->getScripts());
		$this->response->setData($this->action->getVariables());
		$this->response->setOutputType($this->action->getOutputType());
		
		// Override default template in response with template from action
		if ($this->action->hasTemplate())
		{
			$this->response->setTemplate($this->action->getTemplate());
		}
	}

	/**
	 * Uses the ControllerFactory to create a suitable Controller
	 * for the given Action.
	 */
	public function createController()
	{
		$controllerFactory = new \Application\Controller\Factory($this->config, $this->registry);
		$this->controller = $controllerFactory->makeController($this->action->getController(), $this->log, $this->session, $this->request, $this->response);
	}
	
	/**
	 * Runs the method from the controller as indicated in the Action
	 * and receives the modified response object. 
	 */
	public function runControllerMethod()
	{
		$this->response = $this->controller->runMethod($this->action->getMethod(), $this->action->getArguments());
	}

	/**
	 * Lets the Renderer render the response and receives the result, stored in $this->output.
	 */
	public function renderResponse()
	{
		// Only merge/minify assets for normal web requests, not for Ajax or CLI
		if (false === $this->request->isAjaxRequest() && $this->environment->isWeb())
		{
			// Get list of assets (CSS/JS) to include in response
			if (!$this->config->app->has('assets'))
			{
				throw new \Exception('Asset configuration is missing');
			}
			$assetMgr = new AssetManager($this->config->app->assets, $this->response->getStyles(), $this->response->getScripts());
			$result = $assetMgr->mergeAssets();
	
			$this->response->setStyles($result->styles);
			$this->response->setScripts($result->scripts);
		}
		$this->log->add($this->response);
		
		$renderer = new Renderer($this->response);
		$this->output = $renderer->getOutput($this->response->getOutputType());
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
