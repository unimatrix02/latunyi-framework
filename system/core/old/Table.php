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
*	The Table class provides functions for working with a 
*	database table.
*
*	@author      	Raymond van Velzen <raymond@latunyi.com>
*	@copyright		Latunyi
*	@package     	Meridium
**/
class Table
{
    /**
    *	$table_name
    * 
	*	Table name from db
	*
    *	@var     string
    **/
	protected $table_name;

    /**
    *	$order_by
    * 
	*	Order by (default)
	*
    *	@var     string
    **/
	protected $order_by;

    /**
    *	$fields
    * 
	*	Array of Field objects
	*
    *	@var     mixed
    **/
	protected $fields;

    /**
    *	$has_foreign_fields
    * 
	*	Boolean, default false
	*
    *	@var     mixed
    **/
	protected $has_foreign_fields = false;

    /**
    *	$has_custom_fields
    * 
	*	Boolean, default false
	*
    *	@var     mixed
    **/
	protected $has_custom_fields = false;

    /**
    *	$pk_is_auto_incr
    * 
	*	Primary key is auto-increment. Boolean, default true
	*
    *	@var     mixed
    **/
	protected $pk_is_auto_incr = true;

    /**
    *	$foreign_fields
    * 
	*	Array of foreign field names, empty by default
	*
    *	@var     mixed
    **/
	protected $foreign_fields = array();

    /**
    *	$primary_key
    * 
	*	Primary key
	*
    *	@var     string
    **/
	protected $primary_key;

    /**
    *	$db
    * 
	*	Database connection
	*
    *	@var     object
    **/
	protected $db;

    /**
    *	$last_query
    * 
	*	SQL of last query 
	*
    *	@var     string
    **/
	protected $last_query;

    /**
    *	$table_aliases
    * 
	*	Array of table aliases from last query
	*
    *	@var     mixed
    **/
	protected $table_aliases;

    /**
    *	$select_fields
    * 
	*	Array of fields to select from table. 
	*
    *	@var     mixed
    **/
	protected $select_fields;

    /**
    *	$return_obj
    * 
	*	Return results as objects or arrays, default true
	*
    *	@var     mixed
    **/
	protected $return_obj = true;

	// -------------------------------------------------------------------

	/**
	*	Constructor
	*
	*	Takes an array of DataField objects, initializes the object and
	*	sets up the Database object. 
	*
	*	@param		mixed	$fields		Array of DataField objects, by ref
	*	@return		void
	**/
	public function __construct(&$fields) 
	{
		$this->fields = $fields;
	
		// Iterate over fields
		foreach ($fields as &$field)
		{
			// Add TableField objects
			if ($field->IsForeign()) 
			{
				$this->has_foreign_fields = true;
				$this->foreign_fields[] = $field->foreign_field->GetAlias();
			}
			
			// Detect primary key
			if ($field->IsPrimaryKey())
			{
				$this->primary_key = $field->name;
			}
			
			// Detect custom fields
			if ($field->type == 'custom')
			{
				$this->has_custom_fields = true;
			}
		}
		
		// Get database connection
		$this->db = Database::GetInstance();
	}

	// -------------------------------------------------------------------

	/**
	*	SetSelectFields
	*
	*	Sets the fields that should be used for the SELECT clause.
	*
	*	@param		mixed		$fields		Array of fields
	*	@return		void
	**/
	public function SetSelectFields() 
	{
		$fields = func_get_args();
		if (empty($fields))
		{
			throw new Exception('Select fields list is not an array or empty.');
		}

		// Check if fields exist in fields collection
		foreach($fields as &$item)
		{
			$found = false;
			foreach ($this->fields as &$field)
			{
				if ($item == $field->getName())
				{
					$found = true;
					break;
				}
			}
			
			if (!$found)
			{
				throw new Exception('There is no field "' . $item . '"');
			}

			// Add to list
			if ($this->has_foreign_fields)
			{
				$item = 't1.`' . $item . '`';
			}
			else
			{
				$item = '`' . $this->table_name . '`.`' . $item . '`';
			}
		}
		
		$this->select_fields = $fields;
	}

	// -------------------------------------------------------------------

