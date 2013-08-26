<?php
/*========================================================================
                    _          _                     _ 
                   | |    __ _| |_ _   _ _ __  _   _(_)
                   | |   / _` | __| | | | '_ \| | | | |
                   | |__| (_| | |_| |_| | | | | |_| | |
                   |_____\__,_|\__|\__,_|_| |_|\__, |_|
                                               |___/   

=========================================================================*/

/**
*	Provides connection to a MySQL 4.1+ database. This is a PHP 5 class.
*
*	Takes care of setting up connection, executing queries and returning 
*	results. Uses MySQLi extension.
*
*	@author      	Raymond van Velzen <raymond@latunyi.com>
*	@copyright		Latunyi
*	@package     	Meridium
**/
class Database {

	/**
	*	@var string $Host Name of the database server (IP address or hostname)
	**/
	private $Host;

	/**
	*	@var	int	$Port	Port on which to connect to database server
	**/
	private $Port;

	/**
	*	@var	string	$Username	Username to use for connection to database server
	**/
	private $Username;
	
	/**
	*	@var	string	$pass		Password to use for connection to database server
	**/
	private $Password;

	/**
	*	@var	string	$Database	Name of database to use 
	**/
	private $Database;

	/**
	*	@var	object	$Connection	Pointer to a database object, shared
	**/
	private $Connection;

	/**
	*	@var	string	$Query		SQL query string
	**/
	private $Query;

	/**
	*	@var	object	$ResultSet	Pointer to a resultset object
	**/
	private $ResultSet;

	/**
	*	@var	object	$Statement	Pointer to a prepared statement object
	**/
	private $Statement;

	/**
	*	@var	int	$ErrorCode		Last error code
	**/
	private $ErrorCode;

	/**
	*	@var	string	$ErrorText	Text corresponding to the last error
	**/
	private $ErrorText;

	/**
	*	@var	int	$RowCount	Number of rows returned from last query
	**/
	private $RowCount;

	/**
	*	@var	array	$CurrentRow	Variable holding array with last row fetched from database
	**/
	private $CurrentRow;

	/**
	*	@var	bool	$ReturnAsObjects		Return results as StdClass objects, default false
	**/
	private $ReturnAsObjects = false;

	/**
	*	Get instance
	*
	*	Creates a new named object of this class and returns it. If
	*	the instance already exists, returns the existing object.
	*
	*	@param	string	$instance_id	Internal ID to use for instance. Default: "default" 
	**/
	static public function GetInstance($config_data = array(), $instance_id = 'default') 
	{
		// Array with instances
		static $instances = array();
		
		$class = __CLASS__;

		// Create new instance 
		if (!isset($instances[$instance_id]))
		{
			// Use constants if config empty
			if (empty($config_data))
			{
				$config_data['host'] = DB_HOST;			
				$config_data['port'] = DB_PORT;			
				$config_data['username'] = DB_USERNAME;			
				$config_data['password'] = DB_PASSWORD;
				$config_data['database'] = DB_NAME;
			}

			// Add new object
			$instances[$instance_id] = new $class($config_data);
		}
		
		// Reset ReturnAsObjects to default (false)
		$instances[$instance_id]->ReturnAsObjects = false; 

		return $instances[$instance_id];
	}

	// -------------------------------------------------------------------

	/**
	*
	*	Creates a new object
	*
	*	@param	mixed	$db_config		Array with configuration
	*
	**/
	private function __construct($db_config) 
	{
		if (empty($db_config))
		{
			throw new Exception('Failed to create Database object: Required configuration parameters are missing.');
		}

		// Set connection parameters
		$this->Host		= $db_config['host'];
		$this->Port		= 3306;
		$this->Username = $db_config['username'];
		$this->Password = $db_config['password'];
		$this->Database = $db_config['database'];

		// initialize error state
		$this->ErrorCode = 0;
		$this->ErrorText = 'OK';

	}

	// -------------------------------------------------------------------

	/**
	*
	*	Checks the connection and if necessary, activates it
	*
	*	@return	void
	**/
	private function GetConnection()
	{
		// Check if connection already exists
		if (!isset($this->Connection)) 
		{
			// Create new object for connection
			$this->Connection = mysqli_init();
			$result = @$this->Connection->real_connect($this->Host, $this->Username, $this->Password, $this->Database, $this->Port, NULL, MYSQLI_CLIENT_COMPRESS);

			// Check for error 
			if ($result === false) 
			{
				throw new Exception('Failed to connect to database. Error: '.mysqli_connect_error(), mysqli_connect_errno());
			} 

			// Set UTF8
			$this->Connection->query("SET NAMES utf8");
		}
	}

