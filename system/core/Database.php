<?php
namespace System\Core;

/**
 * Class for MySQL database operations using PDO.
 */
class Database
{
	/**
	 * Database connection data object
	 * @var \System\Core\DbConnData
	 */
	protected $connData;
	
	/**
	 * PDO handle
	 * @var \PDO
	 */
	protected $handle;
	
	/**
	 * Constructor, stores the connection data.
	 * 
	 * @param DbConnData $connData
	 */
	public function __construct(DbConnData $connData)
	{
		$this->connData = $connData;
	}
	
	private function connect()
	{
		$dsn = 'mysql:host=' . $this->connData->host . ';dbname=' . $this->connData->database;
		try
		{
			$this->handle = new \PDO($dsn, $this->connData->username, $this->connData->password);
		}
		catch (\PDOException $ex)
		{
			throw new Exception\Database($ex);
		}
	}
	
	public function getData($sql)
	{
		$this->connect();
	}
}