	/**
	*	GetRecord
	*
	*	Get a single record by id.
	*
	*	@param		mixed		$id		ID of record
	*	@return		mixed				Array with record
	**/
	public function GetRecord($id) 
	{
		// Build SQL
		$sql = $this->MakeSelectAndFrom();

		// Build SQL
		if (($alias = $this->TableHasAlias($this->table_name, $this->table_aliases)) !== false) 
		{
			$sql .= 'WHERE '.$alias.'.`'.$this->primary_key.'` = \''.$id.'\'';
		} 
		else 
		{
			$sql .= 'WHERE `'.$this->table_name.'`.`'.$this->primary_key.'` = \''.$id.'\'';
		}
		
		// Get data (unwrapped)
		$result = $this->db->GetData($sql);

		// Remember query
		$this->last_query = $sql;

		// Check for error 
		if ($result === false) 
		{
			throw new Exception($this->db->GetError(), 0, $sql);
		}

		$result = $result[0];

		if ($this->return_obj)
		{
			$this->db->MakeObject($result);
		}

		return $result;
	} 

	// -------------------------------------------------------------------

	/**
	*	InsertRecord
	*
	*	Add a record to the table.
	*
	*	@param		array	$params		Array with fields and values, by ref
	*	@param		bool	$replace	Use REPLACE INTO instead of INSERT. Default false.
	*	@return		void
	**/
	public function InsertRecord(&$params, $replace = false) 
	{
		// Build SQL
		$sql = ($replace ? 'REPLACE ' : 'INSERT ');
		$sql .= 'INTO `'.$this->table_name.'` SET ';
		$values = array();
		foreach($params as $key => $val) 
		{
			// Check field name and value
			try 
			{
				// Check fields, but not primary key
				if ($key != $this->primary_key)
				{
					$result = $this->CheckField($key, $val);
					if ($result === false)
					{
						continue;
					}				
				}

				// Always add slashes
				$val = $this->db->Escape($val);

				// Process special values
				if ($val === 'NULL') 
				{
					$values[] = "`$key` = NULL";
				} 
				elseif (substr($val, 0, 2) == '__')
				{
					// Literal SQL 
					$val = substr($val, 2);
					$values[] .= " `$key` = " . $val;
				} 
				else 
				{
					$values[] = "`$key` = '$val'";
				}
			
			} 
			catch (Exception $ex) 
			{
				throw new Exception($ex->GetMessage());
			}
 		}

		// Add values to query
		$sql .= implode(', ', $values);

		// No values: exception
		if (count($values) == 0)
		{
			throw new Exception('No fields were added to the INSERT query');
		}

		// Remember query
		$this->last_query = $sql;

		// Execute query
		$result = $this->db->Query($sql);

		// Check for error 
		if ($result === false) 
		{
			throw new Exception($this->db->GetError(), 0, $sql);
		}
	} 

	// -------------------------------------------------------------------

	/**
	*	CheckField
	*
	*	Check if a field exists and the value is correct. Throws exception
	*	if field is not found or value is not correct.
	*
	*	@param		string	$field_name		Field name
	*	@param		string	$value		Value by ref
	*	@return		void
	**/
	private function CheckField($field_name, &$value) 
	{
		// Iterate over fields
		$found = false;
		$value_ok = false;
		foreach ($this->fields as &$field)
		{
			$name = $field->GetName();
			if ($name == $field_name) 
			{
				$found = true;
				
				$type = $field->GetType();
				
				if ($type == 'int' && (is_numeric($value) || $value == 'NULL')) 
				{
					$value_ok = true;
				}
				if ($type == 'number' && (is_numeric($value) || $value == 'NULL')) 
				{
					$value_ok = true;
				}
				if ($type == 'string' && (is_string($value) || $value == 'NULL')) 
				{
					$value_ok = true;
				}
				
				if ($type == 'datetime')
				{
					if (empty($value))
					{
						$value = 'NULL';
						$value_ok = true;
					}
					else
					{
						// Check date/time
						if (date('Y-m-d H:i:s', strtotime(trim($value))) == $value)
						{
							$value_ok = true;
						}
					}
				}

				if ($type == 'date') 
				{
					if (empty($value))
					{
						$value = 'NULL';
						$value_ok = true;
					}
					else
					{
						// Convert Dutch dates
						if (preg_match('/^\d\d-\d\d-\d{4}$/', $value) > 0)
						{
							$value = date('Y-m-d', strtotime($value));
						}
					
						// Check date
						if (date('Y-m-d', strtotime(trim($value))) == $value)
						{
							$value_ok = true;
						}
					}
				}
				break;
			}
		}

		// Not found
		if (!$found) 
		{
			return false;
		}
		else
		{
			// Value is wrong: exception
			if (!$value_ok) {
				throw new Exception('Value "'.$value.'" for field "'.$field_name.'" is not valid.');
			}
		}
	}