	// -------------------------------------------------------------------

	/**
	*
	*	Sets the current query
	*
	*	@param	string	$query	SQL query string
	*	@return	bool True
	*
	**/
	public function SetQuery($query) 
	{
		//sets query string
		$this->Query = $query;
		unset ($this->ResultSet);
		$this->ErrorCode = 0;
		$this->ErrorText = "OK";
		return true;
	}

	// -------------------------------------------------------------------

	/**
	*
	*	Gets the current query
	*
	*	@return	string Current query
	*
	**/
	public function GetQuery() 
	{
		if (isset($this->Query)) {
			return $this->Query;
		} else {
			return '';
		}
	} 
	
	// -------------------------------------------------------------------

	/**
	*	Prepare
	*
	*	Starts with a new prepared statement and returns
	*	a handle.
	*
	*	@param		string	$query	SQL query
	*	@return		object			Statement object
	*
	**/
	public function Prepare($query) 
	{
		// Check connection
		$this->GetConnection();

		$this->Statement = $this->Connection->stmt_init();
		$result = $this->Statement->prepare($query);
		
		if (!$result)
		{
			throw new Exception('Failed to initialize or prepare statement. Error: ' . $this->Statement->error);
		}
	} 

	// -------------------------------------------------------------------

	/**
	*	Bind
	*
	*	Binds parameters to a prepared statement.
	*
	*	@param		mixed	$params		Parameters for query
	*	@return		void	
	*
	**/
	public function Bind($params) 
	{
		$types = '';
		$values = array();
		for ($i = 0; $i < count($params); $i++)
		{
			$value = $params[$i];
			if (is_string($value))
			{
				$types .= 's';
			}
			if (is_int($value))
			{
				$types .= 'i';
			}
			if (is_double($value))
			{
				$types .= 'd';
			}
			$values[] = $value;
		}

		// Build PHP statement to bind params
		$code = '$result = $this->Statement->bind_param(\'' . $types . '\'';
		for ($i = 0; $i < count($values); $i++)
		{
			$code .= ', $values[' . $i . ']';
		}
		$code .= ');';
		eval($code);

		if (!$result)
		{
			throw new Exception('Failed to bind parameters to prepared statement. Error: ' . $this->Statement->error);
		}
	}

	// -------------------------------------------------------------------

	/**
	*	Query2
	*
	*	Executes a query using a prepared statement.
	*
	*	@param		string	$query		SQL query with ? parameters
	*	@param		mixed	$params		Parameters for query
	*	@return		void
	*
	**/
	public function Query2($query, $params) 
	{
		// Prepare & bind params
		$this->Prepare($query);
		$this->Bind($params);
		
		// Execute
		if (!$this->Statement->execute())
		{
			throw new Exception('Failed to execute prepared statement. Error:' . $this->Statement->error);
		}
		
		/*
		// Get metadata
		$meta = $this->Statement->result_metadata();
		if (!$meta)
		{
			throw new Exception('Failed to get metadata for result. Error:' . $this->Statement->error);
		}

		// Get results, if any		
		if (isset($meta)) 
		{
			// Get field names
			$fields = $meta->fetch_fields();
			if (!$fields)
			{
				throw new Exception('Failed to get fields from metadata.');
			}
			$list = array();
			foreach($fields as $field)
			{
				$list[] = $field->name;
			}
			unset($fields);
			
			// Bind results
			$code = '$result = $this->Statement->bind_result(';
			for ($i = 0; $i < $meta->field_count; $i++)
			{
				$vars[] = '$$list[' . $i . ']';
			}
			$code .= implode(', ', $vars) . ');';
			eval($code);
			
			// Fetch results
			$data = array();
			while ($this->Statement->fetch()) 
			{
				$row = array();
				foreach($list as $field)
				{
	        		$row[$field] = $$field;
	        	}
	        	$data[] = $row;
	        	unset($row);
		    }			
			return $data;
		}
		*/
		
	}
			
	// -------------------------------------------------------------------

	/**
	*
	*	Executes the current query and stores the resultset in the $ResultSet property.
	*
	*	@return	void
	**/
	private function ExecuteQuery() 
	{
		// Check resultset
		if (isset($this->ResultSet)) 
		{
			return;
		} 
		else 
		{
			// Execute query
			$this->ResultSet = $this->Connection->query($this->Query);

			// Check for error
			if ($this->ResultSet === false) 
			{
				$this->ThrowException('Failed to execute query');
			}
		}
	} 
	
	// -------------------------------------------------------------------

