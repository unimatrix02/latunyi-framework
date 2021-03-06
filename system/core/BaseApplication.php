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
	 * Current module, if applicable.
	 * @var \System\Core\Module
	 */
	protected $module;

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

		$this->setupConfig();

		// Setup logging
		$this->log = new Log(DOC_ROOT . $this->config->core->log->dir);

		$this->initRequest();

		$this->initModule();
		$this->checkSecureRequest();
		$this->checkAuth();

		$this->setupActionConfig();

		$this->initResponse();

		// Initialize session
		if ($this->environment->isWeb())
		{
			$this->session = new Session();
		}
	}

	/**
	 * Loads configs for core, application into the config property.
	 */
	private function setupConfig()
	{
		$configLoader = new ConfigLoader();
		$this->config = new Config();
		$this->config->core 	= $configLoader->loadFile(SYSTEM_PATH . '/config/config');
		$this->config->app 		= $configLoader->loadFile(APP_PATH . '/config/config');
	}

	/**
	 * Takes modules from the application config, if available, and compares
	 * the root path of each module with the request path. If there is a match,
	 * a Module object is created and placed in the module property.
	 */
	private function initModule()
	{
		if ($this->config->app->has('modules'))
		{
			$modules = $this->config->app->modules->asArray();
			arsort($modules);

			foreach($modules as $name => $data)
			{
				if ($data['root_path'] == substr($this->request->path, 0, strlen($data['root_path'])))
				{
					$this->module = new Module($name, $data);
					break;
				}
			}
		}
	}

	/**
	 * Sets up the action config, using either the standard actions config
	 * or the actions config for a specific module.
	 */
	private function setupActionConfig()
	{
		$configLoader = new ConfigLoader();

		if (!isset($this->module))
		{
			// Load common actions config
			$this->config->actions 	= $configLoader->loadFile(APP_PATH . '/config/actions');
		}
		else
		{
			// Load module-specific actions
			$this->config->actions 	= $configLoader->loadFile(APP_PATH . '/config/actions_' . $this->module->getId());
		}
	}

	/**
	 * Initializes the Reponse object with a default template.
	 * If a module is set, the template path (if set) is prefixed to the default template.
	 */
	private function initResponse()
	{
		$defaultTemplate = $this->config->app->templating->default;
		if ($this->hasModule() && $this->module->hasTemplatePath())
		{
			$defaultTemplate = $this->module->getTemplatePath() . '/' . $defaultTemplate;
		}
		$this->response = new Response($defaultTemplate);
	}

	/**
	 * Initializes the Request object by setting path, postData and pathParameters,
	 * and checks for an Ajax request.
	 */
	private function initRequest()
	{
		$this->request = new Request();
		$this->request->path = $_SERVER['REQUEST_URI'];
		$this->request->postData = $_POST;
		$this->request->pathParameters = $_GET;

		if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' && isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)
		{
			$this->request->setIsSecure(true);
		}

		if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')
		{
			$this->request->setIsAjaxRequest(true);
		}
	}

	/**
	 * If a module is active, and the module requires secure requests,
	 * checks the Request, and if it is not secure, redirect to https.
	 */
	public function checkSecureRequest()
	{
		if ($this->hasModule() && $this->module->isSecure() && !$this->request->isSecure())
		{
			$url = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			redirect($url);
		}
	}

	/**
	 * If a module is active, and the module has a login, require HTTP basic auth
	 * to match the username and password from the module.
	 */
	public function checkAuth()
	{
		if ($this->hasModule() && $this->module->hasLogin())
		{
			if (empty($_SERVER['PHP_AUTH_USER']) || empty($_SERVER['PHP_AUTH_PW']) ||
				$_SERVER['PHP_AUTH_USER'] !== $this->module->getUsername() ||
				$_SERVER['PHP_AUTH_PW'] !== $this->module->getPassword())
			{
				header('WWW-Authenticate: Basic realm="Secure Area"');
				header('HTTP/1.0 401 Unauthorized');

				echo '<h1>Authorization required</h1>';
				exit;
			}
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
		$controllerName = $this->action->getController();
		$controllerNamespace = '';

		$factoryNamespace = '\Application\Controller';

		// Prefix controller name and factory namespace with namespace from module, if present
		if (isset($this->module) && $this->module->hasControllerNamespace())
		{
			$controllerNamespace = $this->module->getControllerNamespace();
			$factoryNamespace .= '\\' . $controllerNamespace;
		}

		$factoryClass = $factoryNamespace . '\Factory';

		$controllerFactory = new $factoryClass($this->config, $this->registry);

		$this->controller = $controllerFactory->makeController($controllerNamespace, $controllerName, $this->log, $this->session, $this->request, $this->response);
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

	/**
	 * Returns true if a module is set.
	 *
	 * @return bool
	 */
	public function hasModule()
	{
		return isset($this->module);
	}
}
