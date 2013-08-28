<?php
namespace System\Core\Database;

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
	 * Last PDO statement
	 * @var \PDOStatement
	 */
	protected $lastStatement;
	
	/**
	 * Default namespace for classes without namespace 
	 * @var string
	 */
	protected $defaultObjectNamespace;
	
	/**
	 * Constructor, stores the connection data and default object namespace.
	 * 
	 * @param 	ConnectionData 	$connData
	 * @param 	string 			$defaultObjectNamespace
	 */
	public function __construct(ConnectionData $connData, $defaultObjectNamespace)
	{
		$this->connData = $connData;
		$this->defaultObjectNamespace = $defaultObjectNamespace;
	}

	/**
	 * Sets up a database connection handle.
	 * 
	 * @throws Exception\Database
	 */
	private function connect()
	{
		$dsn = 'mysql:host=' . $this->connData->host . ';dbname=' . $this->connData->database .';charset=utf8';
		try
		{
			$this->handle = new \PDO($dsn, $this->connData->username, $this->connData->password);
			
			// Use exceptions for errors
			$this->handle->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
		}
		catch (\PDOException $ex)
		{
			throw new Exception\Database($ex);
		}
	}
	
	/**
	 * Returns data for the given query.
	 * 
	 * @param string $query
	 * @param array|object $params	Parameters, default null
	 * @param string	$class		Class name, default empty
	 */	
	public function getData($query, $params = null, $class = '')
	{
		$stmt = $this->runStatement($query, $params, $class);
		
		// Collect results
		$result = $stmt->fetchAll();
		
		// Return results
		return $result;
	}

	/**
	 * Returns a single row from the result set.
	 * 
	 * @param string $query
	 * @param array|object $params
	 * @param string $class
	 * @return object
	 */
	public function getRow($query, $params = null, $class = '')
	{
		$stmt = $this->runStatement($query, $params, $class);
		
		// Collect results
		$result = $stmt->fetch();
		
		// Return results
		return $result;
	}

	/**
	 * Returns a single field from all rows of the result set.
	 * 
	 * @param string $query
	 * @param array|object $params
	 * @param string $class
	 * @return array
	 */
	public function getField($query, $params = null, $class = '')
	{
		$stmt = $this->runStatement($query, $params, $class);
		
		$stmt->setFetchMode(\PDO::FETCH_COLUMN, 0);
		
		// Collect results
		$result = $stmt->fetchAll();
		
		// Return results
		return $result;
	}
	
	/**
	 * Returns a single value from the first row of the result set.
	 * 
	 * @param string $query
	 * @param array|object $params
	 * @return string
	 */
	public function getValue($query, $params = null)
	{
		$stmt = $this->runStatement($query, $params, '');
		
		// Collect results
		$result = $stmt->fetchColumn(0);
		
		// Return results
		return $result;
	}
	
	/**
	 * Creates and executes a PDO statement and returns it.
	 * 
	 * @param string $query
	 * @param array|object $params
	 * @param string $class
	 * @return \PDOStatement
	 * @throws \Exception
	 */
	private function runStatement($query, $params, $class)
	{
		$this->connect();
	
		$params = (array)$params;
	
		// Check query for named placeholders
		$placeholders = regex_matches('/(?<=\s):[a-z_]+(?=\s|,|$)/', $query);
		if (count($placeholders) > count($params))
		{
			throw new \Exception('Placeholder/parameter count mismatch (placeholders: ' . count($placeholders) . '; parameters: ' . count($params) . ')');
		}
		
		// Trim unnecessary parameters
		if (count($placeholders) < count($params))
		{
			$newParams = array();
			foreach ($placeholders as $ph)
			{
				$ph = substr($ph, 1);
				if (isset($params[$ph]))
				{
					$newParams[$ph] = $params[$ph];
				}
			}
			$params = $newParams;
		}
	
		// When params are empty...
		if (count($placeholders) == 0 && empty($params))
		{
			// Execute query immediately
			$stmt = $this->handle->query($query);
		}
		else
		{
			// Prepare statement with params
			$stmt = $this->handle->prepare($query);
	
			// Execute with params
			$stmt->execute((array)$params);
		}

		// Set fetch mode for selects
		if (substr($query, 0, 6) == 'SELECT')
		{
			// Retrieve as objects
			if (!empty($class))
			{
				// If just a class name was given, prefix with the default object namespace
				if (substr($class, 0, 1) != '\\')
				{
					$class = $this->defaultObjectNamespace . '\\' . $class;
				}
		
				// Check class exists
				if (!class_exists($class))
				{
					throw new \Exception('Failed to find class ' . $class . ' to fetch data into');
				}
		
				$stmt->setFetchMode(\PDO::FETCH_CLASS, $class);
			}
			else
			{
				$stmt->setFetchMode(\PDO::FETCH_OBJ);
			}
		}
	
		return $stmt;
	}

	/**
	 * Runs the given query and returns the number of affected rows.
	 * 
	 * @param string $query
	 * @param array|object $params
	 * @return int
	 */
	public function runQuery($query, $params)
	{
		$stmt = $this->runStatement($query, $params, '');
		$this->lastStatement = $stmt;
		return $stmt->rowCount();
	}

	/**
	 * Returns the number of rows affected by the last operation.
	 * 
	 * @return int
	 */
	public function getAffectedRows()
	{
		if (isset($this->lastStatement))
		{
			return $this->lastStatement->rowCount();
		}		
	}
	
	/**
	 * Returns the last inserted ID.
	 * 
	 * @return int
	 */
	public function getLastInsertedId()
	{
		if (isset($this->handle))
		{
			return $this->handle->lastInsertId();
		}
	}
	
	/**
	 *	Runs a query which selects only two fields, gets the data,
	 *	and formats the results to a key > value array. The first
	 *	field is used as key, the second as value.
	 *
	 *	@param	string	$sql	Query
	 *  @param QueryConditionList $params
	 *	@return	mixed			List
	 **/
	public function makeList($sql, $params)
	{
		// Get data
		if ($params instanceOf QueryParams)
		{
			$result = $this->getData($sql, $params->asArray());
		}
		else
		{
			$result = $this->getData($sql);
		}
	
		$list = array();
		if (count($result) > 0)
		{
			// Find out names of fields
			$fields = array_keys((array)$result[0]);
				
			foreach ($result as $item)
			{
				$item = (array)$item;
				$list[$item[$fields[0]]] = $item[$fields[1]];
			}
		}
		return $list;
	}
	
}