	/**
	*
	*	Executes the current multi query and takes the first resultset.
	*	Used to execute stored procedures.
	*
	*	@return	bool True/False
	**/
	private function ExecuteMultiQuery() {

		// Check resultset
		if (isset($this->ResultSet)) 
		{
			return;
		} 
		else 
		{
			$error = false;
			
			if (!$this->Connection->multi_query($this->Query))
			{
				$this->ThrowException('Failed to execute multi query');
			}

			// Get first resultset
			$this->ResultSet = $this->Connection->store_result();

			if ($this->ResultSet === false)
			{
				$this->ThrowException('Failed to execute multi query');
			}
			
			// Iterate over other resultsets to avoid 'command out of sync' afterwards (MySQL bug)
			if ($this->Connection->more_results())
			{
				do
				{
					if ($result = $this->Connection->store_result())
					{
						$result->free();
					}
				} 
				while ($this->Connection->next_result());
			}
		}
	} 
	
	// -------------------------------------------------------------------

	/**
	*
	*	Executes the current query; the result is stored in the $ResultSet property.
	*	Use this method directly for queries with no results; otherwise, use GetData().
	*
	*	@return	bool	True/false
	*
	**/
	public function Query() {

		// Set query
		if (func_num_args() == 1) 
		{
			$this->Query = func_get_arg(0);
			unset ($this->ResultSet);
		}

		// Check connection
		$this->GetConnection();

		// Check for stored procedure call
		$multiquery = (substr($this->Query, 0, 4) == 'CALL');
		
		// Log query
		if (SQL_DEBUG)
		{
			Log::Add($this->Query);
		}

		// Execute query
		if (!isset($this->ResultSet)) 
		{
			if (!$multiquery) 
			{
				$this->ExecuteQuery();
			} 
			else 
			{
				$this->ExecuteMultiQuery();
			}
		}
	}
	
	// -------------------------------------------------------------------

	/**
	*
	*	Gets the next row from the current resultset
	*
	*	@return	array|false		Next record (as array), or false if there are no more records
	*
	**/
	public function GetNextRow() 
	{
		// Check connection
		$this->GetConnection();

		// Check result
		if (!isset($this->ResultSet)) 
		{
			if(!$this->ExecuteQuery())
			{
				return false;
			}
		}

		// Get row
		if ($this->CurrentRow = $this->ResultSet->fetch_assoc()) { //try to fecth record
			return $this->CurrentRow;
		} 
		else 
		{ 
			// Couldn't fetch a row
			if ($this->Connection->errno > 0) 
			{
				$this->ThrowException('Failed to get row');
			}
			return false;
		}

	}
	
	// -------------------------------------------------------------------

	/**
	*
	*	Returns results (all rows) from query as array
	*
	*	This is a wrapper around Query() and GetNextRow(), so you don't
	*	have to fetch each row and copy it to an array. 
	*	Returns a nested array if the result contains more than 1 row.
	*	
	*	You can also indicate if you want the results to be unwrapped.
	*	This is used in different ways, depending on the result set.
	*	If the resultset only contains 1 row, setting unwrap to true
	*	will return the array in the first element of the resultset.
	*	Example:
	*	array ([0] => array('somefield' => 'somevalue'));
	*	will be returned as
	*	array('somefield' => 'somevalue');
	*	If the resultset contains more than one row, but each row
	*	contains only 1 field, setting unwrap to true will flatten
	*	the resultset. Example:
	*	array ([0] => array('onlyfield' => 'some value'), 
	*	[1] => array('onlyfield' => 'some other value');
	*	will be returned as:
	*	array ([0] = 'some value', [1] => 'some other value');
	*	If the resultset contains one row with one field,
	*	the result will be the field value.
	*	Example: array([0] => array('onlyfield' => 'some value'))
	*	will be returned as 'some value';
	*
	*	If you expect the resultset to have 1 field, but you don't
	*	know the number of results, set $multi_row to true to avoid
	*	getting a fieldvalue if there is 1 row and an array when there
	*	are more rows.
	*	
	*	@param	string	$sql_query	SQL query
	*	@param	bool	$unwrap_row		Unwrap result row from nested array if only 1 result (default: false)
	*	@param	bool	$unwrap_field	Unwrap field value from array if there is only 1 field in a row (default: false)
	*	@return	array|false		Array with results from query, or false if query fails
	*
	*/
	public function GetData($sql_query, $unwrap_row = false, $unwrap_field = false) 
	{
		if (strlen($sql_query) > 0) 
		{
			// Get query result
			$result = $this->Query($sql_query);
			if ($result !== false) 
			{
				// Get rows from resultset
				$rows = array();
				while ($row = $this->GetNextRow()) 
				{
					$rows[] = $row;
				}
				
				// Close result set
				$this->ResultSet->close();

				// Empty result set
				if (count($rows) == 0) 
				{
					return $rows;
				} 
				else 
				{
					$keys = array_keys($rows[0]);

					// Flatten row to single value if necessary, when row has single field
					if ($unwrap_field && count($keys) == 1)
					{
						foreach ($rows as &$row)
						{
							$row = $row[$keys[0]];
						}
						
						// Can't return objects with single value
						$this->ReturnAsObjects = false;
					}

					// Unwrap from nested array, if there is a single row
					if ($unwrap_row && count($rows) == 1)
					{
						if ($this->ReturnAsObjects)
						{
							$this->MakeObject($rows[0]);
						}
						return $rows[0];
					}

					// Make StdClass objects, if necessary
					if ($this->ReturnAsObjects)
					{
						$this->MakeObjects($rows);
					}

					return $rows;
				}
			} 
			else 
			{
				// query failure
				return false;
			}
		} 
		else 
		{
			// query empty
			return false;
		}

	} 
	