	// -------------------------------------------------------------------

	/**
	*	UpdateRecord
	*
	*	Update a record in the table.
	*
	*	@param		array	$params		Array with fields and values, including primary key, by ref
	*	@return		void
	**/
	public function UpdateRecord(&$params) 
	{
		// Build SQL
		$sql = 'UPDATE `'.$this->table_name.'` SET ';
		$values = array();
		$pk_found = false;
		$field_count = 0;
		foreach($params as $key => $val) 
		{
		  // Check field name and value
			try {
				$result = $this->CheckField($key, $val);
				if ($result === false)
				{
					continue;
				}
				$field_count++;

				// Always escape
				$val = $this->db->Escape($val);
				
				// Include value, but not if primary key and table has auto-incr PK
				$include = true;
				if ($key == $this->primary_key)
				{
					if ($this->HasAutoIncrPk())
					{
						$include = false;
					}

					// Found non-empty primary key
					if (!empty($val))
					{
						$pk_found = true;
					}
				}

				// Include and process special value
				if ($include) 
				{
					if ($val === 'NULL') 
					{
						$values[] = "`$key` = NULL";
					} 
					else 
					{
						$values[] = "`$key` = '$val'";
					}
				}

			} 
			catch (Exception $ex) 
			{
				throw new Exception($ex->GetMessage());
			}
 		}
 		
 		// Quit if has no primary key
 		if (!$pk_found)
 		{
			throw new Exception('Cannot update: The supplied data does not contain a primary key');
 		}

 		// No fields were found - update makes no sense
 		if (count($values) == 0) 
		{
			throw new Exception('No fields were added to the UPDATE query');
		}
 		
		// Add values to query
		$sql .= implode(', ', $values);

		// Add where
		$sql .= ' WHERE `'.$this->primary_key.'` = \''.$params[$this->primary_key] . '\'';
		
		// Remember query
		$this->last_query = $sql;
		
		// Execute
		$result = $this->db->Query($sql);

		// Check for error 
		if ($result === false) 
		{
			throw new Exception($this->db->GetError(), 0, $sql);
		}
	} 

	// -------------------------------------------------------------------

	/**
	*	DeleteRecord
	*
	*	Remove a single record by id
	*
	*	@param	int		$id		ID value
	*	@return		void
	**/
	public function DeleteRecord($id) 
	{
		// Build SQL
		$sql = "DELETE FROM `".$this->table_name."` WHERE `".$this->primary_key."` = '" . $this->db->Escape($id) . "'";

		// Execute
		$result = $this->db->Query($sql);

		// Remember query
		$this->last_query = $sql;

		// Check for error 
		if ($result === false) 
		{
			throw new Exception($this->db->GetError(), 0, $sql);
		}
	} 

	// -------------------------------------------------------------------

	/**
	*	SaveRecord
	*
	*	Saves a record to the table, using either insert when primary
	*	is missing or empty, or update otherwise.
	*
	*	@param		array	$params		Array with fields and values, including primary key, by ref
	*	@return		void
	**/
	public function SaveRecord(&$params) 
	{
		// Check for primary key
		$has_primary_key = false;
		foreach ($params as $field => $value)
		{
			if ($field == $this->primary_key && !empty($value))
			{
				$has_primary_key = true;
				break;
			}
		}
		
		// Do insert or update
		if ($has_primary_key)
		{
			$this->UpdateRecord($params);
		}
		else
		{
			$this->InsertRecord($params);
		}
	}

	// -------------------------------------------------------------------

	/**
	*	GetLastQuery
	*
	*	Returns the last query.
	*
	*	@return		string			String of SQL query
	**/
	public function GetLastQuery() 
	{
		return $this->last_query;
	}

	// -------------------------------------------------------------------

