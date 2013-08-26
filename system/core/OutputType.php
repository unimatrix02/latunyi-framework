<?php
namespace System\Core;

/**
 * Defines a type of output
 */
class OutputType
{
	const TYPE_HTML = 'html';
	const TYPE_JSON = 'json';
	const TYPE_TEXT = 'text';
	const TYPE_NONE = 'none';
		
	/**
	 * Type of output (HTML, JSON, plain text, none)
	 * @var string
	 */
	protected $type;
	
	/**
	 * Constructor, sets the given type, if valid.
	 * 
	 * @param string $type
	 * @throws \Exception
	 */
	public function __construct($type)
	{
		$refClass = new \ReflectionClass(get_class($this));
		$const = $refClass->getConstants();
		if (!in_array($type, $const))
		{
			throw new \Exception('Invalid output type ' . $type);
		}
		
		$this->type = $type;
	}

	/**
	 * Returns the type as string.
	 */
	public function __toString()
	{
		return $this->type;
	}
}