	// -------------------------------------------------------------------

// EXTRA FUNCTIONS ================================================================================

	/**
	*
	*	Wraps the results of GetData in extra arrays, so they can be converted
	*	to XML. Data is assumed to look like this:
	*	Array(
	*		[0] => Array (
	*						[key1] => value 1
	*						[key2] => value 2
	*						...
	*				)
	*		[1] => ...
	*
	*	The results will be like this:
	*	Array (
	*			[items] =>
	*					[0] => 
	*							Array (
	*										[item] => 
	*												Array (
	*													[key1] => value 1
	*													[key2] => value 2
	*												)
	*									)
	*					[1] => ...
	*
	*	@param	array		$data				Pointer to array to be wrapped
	*	@param	string	$item_root		Name for root 'tag', default 'items'
	*	@param	string	$item_name		Name for items, default 'item'
	*	@return	bool		True on success, or false in case of error
	*
	**/
	public function WrapResults(&$data, $item_root = 'items', $item_name = 'item') {

		// Check for array
		if (!is_array($data)) {
			return false;
		}

		// Check for non-empty array 
		if (count($data) <= 0) {
			return false;
		}

		// Wrap results
		reset($data);
		while ($item = each($data)) {
			$data[$item['key']] = array($item_name => $item['value']);
		}

		// Wrap array itself
		$data = array($item_root => $data);
		return true;

	} 
	
	// -------------------------------------------------------------------

	/**
	*
	*	Gets the number of rows affected by last query
	*
	*	@return	int|false	Number of affected rows, or false if an error occured
	*
	**/
	public function GetAffectedRows() 
	{
		// Check connection
		$this->GetConnection();

		return ($this->Connection->affected_rows);

	}
	
	// -------------------------------------------------------------------

	/**
	*
	*	Gets the auto-increment ID that was last inserted
	*
	*	@return int|false	Last insert ID, or false if an error occured
	*
	**/
	public function GetInsertId() 
	{
		// Check connection
		$this->GetConnection();

		return ($this->Connection->insert_id);

	} 
	
	// -------------------------------------------------------------------

	/**
	*
	*	Gets the number of rows returned from last query. Wrapper for 
	*	GetRowCount; only provided for backward compatibility.
	*
	*	@return	int|false Number of rows or False
	*
	**/
	public function GetNumRows() {
		return $this->GetRowCount();
	}

	// -------------------------------------------------------------------

	/**
	*
	*	Gets the number of rows returned from last query
	*
	*	@return	int|false Number of rows or False
	*
	**/
	public function GetRowCount() 
	{
		// Check connection
		$this->GetConnection();

		// Check resultset
		if (!isset($this->ResultSet)) {
			if(!$this->ExecuteQuery()){
				return false;
			}
		}

		// Get row count
		$this->RowCount = $this->ResultSet->num_rows;
		return $this->RowCount;

	}

	// -------------------------------------------------------------------

	/**
	*
	*	Gets the code of the last error
	*
	*	@return	int Error code
	*
	**/
	public function GetErrorCode() {

		//returns error code
		return $this->ErrorCode;

	}

	// -------------------------------------------------------------------

	/**
	*
	*	Escapes a string.
	*
	*	@param	string	$str	String to escape
	*	@return	string			Escaped string
	**/
	public function Escape($str) 
	{
		// Check connection
		$this->GetConnection();

		return ($this->Connection->escape_string($str));
	} 
	
	// -------------------------------------------------------------------

