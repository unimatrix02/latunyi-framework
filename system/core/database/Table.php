<?php
namespace System\Core\Database;

/**
 * Base class for operations on database tables.
 */
class Table
{
	/**
	 * Database object
	 * @var \System\Core\Database
	 */
	private $db;
	
	/**
	 * Name of the database table
	 * @var string
	 */
	private $name;
	
	/**
	 * Name of the primary key field
	 * @var string
	 */
	private $primaryKey;
	
	/**
	 * Primary key is an auto increment field or not
	 * @var bool
	 */
	private $primaryKeyIsAutoIncrement;

	/**
	 * Default order by
	 * @var string
	 */
	private $orderBy;
	
	/**
	 * List of TableField, ForeignField and/or CustomField objects
	 * @var array
	 */
	private $fields;
	
	/**
	 * Fields to use in select
	 * @var array
	 */
	private $selectFields;
	
	/**
	 * Map of table aliases.
	 * @var array
	 */
	private $tableAliases;
	
	/**
	 * Class for storing data in.
	 * @var string
	 */
	private $entityClass;
	
	/**
	 * Constructor, sets the db and entity class, inits fields/foreign fields.
	 * 
	 * @param Database $db
	 * @param string $entityClass
	 */
	public function __construct(Database $db, $entityClass = '')
	{
		$this->db = $db;
		$this->entityClass = $entityClass;
		
		$this->fields = array();
		$this->tableAliases = array();
	}
	
	/**
	 * Adds a TableField object to the list of fields.
	 * 
	 * @param TableField $field
	 */
	public function addField(DataField $field)
	{
		$this->fields[] = $field;
		if ($field instanceOf TableField && $field->isPrimaryKey)
		{
			$this->primaryKey = $field->name;
		}
	}
	
	/**
	 * Finds and returns a row by primary key.
	 * 
	 * @param mixed $id
	 */
	public function getRow($id)
	{
		$sql = $this->makeSelectAndFrom();

		if (($alias = $this->tableHasAlias($this->name, $this->tableAliases)) !== false)
		{
			$sql .= 'WHERE '.$alias.'.`'.$this->primaryKey.'` = :id';
		}
		else
		{
			$sql .= 'WHERE `'.$this->name.'`.`'.$this->primaryKey.'` = :id';
		}
		
		$params = array('id' => $id);
		
		$row = $this->db->getRow($sql, $params, $this->entityClass);
		
		return $row;
	}

	/**
	 *	Returns all rows from the table.
	 *
	 *	@param		string	$orderBy		String for ORDER BY clause, to override default
	 *	@param		int		$limit			Limit (0 = no limit)
	 *	@param		int		$offset			Offset for LIMIT (default 0)
	 *	@return		mixed					Array with records
	 **/
	public function getAllRows($orderBy = '', $limit = 0, $offset = 0)
	{
		// Build SQL
		$sql = $this->makeSelectAndFrom();
	
		// Use default order by if not specified
		if (empty($orderBy))
		{
			$orderBy = $this->orderBy . ' ';
		}
		$orderBy = $this->applyBackticks($orderBy);
	
		$sql .= 'ORDER BY '.$orderBy . ' ';
	
		if ($limit > 0)
		{
			$sql .= ' LIMIT ' . $offset . ', '.$limit;
		}
		
		// Get data
		$result = $this->db->getData($sql, array(), $this->entityClass);
	
		return $result;
	}

	/**
	 *	Get a selection of records, based on given conditions.
	 *
	 *	@param		QueryParams			params	Parameter list object
	 *	@param		string				params	Order by clause
	 *	@param		int					paramsLimit (0 = no limit)
	 *	@param		int					params	Offset for LIMIT (default 0)
	 *	@return		mixed							Array with objects
	 **/
	public function getRows(QueryParams $params, $orderBy = '', $limit = 0, $offset = 0)
	{
		// Build SQL
		$sql = $this->makeSelectAndFrom();
		$sql .= $this->makeWhere($params);
	
		// Use default order by if not specified
		if ($orderBy == '')
		{
			$orderBy = $this->orderBy;
		}
		$orderBy = $this->applyBackticks($orderBy);
		$sql .= ' ORDER BY '.$orderBy;
	
		if ($limit > 0)
		{
			$sql .= ' LIMIT ' . $offset . ', '.$limit;
		}
		
		// Get data
		$result = $this->db->getData($sql, $params->asArray(), $this->entityClass);
	
		return $result;
	}
	
