<?php
namespace System\Core\Database;

/**
 * Class for holding database connection data.
 */
class ConnectionData
{
	public $host = 'localhost';
	public $port = 3306;
	
	public $database;
	
	public $username;
	public $password;
}
