<?php
/**
 *	Response class.
 *
 *	@author      Raymond van Velzen <raymond@latunyi.com>
 *	@package     LatunyiFramework
 **/

namespace System\Core;

/**
 * Container for response data.
 */
class Response
{
	/**
	 * Template to use to render the response.
	 * @var string
	 */
	protected $template;

	/**
	 * List of Javascripts to include
	 * @var array
	 */
	protected $scripts;
	
	/**
	 * List of styles to include
	 * @var array
	 */
	protected $styles;
	
	/**
	 * Data container
	 * @var array
	 */
	protected $data;

	/**
	 * Type of output
	 * @var \System\Core\OutputType
	 */
	protected $outputType;
	
	/**
	 * Constructor. Initializes scripts, styles and data as empty arrays
	 * and sets the default template.
	 * 
	 * @param string $defaultTemplate
	 */
	public function __construct($defaultTemplate)
	{
		$this->scripts = array();
		$this->styles = array();
		$this->data = array();
		
		$this->template = $defaultTemplate;
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
	 * Returns the list of scripts.
	 * 
	 * @return array
	 */
	public function getScripts()
	{
		return $this->scripts;
	}

	/**
	 * Returns the list of styles.
	 * 
	 * @return array
	 */
	public function getStyles()
	{
		return $this->styles;
	}

	/**
	 * Returns the data array.
	 * 
	 * @return array
	 */
	public function getData()
	{
		return $this->data;
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
	 * Sets the list of scripts
	 * 
	 * @param array $scripts
	 */
	public function setScripts($scripts)
	{
		$this->scripts = $scripts;
	}

	/**
	 * Adds a file the list of scripts
	 * 
	 * @param string $script
	 */
	public function addScript($script)
	{
		$this->scripts[] = $script;
	}

	/**
	 * Sets the list of styles.
	 * 
	 * @param array $styles
	 */
	public function setStyles($styles)
	{
		$this->styles = $styles;
	}
	
	/**
	 * Adds a file to the list of styles.
	 * 
	 * @param string $stylesheet
	 */
	public function addStylesheet($stylesheet)
	{
		$this->styles[] = $stylesheet;
	}

	/**
	 * Sets the response data.
	 * 
	 * @param array $data
	 */
	public function setData($data)
	{
		$this->data = $data;
	}
	
	/**
	 * Returns all data, scripts and styles.
	 * 
	 * @return array
	 */
	public function getAllData()
	{
		$data = array(
			'scripts' => $this->scripts,
			'styles' => $this->styles,
		);
		return array_merge($data, $this->data);
	}
	
	/**
	 * Returns the OutputType
	 * @return \System\Core\OutputType
	 */
	public function getOutputType()
	{
		return $this->outputType;
	}

	/**
	 * Sets the OutputType.
	 * 
	 * @param \System\Core\OutputType $outputType
	 */
	public function setOutputType($outputType)
	{
		$this->outputType = $outputType;
	}

	/**
	 * Magic method to set data.
	 * 
	 * @param string $key
	 * @param mixed	$value 
	 */
	public function __set($key, $value)
	{
		$this->data[$key] = $value;
	}

	/**
	 * Magic method to get data.
	 * 
	 * @param string $key
	 * @return mixed $value 
	 */
	public function __get($key)
	{
		if (isset($this->data[$key]))
		{
			return $this->data[$key];
		}
	}
}
