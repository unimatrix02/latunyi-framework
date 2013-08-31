<?php
namespace System\Core;

use \System\Core\Helper\tpl;

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
	 * Renders the default or given template with the provided data and returns the output.
	 * 
	 * @param string $template	Template file
	 * @return string Output
	 */
	public function render($template = '')
	{
		if (empty($template))
		{
			$tplFile = APP_PATH . '/templates/' . $this->template;
		}
		else
		{
			$tplFile = APP_PATH . '/templates/' . $template;
		}

		if (!file_exists($tplFile))
		{
			//throw new \Exception('Failed to find template ' . $file);
			echo '[Failed to find template ' . str_replace(APP_PATH . '/templates/', '', $tplFile) . ']';
			return;
		}
		
		// Start a new buffer to capture output
		ob_start();
		
		// Load template functions
		require_once(SYSTEM_PATH . '/includes/template_functions.php');

		// Get data into scope
		extract($this->data, EXTR_REFS);
		
		// Load template
		include($tplFile);
		
		// Get result
		$result = ob_get_clean();
		
		return $result;
	}
	
}