	/**
	*	MakeSelectAndFrom 
	*	
	*	Build SELECT and FROM, making new join for each foreign column.
	*
	*	@return		string			SELECT and FROM clause for SQL query
	**/
	private function MakeSelectAndFrom() 
	{
		// Build SQL
		$sql = 'SELECT ';

		// Add custom fields (subqueries)
		if ($this->has_custom_fields)
		{
			// Find custom fields
			foreach ($this->fields as &$field)
			{
				if ($field->type == 'custom')
				{
					$sql .= '(' . $field->query . ') AS `' . $field->name . '`, ';
				}
			}
		}	

		if (!$this->has_foreign_fields) 
		{
			if (isset($this->select_fields))
			{
				// Use custom field selection
				$sql .= implode(', ', $this->select_fields) . ' ';
			}
			else
			{
				// Take all fields
				$sql .= '`'.$this->table_name.'`.* ';
			}
			
			$sql .= 'FROM `'.$this->table_name.'` ';
		} 
		else 
		{
			// Get foreign fields
			$cols = array();

			if (isset($this->select_fields))
			{
				// Use custom field selection
				foreach ($this->select_fields as $field)
				{
					$cols[] = $field;
				}
			}
			else
			{
				// Take all fields
				$cols[] = 't1.*';
			}
	
			$from_sql = 'FROM `'.$this->table_name.'` t1 ';
			$joined = array();
			$joins = array();
			$alias_no = 2;
			$tables[] = array('table' => $this->table_name, 'alias' => 't1');

			// Iterate over fields
			foreach ($this->fields as &$field)
			{
				$use_alias = false;

				if ($field->IsForeign() == true) 
				{
					// Check if foreign field should be included, if select fields were set
					if (isset($this->select_fields))
					{
						$found = false;
						foreach ($this->select_fields as $sel_field)
						{
							if (str_contains($sel_field, $field->name))
							{
								$found = true;
								break;
							}
						}
						if (!$found)
						{
							continue;
						}
					}
				
					// Find out what kind of join it should be (INNER or LEFT)
					$join_type = strtoupper($field->foreign_field->join_type);

					// Check if it's a join between two foreign tables 
					if ($field->foreign_field->join_from_table == null) 
					{
						// Join between this table and other table
					
						// Check if we need to make a new join
						$this_join = array('from_table' => $this->table_name, 'to_table' => $field->foreign_field->table, 
									'from' => $field->foreign_field->join_from, 'to' => $field->foreign_field->join_to);
						if (!$this->CheckJoinExists($this_join, $joins)) 
						{
							$from_sql .= $join_type.' JOIN `'.$field->foreign_field->table .'` AS t'.$alias_no.' ON t1.`'.$field->foreign_field->join_from.'` = t'.$alias_no.'.`'.$field->foreign_field->join_to.'` ';
							$joins[] = $this_join;
							$use_alias = true;
						} 
					} 
					else 
					{
						// Join between two other tables

						// Check if we need to make a new join
						$this_join = array('from_table' => $this->table_name, 'to_table' => $field->foreign_field->table, 
									'from' => $field->foreign_field->join_from, 'to' => $field->foreign_field->join_to);
						if (!$this->CheckJoinExists($this_join, $joins)) {

							if (($alias = $this->TableHasAlias($field->foreign_field->table, $tables)) !== false) {
								$from_sql .= $join_type.' JOIN `'.$field->foreign_field->table.'` AS t'.$alias_no.' ON '.$alias.'.`'.$field->foreign_field->join_from.'` = t'.$alias_no.'.`'.$field->foreign_field->join_to.'` ';
							} else {
								$from_sql .= $join_type.' JOIN `'.$field->foreign_field->table.'` AS t'.$alias_no.' ON `'.$field->foreign_field->join_from_table.'`.`'.$field->foreign_field->join_from.'` = t'.$alias_no.'.`'.$field->foreign_field->join_to.'` ';
							}
							$joins[] = $this_join;
							$use_alias = true;
						}
					}

					if ($use_alias) 
					{
						$cols[] = 't'.$alias_no.'.`'.$field->foreign_field->alias.'` AS `'.$field->GetName().'`';
						$tables[] = array('table' => $field->foreign_field->table, 'alias' => 't'.$alias_no);
						$alias_no++;
					} 
					else 
					{
						// Check if table has alias
						if (($alias = $this->TableHasAlias($field->foreign_field->table, $tables)) !== false) 
						{
							$cols[] = $alias.'.`'.$field->foreign_field->alias.'` AS `'.$field->GetName().'`';
						} 
						else 
						{
							$cols[] = '`'.$field->foreign_field->table.'`.`'.$field->foreign_field->alias.'` AS `'.$field->GetName().'`';
						}
					}
				}

			}

			$sql .= implode(', ', $cols).' '.$from_sql;

			$this->table_aliases = $tables;
		}
		
		//pr($sql);
		
		return $sql;
	}

	// -------------------------------------------------------------------
	