	/**
	 *	Add a record to the table.
	 *
	 *	@param		array	$params		Array with fields and values, by ref
	 *	@param		bool	$replace	Use REPLACE INTO instead of INSERT. Default false.
	 *	@return		void
	 **/
	public function insertRow($data, $replace = false)
	{
		$data = (array)$data;
		
		// Remove primary key from data, if auto increment
		if ($this->primaryKeyIsAutoIncrement && array_key_exists($this->primaryKey, $data))
		{
			unset($data[$this->primaryKey]);
		}
		
		$data = $this->cleanupFieldNames($data);
		
		// Build SQL
		$sql = ($replace ? 'REPLACE ' : 'INSERT ');
		$sql .= 'INTO `'.$this->name.'` SET ';
		$values = array();
		foreach($data as $key => $val)
		{
			// Ignore if field is not in table
			if (!$this->hasField($key))
			{
				continue;
			}

			// Process special values
			if ($val === 'NULL')
			{
				$values[] = "`$key` = NULL";
			}
			elseif (substr($val, 0, 2) == '__')
			{
				// Literal SQL
				$val = substr($val, 2);
				$values[] .= " `$key` = :$key";
			}
			else
			{
				$values[] = "`$key` = :$key";
			}
		}
	
		// Add values to query
		$sql .= implode(', ', $values);
	
		// No values: exception
		if (count($values) == 0)
		{
			throw new Exception('No fields were added to the INSERT query');
		}
	
		// Execute query
		$result = $this->db->runQuery($sql, $data);
		
		if ($this->primaryKeyIsAutoIncrement)
		{
			return $this->db->getLastInsertedId();
		}
	}

	/**
	 *	Update a row in the table.
	 *
	 *	@param		array	$data		Array with fields and values, including primary key
	 *	@return		void
	 **/
	public function updateRow($data)
	{
		$data = (array)$data;
		$data = $this->cleanupFieldNames($data);
		
		// Build SQL
		$sql = 'UPDATE `'.$this->name.'` SET ';
		$values = array();
		$pkFound = false;
		foreach($data as $key => $val)
		{
			// Ignore if field is not in table
			if (!$this->hasField($key))
			{
				unset($data[$key]);
				continue;
			}

			// Include value, but not if primary key and table has auto-incr PK
			$include = true;
			if ($key == $this->primaryKey)
			{
				if ($this->primaryKeyIsAutoIncrement)
				{
					$include = false;
				}

				// Found non-empty primary key
				if (!empty($val))
				{
					$pkFound = true;
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
					$values[] = "`$key` = :$key";
				}
			}
		}
			
		// Quit if has no primary key
		if (!$pkFound)
		{
			throw new \Exception('Cannot update: The supplied data does not contain a primary key');
		}
	
		// No fields were found - update makes no sense
		if (count($values) == 0)
		{
			throw new Exception('No fields were added to the UPDATE query');
		}
			
		// Add values to query
		$sql .= implode(', ', $values);
	
		// Add where
		$sql .= ' WHERE `'.$this->primaryKey.'` = :' . $this->primaryKey;

		// Execute
		$result = $this->db->runQuery($sql, $data);
		
		return $this->db->getAffectedRows();
	}
	
