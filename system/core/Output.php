<?php
namespace System\Core;

/**
 * Container for output data.
 */
class Output
{
	const TYPE_HTML = 'html';
	const TYPE_JSON = 'json';
	const TYPE_NONE = 'none';
		
	/**
	 * Type of output (HTML, JSON, plain text)
	 * @var unknown_type
	 */
	protected $type;
	
	
}