	/**
	*	MakeWhere 
	*	
	*	Build WHERE clause for SQL query based on parameters.
	*
	*	@param		mixed		$params		Array of fields and values, by ref
	*	@return		string					WHERE clause for SQL query
	**/
	private function MakeWhere(&$params) 
	{
		// Add where clause
		$sql = ' WHERE 1 = 1';
		foreach($params as $key => $val) 
		{
			// Always add slashes
			$val = addslashes((string)$val);

			// Convert foreign field alias to table alias.fieldname
			if (in_array($key, $this->foreign_fields)) 
			{
				$field = $this->GetFieldByAlias($key);
				
				// Find table alias
				if (($alias = $this->TableHasAlias($field->GetTable(), $this->table_aliases)) !== false) 
				{
					$key = $alias.'.`'.$field->GetName().'`';
				} 
				else 
				{
					// No alias for table
					$key = '`'.$field->GetTable().'`.`'.$field->GetName().'`';
				}
			} 
			else 
			{
				// Find table alias
				if (($alias = $this->TableHasAlias($this->table_name, $this->table_aliases)) !== false) 
				{
					$key = $alias.'.`'.$key.'`';
				} 
				else 
				{
					// No alias for table
					//$key = '`'.$field->GetTable().'`.`'.$field->GetName().'`';
					$key = '`'.$key.'`';
				}
			}
			
			// Process special values
			if ($val == 'NOT_NULL') 
			{
				$sql .= " AND $key IS NOT NULL";
			} 
			elseif ($val == 'NULL') 
			{
				$sql .= " AND $key IS NULL";
			} 
			elseif (preg_match('/^[<|>](?!\=).+$/i', $val) > 0) 			// Match > and <
			{
				$op = substr($val, 0, 1);
				$val = substr($val, 1);
				$sql .= " AND $key ".$op." '" . $this->db->Escape($val) . "'";
			} 
			elseif (preg_match('/^(<=|>=).+$/i', $val) > 0) 				// Match >= and <=
			{
				$op = substr($val, 0, 2);
				$val = substr($val, 2);
				$sql .= " AND $key ".$op." '" . $this->db->Escape($val) . "'";
			} 
			elseif (substr($val, 0, 5) == 'LIKE ') 					// Match LIKE
			{
				$val = substr($val, 5);
				$sql .= " AND $key LIKE '%" . $this->db->Escape($val) . "%'";
			} 
			elseif (substr($val, 0, 2) == '__') 					// Match LIKE
			{
				// Literal SQL 
				$val = substr($val, 2);
				$sql .= " AND " . $this->db->Escape($val) . " ";
			} 
			else 
			{			
				$sql .= " AND $key = '" . $this->db->Escape($val) . "'";
			}
 		}
		return $sql;
	}

	// -------------------------------------------------------------------
	
	/**
	*	TableHasAlias
	*	
	*	Check if a table name has an alias.
	*
	*	@param		string		$table			Name of table to check for
	*	@param		mixed		$tables			Array with list of tables with their aliases
	*	@return		mixed						Alias if found or false
	**/
	protected function TableHasAlias($table, &$tables) 
	{
		// If a table has more than 1 alias, the *LAST* one will be returned

		$alias = '';
		for ($i = 0; $i < count($tables); $i++) 
		{
			if ($tables[$i]['table'] == $table) 
			{
				$alias = $tables[$i]['alias'];
			}
		}
		return (strlen($alias) > 0 ? $alias : false);
	}

	// -------------------------------------------------------------------

	/**
	*	GetAllRecords
	*
	*	Returns array with all records from the table.
	*
	*	@param		string	$order_by		String for ORDER BY clause, to override default
	*	@param		int		$limit			Limit (0 = no limit)
	*	@param		int		$offset			Offset for LIMIT (default 0)
	*	@return		mixed					Array with records
	**/
	public function GetAllRecords($order_by = '', $limit = 0, $offset = 0) 
	{
		// Build SQL
		$sql = $this->MakeSelectAndFrom();

		// Use default order by if not specified
		if ($order_by == '') 
		{
			$order_by = $this->order_by . ' ';
		}
		$order_by = $this->ApplyBackticks($order_by);

		$sql .= 'ORDER BY '.$order_by . ' ';

		if ($limit > 0) 
		{
			$sql .= ' LIMIT ' . $offset . ', '.$limit;
		}

		// Get data
		$result = $this->db->GetData($sql);

		// Remember query
		$this->last_query = $sql;

		// Check for error - empty array is not error!
		if ($result === false) 
		{
			throw new Exception($this->db->GetError(), 0, $sql);
		}
		
		if ($this->return_obj)
		{
			$this->db->MakeObjects($result);
		}

		return $result;
	} 

