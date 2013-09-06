<?php
namespace System\Core\Database;

/**
 * Class for holding database connection data.
 */
class ConnectionData
{
	/**
	 * Hostname
	 * @var string
	 */
	public $host = 'localhost';
	
	/**
	 * Port number
	 * @var int
	 */
	public $port = 3306;
	
	/**
	 * Database name
	 * @var string
	 */
	public $database;
	
	/**
	 * Username
	 * @var string
	 */
	public $username;
	
	/**
	 * Password
	 * @var string
	 */
	public $password;
}
