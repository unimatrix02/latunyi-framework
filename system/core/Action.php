<?php
/**
 *	Action class.
 *
 *	@author      Raymond van Velzen <raymond@latunyi.com>
 *	@package     LatunyiFramework
 **/

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
	protected $styles;
	
	/**
	 * Javascript files
	 * @var array
	 */
	protected $scripts;

	/**
	 * Template to use to render the response.
	 * @var string
	 */
	protected $template;
	
	/**
	 * Variables
	 * @var array
	 */
	protected $variables;

	/**
	 * Constructor, sets the default output type and inits arguments, 
	 * styles and scripts as empty arrays.
	 */
	public function __construct()
	{
		// Set HTML as default output type
		$this->outputType = new OutputType(OutputType::TYPE_HTML);
		
		// Set empty arrays for arguments, styles and scripts
		$this->arguments = array();
		$this->styles = array();
		$this->scripts = array();
		$this->variables = array();
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
	 * Adds an argument.
	 * 
	 * @param mixed $value
	 */
	public function addArg($value)
	{
		$this->arguments[] = $value;
	}
	/**
	 * Returns the styles.
	 * 
	 * @return array
	 */
	public function getStyles()
	{
		return $this->styles;
	}

	/**
	 * Returns the scripts.
	 * 
	 * @return array
	 */
	public function getScripts()
	{
		return $this->scripts;
	}

	/**
	 * Returns the variables.
	 * 
	 * @return array
	 */
	public function getVariables()
	{
		return $this->variables;
	}

	/**
	 * Sets the styles
	 * 
	 * @param arary $styles
	 */
	public function setStyles($styles)
	{
		$this->styles = $styles;
	}

	/**
	 * Sets the scripts.
	 * 
	 * @param array $scripts
	 */
	public function setScripts($scripts)
	{
		$this->scripts = $scripts;
	}

	/**
	 * Sets the variables.
	 * 
	 * @param array $variables
	 */
	public function setVariables($variables)
	{
		$this->variables = $variables;
	}
	
	/**
	 * Sets the arguments
	 * 
	 * @param array $arguments
	 */
	public function setArguments($arguments)
	{
		$this->arguments = $arguments;
	}
	
	/**
	 * Returns the template.
	 * 
	 * @return string
	 */
	public function getTemplate()
	{
		return $this->template;
	}

	/**
	 * Sets the template.
	 * 
	 * @param string $template
	 */
	public function setTemplate($template)
	{
		$this->template = $template;
	}

	/**
	 * Returns true if the template is not empty.
	 * 
	 * @return bool
	 */
	public function hasTemplate()
	{
		return !empty($this->template);
	}
}