	// -------------------------------------------------------------------

	/**
	*	GetRecords
	*
	*	Get a selection of records, based on given conditions.
	*
	*	@param		array	$params		Array with fields and values for where clause
	*	@param		string	$order_by	Order by clause
	*	@param		int		$limit		Limit (0 = no limit)
	*	@param		int		$offset		Offset for LIMIT (default 0)
	*	@return		mixed				Array with records
	**/
	public function GetRecords($params, $order_by = '', $limit = 0, $offset = 0) 
	{
		// Build SQL
		$sql = $this->MakeSelectAndFrom();
		$sql .= $this->MakeWhere($params);

		// Use default order by if not specified
		if ($order_by == '') 
		{
			$order_by = $this->order_by;
		}
		$order_by = $this->ApplyBackticks($order_by);
		$sql .= ' ORDER BY '.$order_by;

		if ($limit > 0) 
		{
			$sql .= ' LIMIT ' . $offset . ', '.$limit;
		}

		// Get data 
		$result = $this->db->GetData($sql);

		// Remember query
		$this->last_query = $sql;
		

		// Check for error 
		if ($result === false) 
		{
			throw new Exception($this->db->GetError(), 0, $sql);
		}

		if ($this->return_obj)
		{
			$this->db->MakeObjects($result);
		}

		return $result;
	} 

	// -------------------------------------------------------------------

	/**
	*	DeleteRecords
	*
	*	Remove some records based on given conditions.
	*
	*	@param		array	$params		Array with fields and values for where clause
	*	@return		void
	**/
	public function DeleteRecords($params) 
	{
		// Build SQL
		$sql = 'DELETE FROM `'.$this->table_name.'` WHERE 1 = 1';
		foreach($params as $key => $val) 
		{
			// Check field name and value
			try 
			{
				$this->CheckField($key, $val);

				// Always add slashes
				$val = addslashes($val);

				$sql .= " AND `$key` = '$val'";
			
			} 
			catch (Exception $e) 
			{
				// Nothing to do
			}
 		}

		// Execute query
		$result = $this->db->Query($sql);

		// Remember query
		$this->last_query = $sql;

		// Check for error 
		if ($result === false) 
		{
			throw new Exception($this->db->GetError(), 0, $sql);
		}
	} 

	// -------------------------------------------------------------------

	/**
	*	UpdateRecords
	*
	*	Updates one or more record in the table based on the given parameters.
	*
	*	@param		array	$params		Array with fields and values for UPDATE clause
	*	@param		array	$where		Array with fields and values for WHERE clause
	*	@return		void
	**/
	public function UpdateRecords($params, $where) 
	{
		// Build SQL
		$sql = 'UPDATE `'.$this->table_name.'` SET ';
		$values = array();
		foreach($params as $key => $val) 
		{
		  // Check field name and value
			try {
				$this->CheckField($key, $val);

				// Always escape
				$val = $this->db->Escape($val);

				// Process special values
				if ($key != $this->primary_key) {			 // Add, but not if it's the primary key
					if ($val === 'NULL') {
						$values[] = "`$key` = NULL";
					} else {
						$values[] = "`$key` = '$val'";
					}
				}

			} 
			catch (Exception $ex) 
			{
				if ($ex->GetMessage() != 'not found')
				{
					throw new Exception($ex->GetMessage());
				}
			}
 		}
 		
 		// Quit if no fields were found - update makes no sense
 		if (count($values) == 0) 
 		{
 			return;
 		}
 		
		// Cut off last comma
		$sql .= implode(', ', $values);

		// Add where
		$sql .= $this->MakeWhere($where);

		// Remember query
		$this->last_query = $sql;
		
		// Execute
		$result = $this->db->Query($sql);

		// Check for error 
		if ($result === false) 
		{
			throw new Exception($this->db->GetError(), 0, $sql);
		}
	} 

	// -------------------------------------------------------------------

	/**
	*	CheckRecord
	*
	*	Check if a certain record exists
	*
	*	@param		array	$params		Array with fields and values for where clause
	*	@return		void
	**/
	public function CheckRecord($params) 
	{
		// Build SQL
		$sql = 'SELECT COUNT(*) AS `count` FROM `'.$this->table_name.'` ';
		$sql .= $this->MakeWhere($params);

		// Get data, unwrapped
		$result = $this->db->GetData($sql, true);

		// Remember query
		$this->last_query = $sql;

		// Check for error 
		if ($result === false) 
		{
			throw new Exception($this->db->GetError(), 0, $sql);
		}

		return $result;
	}

