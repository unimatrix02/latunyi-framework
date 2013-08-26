<?php
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
	 * List of stylesheets to include
	 * @var array
	 */
	protected $stylesheets;
	
	/**
	 * Data container
	 * @var array
	 */
	protected $data;
	
	/**
	 * Constructor. Initializes scripts, stylesheets and data as empty arrays
	 * and sets the default template.
	 * 
	 * @param string $defaultTemplate
	 */
	public function __construct($defaultTemplate)
	{
		$this->scripts = array();
		$this->stylesheets = array();
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
	 * Returns the list of stylesheets.
	 * 
	 * @return array
	 */
	public function getStylesheets()
	{
		return $this->stylesheets;
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
	 * Sets the list of stylesheets.
	 * 
	 * @param array $stylesheets
	 */
	public function setStylesheets($stylesheets)
	{
		$this->stylesheets = $stylesheets;
	}
	
	/**
	 * Adds a file to the list of stylesheets.
	 * 
	 * @param string $stylesheet
	 */
	public function addStylesheet($stylesheet)
	{
		$this->stylesheets[] = $stylesheet;
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
	 * Returns all data, scripts and stylesheets.
	 * 
	 * @return array
	 */
	public function getAllData()
	{
		$data = array(
			'scripts' => $this->scripts,
			'stylesheets' => $this->stylesheets,
		);
		return array_merge($data, $this->data);
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
}
