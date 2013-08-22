<?php
namespace System\Core;

/**
 * Class for loading of configuration data.
 */
class ConfigData
{
	/**
	 * List of configuration file data.
	 * @var array
	 */
	private $data;

	public function __construct($data)
	{
		$this->data = (object)$data;
	}

	/**
	 * Method to retrieve config data.
	 *
	 * @param string $key
	 */
	public function __get($key)
	{
		if (isset($this->data->$key))
		{
			return $this->data->$key;
		}
		else
		{
			throw new Exception('Failed to find configuration key ' . $key);
		}
	}

}