	// -------------------------------------------------------------------
	
	/**
	*	GetFieldByAlias
	*	
	*	Returns the field that has the given alias, if found.
	*
	*	@param		string			Alias
	*	@return		mixed			Foreign field
	**/
	public function GetFieldByAlias($alias) 
	{
		reset($this->fields);
		for ($i = 0; $i < count($this->fields); $i++) 
		{
			$field = $this->fields[$i];
			if ($field->GetAlias() == $alias) 
			{
				return $field;
				break;
			}
		}
	}

	// -------------------------------------------------------------------
	
	/**
	*	ApplyBackticks
	*	
	*	Add backticks around words.
	*
	*	@param		string		$str			Input string
	*	@return		string						Output string
	**/
	protected function ApplyBackticks($str) 
	{
		// Possible input values: field - table.field - field, table.field - table.field, table.field
		
		// Split on comma
		$parts = explode(',', $str);

		// Iterate over parts
		for ($i = 0; $i < count($parts); $i++) 
		{
			$parts[$i] = trim($parts[$i]);

			// Temporarily remove ' ASC' or ' DESC' postfix
			$postfix = null;
			if (($pos = strpos($parts[$i], ' ASC')) !== false || ($pos = strpos($parts[$i], ' DESC')) !== false) 
			{
				$postfix = substr($parts[$i], $pos);
				$parts[$i] = substr($parts[$i], 0, $pos);
			}

			// Add backticks 
			if (strstr($parts[$i], '.') === false) 
			{
				$parts[$i] = '`'.$parts[$i].'`';
			} 
			else 
			{
				$parts2 = explode('.', $parts[$i]);
				if (($alias = $this->TableHasAlias($parts2[0], $this->table_aliases)) !== false) 
				{
					$parts[$i] = $alias.'.`'.$parts2[1].'`';
				} 
				else 
				{
					$parts[$i] = '`'.$parts2[0].'`.`'.$parts2[1].'`';
				}
			}
			
			// Add postfix
			if (isset($postfix)) 
			{
				$parts[$i] = $parts[$i].$postfix;
			}

		} // end loop on parts

		// Glue together
		return implode(', ', $parts);
	}

	// -------------------------------------------------------------------
	
	/**
	*	CheckJoinExists
	*	
	*	Check if a table name is already used for a join.
	*
	*	@param		mixed		$new_join		Array with foreign field information, by ref
	*	@param		mixed		$joins			Array with list of currently joined foreign fields, by ref
	*	@return		bool						True: table is already joined
	**/
	private function CheckJoinExists(&$new_join, &$joins) 
	{
		for ($i = 0; $i < count($joins); $i++) 
		{
			if ($joins[$i]['from_table'] == $new_join['from_table'] && $joins[$i]['to_table'] == $new_join['to_table'] 
				&& $joins[$i]['from'] == $new_join['from'] && $joins[$i]['to'] == $new_join['to']) 
			{
				return true;
			}
		}
		return false;
	}

	// -------------------------------------------------------------------

	/**
	*	GetListForSelect
	*
	*	Returns a key-value list of all records, using the given
	*	fields as key and value, for use in a select.
	*
	*	@param		string		$val_field		Name of field for value
	*	@param		string		$key_field		Name of field for key, default null = use primary key
	*	@param		mixed		$params			Conditions for where, default empty
	*	@return		mixed						Array with list
	**/
	public function GetListForSelect($val_field, $key_field = null, $params = array()) 
	{
		// Use primary key if key_field is empty
		if (empty($key_field))
		{
			$key_field = $this->primary_key;
		}
		
		// Check key field exists
		if (!$this->CheckFieldExists($key_field))
		{
			throw new Exception('Field "' . $key_field . '" not found in table "' . $this->table_name . '"');
		}

		// Check value field exist
		if (!$this->CheckFieldExists($val_field))
		{
			throw new Exception('Field "' . $val_field . '" not found in table "' . $this->table_name . '"');
		}
		
		// Build query to get list 
		$sql = "SELECT `$key_field`, `$val_field` FROM `" . $this->table_name . "` ";
		
		if (!empty($params))
		{
			$sql .= $this->MakeWhere($params) . " ";
		}
		
		$sql .= "ORDER BY " . $this->order_by;
		
		return $this->db->MakeList($sql);
	}

