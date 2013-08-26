<?php
namespace System\Core;

/**
 * Container for action data.
 */
class Action
{
	/**
	 * Name of controller to use
	 * @var string
	 */
	protected $controller;
	
	/**
	 * Name of method to run
	 * @var string
	 */
	protected $method;
	
	/**
	 * Type of output
	 * @var \System\Core\OutputType
	 */
	protected $outputType;
	
	/**
	 * Arguments
	 * @var array
	 */
	protected $arguments;
	
	/**
	 * CSS files
	 * @var array
	 */
	protected $stylesheets;
	
	/**
	 * Javascript files
	 * @var array
	 */
	protected $scripts;
	
	/**
	 * Variables
	 * @var array
	 */
	protected $variables;
	
	public function __construct()
	{
		// Set HTML as default output type
		$this->outputType = new OutputType(OutputType::TYPE_HTML);
		
		// Set empty arrays for arguments, stylesheets and scripts
		$this->arguments = array();
		$this->stylesheets = array();
		$this->scripts = array();
	}
	
	/**
	 * Returns controller name
	 * 
	 * @return string $controller
	 */
	public function getController()
	{
		return $this->controller;
	}

	/**
	 * Sets the controller name.
	 * 
	 * @param string $controller
	 */
	public function setController($controller)
	{
		$this->controller = $controller;
	}

	/**
	 * Returns the method name.
	 * 
	 * @return string $method
	 */
	public function getMethod()
	{
		return $this->method;
	}

	/**
	 * Sets the method name.
	 * 
	 * @param string $method
	 */
	public function setMethod($method)
	{
		$this->method = $method;
	}

	/**
	 * Returns the output type
	 * 
	 * @return string
	 */
	public function getOutputType()
	{
		return $this->outputType;
	}

	/**
	 * Sets the output type.
	 * 
	 * @param \System\Core\OutputType
	 */
	public function setOutputType(OutputType $outputType)
	{
		$this->outputType = $outputType;
	}

	/**
	 * Returns the arguments.
	 * 
	 * @return array $args
	 */
	public function getArguments()
	{
		return $this->arguments;
	}

	/**
	 * Adds an argument
	 * @param mixed $value
	 */
	public function addArg($value)
	{
		$this->arguments[] = $value;
	}
	/**
	 * @return the $stylesheets
	 */
	public function getStylesheets()
	{
		return $this->stylesheets;
	}

	/**
	 * @return the $scripts
	 */
	public function getScripts()
	{
		return $this->scripts;
	}

	/**
	 * @return the $variables
	 */
	public function getVariables()
	{
		return $this->variables;
	}

	/**
	 * @param multitype: $stylesheets
	 */
	public function setStylesheets($stylesheets)
	{
		$this->stylesheets = $stylesheets;
	}

	/**
	 * @param multitype: $scripts
	 */
	public function setScripts($scripts)
	{
		$this->scripts = $scripts;
	}

	/**
	 * @param multitype: $variables
	 */
	public function setVariables($variables)
	{
		$this->variables = $variables;
	}


	
	
}
