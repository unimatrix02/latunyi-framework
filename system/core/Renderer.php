<?php
/**
 *	Renderer class.
 *
 *	@author      Raymond van Velzen <raymond@latunyi.com>
 *	@package     LatunyiFramework
 **/

namespace System\Core;

/**
 * Class to produce the output of a response.
 */
class Renderer
{
	/**
	 * Response object
	 * @var \System\Core\Response
	 */
	protected $response;

	/**
	 * Constructor
	 *
	 * @param object $response
	 */
	public function __construct($response)
	{
		$this->response = $response;
	}

	/**
	 * Returns the output of the response, in the given output type.
	 * 
	 * @param OutputType $outputType
	 * @return string Output string
	 */
	public function getOutput(OutputType $outputType)
	{
		$method = 'render' . ucfirst($outputType);
		return $this->$method();
	}
	
	/**
	 * Renders the response using an HTML template object.
	 * 
	 * @return string HTML output
	 */
	public function renderHtml()
	{
		$tpl = new Template($this->response->getTemplate(), $this->response->getAllData());
		return $tpl->render();
	}

	/**
	 * Renders only the response data as JSON.
	 * 
	 *  @return string JSON output
	 */
	public function renderJson()
	{
		return json_encode($this->response->getData());
	}

	/**
	 * Renders only the "result" entry of the response data as text.
	 * 
	 * @return string Text output
	 */
	public function renderText()
	{
		$data = $this->response->getData();
		if (isset($data['result']))
		{
			return (string)$data['result'];
		}
		return '';
	}

	/**
	 * Renders nothing, immediately returns.
	 */
	public function renderNone()
	{
		return;
	}
}