	/**
	*	SetAutoCommit
	*	
	*	Turns autocommitting on or off.
	*
	*	@param	bool	$value	True/false
	*	@return	void
	**/
	public function SetAutoCommit($value) 
	{
		// Check connection
		$this->GetConnection();

		$result = $this->Connection->autocommit($value);
		if (!$result)
		{
			throw new Exception('Error setting autocommit to ' . $value);
		}
	} 

	// -------------------------------------------------------------------

	/**
	*	Begin
	*	
	*	Starts a new transaction. Notice: This will automatically turn off
	*	autocommit until the next commit or rollback.
	*
	*	@return	void
	**/
	public function Begin() 
	{
		// Check connection
		$this->GetConnection();

		$result = $this->Connection->query('START TRANSACTION');
		if (!$result)
		{
			throw new Exception('Error starting transaction');
		}
	} 

	// -------------------------------------------------------------------

	/**
	*	Commit
	*	
	*	Commits the current transaction. 
	*
	*	@return	void
	**/
	public function Commit() 
	{
		// Check connection
		$this->GetConnection();

		// Commit transaction
		$result = $this->Connection->commit();
		if (!$result)
		{
			throw new Exception('Error committing transaction.');
		}
	} 

	// -------------------------------------------------------------------

	/**
	*	Rollback
	*	
	*	Rolls the current transaction back. 
	*
	*	@return	void
	**/
	public function Rollback() 
	{
		// Check connection
		$this->GetConnection();

		// Rollback transaction
		$result = $this->Connection->rollback();
		if (!$result)
		{
			throw new Exception('Error on rollback transaction.');
		}
	} 

	// -------------------------------------------------------------------

	/**
	*	MakeList
	*	
	*	Runs a query which selects only two fields, gets the data,
	*	and formats the results to a key > value array. The first
	*	field is used as key, the second as value.
	*
	*	@param	string	$sql	Query
	*	@return	mixed			List
	**/
	public function MakeList($sql) 
	{
		// Get data
		$result = $this->GetData($sql);
		
		$list = array();
		if (count($result) > 0)
		{
			// Find out names of fields
			$fields = array_keys($result[0]);
			
			foreach ($result as $item)
			{
				$list[$item[$fields[0]]] = $item[$fields[1]];
			}
		}
		return $list;
	} 

	// -------------------------------------------------------------------

	/**
	*	GetUniqueId
	*	
	*	Returns a unique ID (UUID).
	*
	*	@return	string Unique ID
	*
	**/
	public function GetUniqueId() 
	{
		$sql = "SELECT UUID()";
		$result = $this->GetData($sql, true);
		return strtoupper($result);
	} 
	
	// -------------------------------------------------------------------

	/**
	*
	*	Gets the text of the last error
	*
	*	@return	string Error text
	*
	**/
	public function GetError() {

		//returns error text
		return $this->ErrorText;

	} 

	// -------------------------------------------------------------------

	/**
	*	Throws an exception, setting the given message as message text,
	*	appended with the MySQL error description, and setting the error
	*	code to the MySQL error number.
	*
	*	@param	string	$msg	Message
	*	@return	void
	**/
	private function ThrowException($msg) 
	{
		$msg .= '. Error: ' . $this->Connection->error;
		$msg .= '. Query: ' . $this->Query;
		throw new Exception($msg, $this->Connection->errno);
	} 

	// -------------------------------------------------------------------
	
	/**
	*	MakeObjects
	*	
	*	Turns the given array of results into StdClass objects.
	*
	*	@param		mixed	$data	Array with records as array, by ref
	*	@return		void
	**/
	public function MakeObjects(&$data) 
	{
		foreach ($data as &$rec)
		{
			$this->MakeObject($rec);
		}
	}

	// -------------------------------------------------------------------
	
	/**
	*	MakeObject
	*	
	*	Turns the given array into a single StdClass object.
	*
	*	@param		mixed	$data	Array with data, by ref
	*	@return		void
	**/
	public function MakeObject(&$data) 
	{
		$obj = new StdClass;
		foreach ($data as $field => $value)
		{
			$obj->$field = $value;
		}
		$data = $obj;
	}

	// -------------------------------------------------------------------
	
	/**
	*	ReturnObjects
	*	
	*	Sets ReturnAsObjects to true.
	*
	*	@return		void
	**/
	public function ReturnObjects() 
	{
		return $this->ReturnAsObjects = true;
	}

	// -------------------------------------------------------------------

	/**
	*
	* Closes the connection
	*
	**/
	public function __destruct() {
		if (isset($this->Connection)) {
			@$this->Connection->close();
		}
	}

} // end class MySQLiDatabase

?>
