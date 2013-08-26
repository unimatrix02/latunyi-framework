<?php
namespace System\Core;

/**
 * Class to render a PHP template.
 */
class Template
{
	/**
	 * Template name
	 * @var string
	 */
	protected $template;
	
	/**
	 * Template data
	 * @var array
	 */
	protected $data;
	
	/**
	 * Constructor
	 * 
	 * @param string $template
	 * @param array	$data;
	 */
	public function __construct($template, $data)
	{
		$this->template = $template;
		$this->data = $data;
	}

	/**
	 * Renders the set template with the provided data and returns the output.
	 * 
	 * @return string Output
	 */
	public function render()
	{
		$tplFile = APP_PATH . '/templates/' . $this->template;
		
		$this->checkTemplateFileExists($this->template);

		// Get data into scope and include template
		extract($this->data, EXTR_REFS);
		ob_start();
		include($tplFile);
		$result = ob_get_clean();
		return $result;
	}

	/**
	 * Loads the given file. Used to include subtemplates in the template.
	 * 
	 * @param string $file
	 */
	public function load($file)
	{
		$this->checkTemplateFileExists($file);
		include(APP_PATH . '/templates/' . $file);	
	}
	
	private function checkTemplateFileExists($file)
	{
		// Check for template file
		$tplFile = APP_PATH . '/templates/' . $file;
		
		if (!file_exists($tplFile))
		{
			throw new \Exception('Failed to find template ' . $file);
		}
	}
}
