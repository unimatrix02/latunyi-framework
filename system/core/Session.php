<?php
namespace System\Core;

/**
 * Container for session data.
 */
class Session
{
	public function __construct()
	{
		session_start();
	}
	
	/**
	 * Method to retrieve data.
	 *
	 * @param string $key
	 */
	public function __get($key)
	{
		if (isset($_SESSION[$key]))
		{
			return $_SESSION[$key];
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
		$_SESSION[$key] = $value;
	}

	/**
	 * Utility method to clear the entire session.
	 */
	public function clear()
	{
		$_SESSION = array();
	}
	
	/**
	 * Method to get all session data
	 */
	public function all()
	{
		return $_SESSION;
	}
	
	public function __toString()
	{
		return print_r($_SESSION, true);
	}
}