	// -------------------------------------------------------------------
	
	/**
	*	GetAffectedRows
	*	
	*	Returns the number of rows affected by the last query.
	*
	*	@return		int			Number of affected rows
	**/
	public function GetAffectedRows() 
	{
		return $this->db->GetAffectedRows();
	}

	// -------------------------------------------------------------------
	
	/**
	*	GetLastId
	*	
	*	Returns the last auto-increment ID.
	*
	*	@return		int			Last ID value
	**/
	public function GetLastId() 
	{
		return $this->db->GetInsertId();
	}

	// -------------------------------------------------------------------
	
	/**
	*	Query
	*	
	*	Perform a raw SQL query (for INSERT/UPDATE).
	*
	*	@param		string	$sql		Query		
	*	@return		void
	**/
	public function Query($sql) 
	{
		$this->db->Query($sql);
	}

	// -------------------------------------------------------------------
	
	/**
	*	SetTableName
	*	
	*	Sets the table name.
	*
	*	@param		string		$table			Table name
	*	@return		void
	**/
	public function SetTableName($name) 
	{
		$this->table_name = $name;
	}

	// -------------------------------------------------------------------
	
	/**
	*	SetPrimaryKey
	*	
	*	Sets the primary key.
	*
	*	@param		string		$key		Key name
	*	@return		void
	**/
	public function SetPrimaryKey($key) 
	{
		$this->primary_key = $key;
	}

	// -------------------------------------------------------------------
	
	/**
	*	SetOrderBy
	*	
	*	Sets the default order by clause.
	*
	*	@param		string		$str		Order by clause
	*	@return		void
	**/
	public function SetOrderBy($str) 
	{
		$this->order_by = $str;
	}

	// -------------------------------------------------------------------
	
	/**
	*	GetPrimaryKey
	*	
	*	Returns the primary key name.
	*
	*	@return		string		Primary key
	**/
	public function GetPrimaryKey() 
	{
		return $this->primary_key;
	}

	// -------------------------------------------------------------------
	
	/**
	*	CheckFieldExists
	*	
	*	Checks if there is a field with the given name and returns
	*	true if so, false if not.
	*
	*	@param		string	$name	Name to search for
	*	@return		bool			True: field exists
	**/
	private function CheckFieldExists($name) 
	{
		foreach ($this->fields as &$field)
		{
			if ($field->GetName() == $name)
			{
				return true;
			}
		}
		return false;
	}

	// -------------------------------------------------------------------
	
	/**
	*	GetForeignFields
	*	
	*	Returns the list of foreign fields.
	*
	*	@return		mixed			Array with foreign fields
	**/
	public function GetForeignFields() 
	{
		return $this->foreign_fields;
	}

	// -------------------------------------------------------------------
	
	/**
	*	SetPkNotAutoInc
	*	
	*	Sets the 'pk_auto_incr' property to false, which means that the 
	*	primary key must be included in INSERT queries.
	*
	*	@return		void
	**/
	public function SetPkNotAutoInc() 
	{
		$this->pk_is_auto_incr = false;
	}

	// -------------------------------------------------------------------
	
	/**
	*	HasAutoIncrPk
	*	
	*	Returns the value of the pk_is_auto_incr property.
	*
	*	@return		void
	**/
	public function HasAutoIncrPk() 
	{
		return $this->pk_is_auto_incr;
	}

	// -------------------------------------------------------------------
	
	/**
	*	ReturnArrays
	*	
	*	Sets return_obj to false.
	*
	*	@return		void
	**/
	public function ReturnArrays() 
	{
		return $this->return_obj = false;
	}

	// -------------------------------------------------------------------
	
	/**
	*	GetEmpty
	*	
	*	Returns an object with all table fields, with empty values.
	*
	*	@return		void
	**/
	public function GetEmpty() 
	{
		$obj = new StdClass;
		
		// Iterate over fields
		foreach ($this->fields as &$field)
		{
			// Add TableField objects
			if (!$field->IsForeign()) 
			{
				$name = $field->name;
				$obj->$name = '';
			}
		}
		return $obj;
	}

	// -------------------------------------------------------------------

	/**
	*	Clear
	*
	*	Clear (TRUNCATE) the entire table.
	*
	*	@return		void
	**/
	public function Clear() 
	{
		$this->db->Query("TRUNCATE TABLE `" . $this->table_name . "`");
	}
}

?>