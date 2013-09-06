<?php
/**
 *	Session class.
 *
 *	@author      Raymond van Velzen <raymond@latunyi.com>
 *	@package     LatunyiFramework
 **/

namespace System\Core;

/**
 * Container for session data.
 */
class Session
{
	/**
	 * Constructor, starts the session.
	 */
	public function __construct()
	{
		session_start();
	}
	
	/**
	 * Method to retrieve data.
	 *
	 * @param string $key
	 * @return mixed
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
	 * 
	 * @return array
	 */
	public function all()
	{
		return $_SESSION;
	}
	
	/**
	 * Returns the contents of the session as printed array.
	 * 
	 * @return string
	 */
	public function __toString()
	{
		return print_r($_SESSION, true);
	}
}