	/**
	 *	Updates one or more record in the table based on the given parameters.
	 *
	 *	@param		array|object		$data		Array or object with fields and values for UPDATE clause
	 *	@param		QueryParams			$where		Array with fields and values for WHERE clause
	 *	@return		int					Number of affected rows
	 **/
	public function updateRows($data, QueryParams $where)
	{
		$data = (array)$data;
		$data = $this->cleanupFieldNames($data);
		
		// Build SQL
		$sql = 'UPDATE `'.$this->name.'` SET ';
		$values = array();
		foreach($data as $key => $val)
		{
			// Ignore if field is not in table
			if (!$this->hasField($key))
			{
				unset($data[$key]);
				continue;
			}
				
			// Process special values
			if ($key != $this->primaryKey)	// Add, but not if it's the primary key 
			{			 
				if ($val === 'NULL') 
				{
					$values[] = "`$key` = NULL";
				} 
				else 
				{
					$values[] = "`$key` = :$key";
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
		
		// Merge data and where conditions for query params
		$data = array_merge($data, $where->asArray());
	
		// Execute
		$result = $this->db->runQuery($sql, $data);
		
		return $this->db->getAffectedRows();
	}	

	/**
	 *	Remove a single row by id
	 *
	 *	@param		mixed	$id		ID value
	 *	@return		int				Number of affected rows
	 **/
	public function deleteRow($id)
	{
		// Build SQL
		$sql = "DELETE FROM `".$this->name."` WHERE `".$this->primaryKey."` = :id";
		
		$params = array('id' => $id);
	
		// Execute
		$result = $this->db->runQuery($sql, $params);
		
		return $this->db->getAffectedRows();
	}

	/**
	 *	Remove some rows based on given conditions.
	 *
	 *	@param		QueryParams		$params		Object with fields and values for where clause
	 *	@return		int							Number of affected rows
	 **/
	public function deleteRows(QueryParams $params)
	{
		$sql = 'DELETE FROM `'.$this->name.'`';
		$sql .= $this->makeWhere($params);
		$result = $this->db->runQuery($sql, $params->asArray());
		return $this->db->getAffectedRows();
	}
	
	/**
	 *	Counts the number of rows matching the given conditions.
	 *
	 *	@param		QueryParams	$params		Object with fields and values for where clause
	 *	@return		void
	 **/
	public function countRows(QueryParams $params)
	{
		// Build SQL
		$sql = 'SELECT COUNT(*) AS `count` FROM `'.$this->name.'` ';
		$sql .= $this->MakeWhere($params);
	
		// Get data, unwrapped
		$result = $this->db->getValue($sql, $params->asArray());
	
		return $result;
	}
	
	/**
	 *	Returns the last auto-increment ID.
	 *
	 *	@return		int			Last ID value
	 **/
	public function getLastInsertedId()
	{
		return $this->db->getLastInsertedId();
	}
	
	/**
	 *	Returns a key-value list of all records, using the given
	 *	fields as key and value, for use in a select.
	 *
	 *	@param		string		$valueField		Name of field for value
	 *	@param		string		$Keyfield		Name of field for key, default null = use primary key
	 *	@param		QueryParams	$params			Conditions for where, default empty
	 *	@return		mixed						Array with list
	 **/
	public function getSimpleList($valueField, $keyField = null, QueryParams $params = null)
	{
		// Use primary key if key_field is empty
		if (empty($keyField))
		{
			$keyField = $this->primaryKey;
		}
	
		// Check key field exists
		if (!$this->hasField($keyField))
		{
			throw new Exception('Field "' . $keyField . '" not found in table "' . $this->name . '"');
		}
	
		// Check value field exist
		if (!$this->hasField($valueField))
		{
			throw new Exception('Field "' . $valueField . '" not found in table "' . $this->name . '"');
		}
	
		// Build query to get list
		$sql = "SELECT `$keyField`, `$valueField` FROM `" . $this->name . "` ";
	
		if (isset($params))
		{
			$sql .= $this->MakeWhere($params) . " ";
		}
	
		$sql .= "ORDER BY " . $this->orderBy;
	
		return $this->db->makeList($sql, $params);
	}
	
	/**
	 * Build SELECT and FROM, making new join for each foreign column.
	 *
	 * @return	string SELECT and FROM clause for SQL query
	 */
	private function makeSelectAndFrom()
	{
		// Build SQL
		$sql = 'SELECT ';
	
		// Add custom fields (subqueries)
		if ($this->hasCustomFields())
		{
			// Find custom fields
			foreach ($this->fields as $field)
			{
				if ($field instanceOf CustomField)
				{
					$sql .= '(' . $field->query . ') AS `' . $field->name . '`, ';
				}
			}
		}
	
		if (!$this->hasForeignFields())
		{
			if (isset($this->selectFields))
			{
				// Use custom field selection
				$sql .= implode(', ', $this->selectFields) . ' ';
			}
			else
			{
				// Take all fields
				$sql .= '`'.$this->name.'`.* ';
			}
				
			$sql .= 'FROM `'.$this->name.'` ';
		}
		else
		{
			// Get foreign fields
			$cols = array();
	
			if (isset($this->selectFields))
			{
				// Use custom field selection
				foreach ($this->selectFields as $field)
				{
					$cols[] = $field;
				}
			}
			else
			{
				// Take all fields
				$cols[] = 't1.*';
			}
	
			$from_sql = 'FROM `'.$this->name.'` t1 ';
			$joined = array();
			$joins = array();
			$alias_no = 2;
			$tables[] = array('table' => $this->name, 'alias' => 't1');
	
			// Iterate over fields
			foreach ($this->fields as &$field)
			{
				$use_alias = false;
	
				if ($field instanceof ForeignField)
				{
					// Check if foreign field should be included, if select fields were set
					if (isset($this->selectFields))
					{
						$found = false;
						foreach ($this->selectFields as $sel_field)
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
					$join_type = strtoupper($field->joinType);
	
					// Check if it's a join between two foreign tables
					if ($field->joinFromTable == null)
					{
						// Join between this table and other table
							
						// Check if we need to make a new join
						$this_join = array(
							'from_table' => $this->name, 
							'to_table'   => $field->table,
							'from'       => $field->joinFrom, 
							'to'         => $field->joinTo
						);
						if (!$this->CheckJoinExists($this_join, $joins))
						{
							$from_sql .= $join_type.' JOIN `'.$field->table .'` t'.$alias_no.' ON t1.`'.$field->joinFrom.'` = t'.$alias_no.'.`'.$field->joinTo.'` ';
							$joins[] = $this_join;
							$use_alias = true;
						}
					}
					else
					{
						// Join between two other tables
	
						// Check if we need to make a new join
						$this_join = array(
							'from_table' 	=> $this->table_name, 
							'to_table' 		=> $field->foreign_field->table,
							'from' 			=> $field->foreign_field->join_from, 
							'to' 			=> $field->foreign_field->join_to
						);
						if (!$this->checkJoinExists($this_join, $joins)) 
						{
							if (($alias = $this->tableHasAlias($field->table, $tables)) !== false) 
							{
								$from_sql .= $join_type.' JOIN `'.$field->table.'` t'.$alias_no.' ON '.$alias.'.`'.$field->joinFrom.'` = t'.$alias_no.'.`'.$field->joinTo.'` ';
							} 
							else 
							{
								$from_sql .= $join_type.' JOIN `'.$field->table.'` t'.$alias_no.' ON `'.$field->joinFromTable.'`.`'.$field->joinFrom.'` = t'.$alias_no.'.`'.$field->joinTo.'` ';
							}
							$joins[] = $this_join;
							$use_alias = true;
						}
					}
	
					if ($use_alias)
					{
						$cols[] = 't'.$alias_no.'.`'.$field->foreignFieldName .'` AS `'.$field->name.'`';
						$tables[] = array('table' => $field->table, 'alias' => 't'.$alias_no);
						$alias_no++;
					}
					else
					{
						// Check if table has alias
						if (($alias = $this->tableHasAlias($field->table, $tables)) !== false)
						{
							$cols[] = $alias.'.`'.$field->foreignFieldName.'` AS `'.$field->name .'`';
						}
						else
						{
							$cols[] = '`'.$field->table.'`.`'.$field->foreignFieldName.'` AS `'.$field->name.'`';
						}
					}
				}
	
			}
			
			$this->tableAliases = $tables;
	
			$sql .= implode(', ', $cols).' '.$from_sql;
	
			$this->table_aliases = $tables;
		}
	
		return $sql;
	}

	/**
	 *	Build WHERE clause for SQL query based on parameters.
	 *
	 *	@param		QueryConditionList		$params		QueryConditionList objects
	 *	@return		string								WHERE clause for SQL query
	 **/
	private function makeWhere(QueryParams $params)
	{
		// Add where clause
		$sql = ' WHERE 1 = 1';
		foreach($params as $param)
		{
			$key = $param->fieldName;
			$val = $param->fieldValue;
			
			// Always add slashes
			if (!is_array($val))
			{
				$val = addslashes((string)$val);
			}
			
			$shortKey = $key;
	
			// Convert foreign field alias to table alias.fieldname
			if ($this->isForeignField($key))
			{
				$field = $this->getFieldByAlias($key);
	
				// Find table alias
				if (($alias = $this->tableHasAlias($field->table, $this->tableAliases)) !== false)
				{
					$key = $alias.'.`'.$field->foreignFieldName.'`';
				}
				else
				{
					// No alias for table
					$key = '`'.$field->table.'`.`'.$field->name.'`';
				}
			}
			else
			{
				// Find table alias
				if (($alias = $this->tableHasAlias($this->name, $this->tableAliases)) !== false)
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
			
			switch ($param->operator)
			{
				case 'NOT NULL':
					$sql .= " AND $key IS NOT NULL";
					break;
				case 'NULL':
					$sql .= " AND $key IS NULL";
					break;
				case 'IN':
					$sql .= " AND $key IN (" . implode(',', $param->fieldValue) . ")";
					break;
				case '=':
				case '>':
				case '<':
				case '>=':
				case '<=':
				case '!=':
				case 'LIKE':
					$sql .= " AND $key " . $param->operator ." :" . $shortKey . " ";
					break;
			}
		}
		return $sql;
	}

	/**
	 *	Clear (TRUNCATE) the entire table.
	 *
	 *	@return		void
	 **/
	public function clear()
	{
		$this->db->runQuery("TRUNCATE TABLE `" . $this->name . "`");
	}

	/**
	 *	Returns the number of rows affected by the last query.
	 *
	 *	@return		int			Number of affected rows
	 **/
	public function getAffectedRows()
	{
		return $this->db->getAffectedRows();
	}
	
	/**
	 *	Returns the field that has the given alias, if found.
	 *
	 *	@param		string			Alias
	 *	@return		mixed			Foreign field
	 **/
	private function getFieldByAlias($alias)
	{
		foreach ($this->fields as $field)
		{
			if ($field instanceof ForeignField && $field->name == $alias)
			{
				return $field;
			}
		}
	}
	
	/**
	 *	Sets the fields that should be used for the SELECT clause.
	 *
	 *	@param		string	$field		Field name (unlimited number)
	 *	@return		void
	 **/
	public function setSelectFields()
	{
		$fields = func_get_args();
		if (empty($fields))
		{
			throw new \Exception('Select fields list is not an array or empty.');
		}
	
		// Check if fields exist in fields collection
		foreach($fields as &$item)
		{
			$found = false;
			foreach ($this->fields as &$field)
			{
				if ($item == $field->name)
				{
					$found = true;
					break;
				}
			}
				
			if (!$found)
			{
				throw new \Exception('There is no field "' . $item . '" in table ' . $this->name . '.');
			}
	
			// Add to list
			if ($this->hasForeignFields())
			{
				$item = 't1.`' . $item . '`';
			}
			else
			{
				$item = '`' . $this->name . '`.`' . $item . '`';
			}
		}
	
		$this->selectFields = $fields;
	}

	/**
	 *	Check if a table name is already used for a join.
	 *
	 *	@param		mixed		$new_join		Array with foreign field information, by ref
	 *	@param		mixed		$joins			Array with list of currently joined foreign fields, by ref
	 *	@return		bool						True: table is already joined
	 **/
	private function checkJoinExists(&$new_join, &$joins)
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

	/**
	 *	Check if a table name has an alias.
	 *
	 *	@param		string		$table			Name of table to check for
	 *	@param		mixed		$tables			Array with list of tables with their aliases
	 *	@return		mixed						Alias if found or false
	 **/
	protected function tableHasAlias($table, &$tables)
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
	
	/**
	 *	Add backticks around words.
	 *
	 *	@param		string		$str			Input string
	 *	@return		string						Output string
	 **/
	protected function applyBackticks($str)
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
	
	/**
	 * Returns true if the list of fields contains a ForeignField.
	 * 
	 * @return bool
	 */
	private function hasForeignFields()
	{
		foreach ($this->fields as $field)
		{
			if ($field instanceOf ForeignField)
			{
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Returns true if the list of fields contains a CustomField.
	 * 
	 * @return bool
	 */
	private function hasCustomFields()
	{
		foreach ($this->fields as $field)
		{
			if ($field instanceOf CustomField)
			{
				return true;
			}
		}
		return false;
	}

	/**
	 * Returns true if given field name is a ForeignField.
	 * 
	 * @param string $fieldName
	 * @return boolean
	 */
	private function isForeignField($fieldName)
	{
		foreach ($this->fields as $field)
		{
			if ($field->name == $fieldName && $field instanceOf ForeignField)
			{
				return true;
			}
		}
		return false;
	}

	/**
	 *	Checks if there is a field with the given name and returns
	 *	true if so, false if not. Only checks TableFields.
	 *
	 *	@param		string	$name	Name to search for
	 *	@return		bool			True: field exists
	 **/
	private function hasField($name)
	{
		foreach ($this->fields as $field)
		{
			if ($field instanceOf TableField && $field->name == $name)
			{
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Returns the name
	 * 
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Returns the primary key name
	 * 
	 * @return string
	 */
	public function getPrimaryKey()
	{
		return $this->primaryKey;
	}

	/**
	 * Sets the primary key name
	 * 
	 * @param string $primaryKey
	 */
	public function setPrimaryKey($primaryKey)
	{
		$this->primaryKey = $primaryKey;
	}

	/**
	 * Returns the order by.
	 * 
	 * @return string
	 */
	public function getOrderBy()
	{
		return $this->orderBy;
	}

	/**
	 * Sets the name.
	 * 
	 * @param string $name
	 */
	public function setName($name)
	{
		$this->name = $name;
	}

	/**
	 * Sets the order by.
	 * 
	 * @param string $orderBy
	 */
	public function setOrderBy($orderBy)
	{
		$this->orderBy = $orderBy;
	}
	
	/**
	 * Sets the PK is auto incr. field.
	 * 
	 * @param bool $value
	 */
	public function setPrimaryKeyIsAutoIncrement($value)
	{
		$this->primaryKeyIsAutoIncrement = $value;
	}
	
	/**
	 * Takes a key-value array and converts field names to lower case.
	 * 
	 * @param array $data
	 * @return array
	 */
	private function cleanupFieldNames($data)
	{
		foreach ($data as $key => $value)
		{
			// Translate field name to lower case
			$newKey = $this->makeLowerCaseName($key);
			if ($newKey != $key)
			{
				$data[$newKey] = $value;
				unset($data[$key]);
			}
		}
		return $data;
	}
	
	/**
	 * Converts a camelCase name to a lower_case name.
	 * 
	 * @param string $name
	 * @return string
	 */
	public function makeLowerCaseName($name)
	{
		$pos = array();
		for ($i = 0; $i < strlen($name); $i++)
		{
			if ($i > 0 && ctype_upper($name[$i]))
			{
				$pos[] = $i; 
			}
		}
		foreach ($pos as $index => $p)
		{
			$p += $index;
			$name = substr($name, 0, $p) . '_' . substr($name, $p);
		}
		return strtolower($name);
	}

	/**
	 * Converts a lower_case name to a camelCase name.
	 * 
	 * @param string $name
	 * @return string
	 */
	public function makeCamelCaseName($name)
	{
		// Make new camelCase name without underscore
		for ($i = 0; $i < strlen($name); $i++)
		{
			if ($i > 0 && $name[$i - 1] == '_')
			{
				$name[$i] = strtoupper($name[$i]);
			}
		}
		$name = str_replace('_', '', $name);
		return $name;
	}
}