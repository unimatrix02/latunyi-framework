<?php
namespace System\Core;

/**
 * Class for holding data.
 */
class DataContainer
{
	/**
	 * List for holding data.
	 * @var array
	 */
	private $data;

	public function __construct($data = null)
	{
		if (!empty($data))
		{
			$this->data = $data;
		}
	}

	/**
	 * Method to retrieve data.
	 *
	 * @param string $key
	 */
	public function __get($key)
	{
		if (isset($this->data[$key]))
		{
			return $this->data[$key];
		}
		else
		{
			throw new \Exception('Failed to find key ' . $key);
		}
	}

	/**
	 * Method to set data.
	 * 
	 * @param string $key
	 * @param mixed $value
	 */
	public function __set($key, $value)
	{
		$this->data[$key] = $value;
	}
	
	/**
	 * Utility method to recursively produce a DataContainer from array data.
	 * 
	 * @param array $data
	 * @return \System\Core\DataContainer|boolean
	 */
	static public function makeObject($data)
	{
		if (!is_array($data))
		{
			return $data;
		}
	
		if (is_array($data) && !empty($data))
		{
			$obj = new \System\Core\DataContainer();
			foreach ($data as $key => $val)
			{
				$key = strtolower(trim($key));
				if (!empty($key))
				{
					$obj->$key = self::makeObject($val);
				}
			}
			return $obj;
		}
		else
		{
			return false;
		}
	}
	
}
