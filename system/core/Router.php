<?php
namespace System\Core;

class Router
{
	/**
	 * Action configuration object
	 * @var \System\Core\Config
	 */
	protected $config;

	/**
	 * Constructor
	 * 
	 * @param object $actionConfig
	 */
	public function __construct($actionConfig)
	{
		$this->config = $actionConfig;
	}
	
	/**
	 * Takes a request and finds
	 * @param Request $request
	 */
	public function findAction(Request $request)
	{
		// Make a list of paths
		$paths = array_keys($this->config->getData());
		
		// Iterate over paths to find match with request
		$found = false;
		foreach ($paths as $path)
		{
			$regexPath = str_replace('/', '\/', $path);
			$pattern = '/^' . $regexPath . '$/';

			if (regex_check($pattern, $request->path))
			{
				$found = true;

				$action = new Action;
				$action->setController($this->config->$path->controller);
				$action->setMethod($this->config->$path->method);
				if ($this->config->$path->has('output'))
				{
					$action->setOutputType(new OutputType($this->config->$path->output));
				}
				if ($this->config->$path->has('files'))
				{
					if ($this->config->$path->files->has('css'))
					{
						$action->setStylesheets($this->config->$path->files->css->asArray());
					}
					if ($this->config->$path->files->has('js'))
					{
						$action->setScripts($this->config->$path->files->js->asArray());
					}
				}
				if ($this->config->$path->has('vars'))
				{
					$action->setVariables($this->config->$path->vars->asArray());
				}
				
				// Add parameters from path as arguments to action
				$matches = regex_matches($pattern, $request->path, true);
				if (count($matches) > 1)
				{
					array_shift($matches);
					foreach ($matches as $arg)
					{
						$action->addArg($arg[0]);
					}
				}
				else
				{
					// If the path didn't have parameters, and the config does, provide them to the action as arguments
					if ($this->config->$path->has('args'))
					{
						$action->setArguments($this->config->$path->args->asArray());
					}
				}
				
				break;
			}
		}
		
		if (!$found)
		{
			throw new \Exception('Failed to find action for path ' . $request->path);
		}
		
		return $action;
	}

}
