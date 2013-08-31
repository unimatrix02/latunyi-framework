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
	private $_data;

	public function __construct($data = null)
	{
		if (!empty($data))
		{
			$this->_data = $data;
		}
	}

	/**
	 * Method to retrieve data.
	 *
	 * @param string $key
	 */
	public function __get($key)
	{
		// Look for properties first
		if ($key != '_data' && property_exists($this, $key))
		{
			return $this->$key;
		}
		
		if (isset($this->_data[$key]))
		{
			return $this->_data[$key];
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
		// Look for property first
		if ($key != '_data' && property_exists($this, $key))
		{
			$this->$key = $value;
		}
		else
		{
			$this->_data[$key] = $value;
		}
	}
	
	/**
	 * Returns the content of the _data property.
	 * 
	 * @return array
	 */
	public function getData()
	{
		return $this->_data;
	}

	/**
	 * Checks if the given key exists in the _data property.
	 * 
	 * @param string $key
	 * @return bool
	 */
	public function has($key)
	{
		return isset($this->_data[$key]);
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
				if (strlen($key) > 0)
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

	public function asArray()
	{
		$data = array();
		foreach ($this->_data as $key => $val)
		{
			if ($val instanceOf self)
			{
				$data[$key] = $val->asArray();
			}
			else
			{
				$data[$key] = $val;
			}
		}
		return $data;
	}

}
