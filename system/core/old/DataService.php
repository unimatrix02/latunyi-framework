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
*	The DataService class is a base class that provides methods to
*	perform common operations on table classes.
*
*	@author      	Raymond van Velzen <raymond@latunyi.com>
*	@copyright		Latunyi
*	@package     	Meridium
*
**/
class DataService
{
    /**
    *	$table
    * 
	*	Table object
	*
    *	@var     object
    **/
	public $table;

    /**
    *	$fields
    * 
	*	Array with DataField objects
	*
    *	@var     mixed
    **/
	public $fields;

	// -------------------------------------------------------------------

	/**
	*	Constructor
	*
	*	Creates a new Table object using the supplied fields and data.
	*
	*	@param		mixed		$fields			Array with field definitions, by ref
	*	@param		string		$table_name		Name of table to use
	*	@param		string		$order_by		String for ORDER BY clause
	*	@return		void
	**/
	public function __construct(&$fields, $table_name, $order_by) 
	{
		// Keep fields (used for validation)
		$this->fields = $fields;
	
		// Set up table object
		$this->table = new Table($fields);

		// Set table properties
		$this->table->SetTableName($table_name);
		$this->table->SetOrderBy($order_by);
	}

	// -------------------------------------------------------------------

	/**
	*	Get
	*
	*	Returns data of a single record.
	*
	*	@return		void
	**/
	public function Get($id) 
	{
		return $this->table->GetRecord($id);	
	}

	// -------------------------------------------------------------------

	/**
	*	Find
	*
	*	Returns a set of records.
	*
	*	@param		mixed	$params		Search conditions for WHERE
	*	@param		bool	$unwrap		Return unnested array if count = 1, default false
	*	@return		void
	**/
	public function Find($params, $unwrap = false) 
	{
		$result = $this->table->GetRecords($params);	
		if ($unwrap && count($result) == 1)
		{
			return $result[0];
		} 
		return $result;
	}

	// -------------------------------------------------------------------

	/**
	*	GetAll
	*
	*	Returns an array with all records.
	*
	*	@return		void
	**/
	public function GetAll() 
	{
		return $this->table->GetAllRecords();	
	}

	// -------------------------------------------------------------------

	/**
	*	Save
	*
	*	Saves a record to the table, using either insert (if primary key
	*	is empty) or update (if primary key is not empty). 
	*
	*	@param		mixed		$data				Array with data to save
	*	@param		bool		$force_insert		Force insert, default false
	*	@return		void
	**/
	public function Save($data, $force_insert = false) 
	{
		// Check for non-autoinc PK
		if ($this->table->HasAutoIncrPk())
		{
			if (empty($data[$this->table->GetPrimaryKey()]) || $force_insert)
			{
				$action = 'Insert';
			}
			else
			{
				$action = 'Update';
			}			
		}
		else
		{
			// Check for _mode key in data to indicate insert/update
			if (!isset($data['_mode']))
			{
				throw new Exception('Missing _mode element to indicate desired action (insert/update)');
			}
			
			if ($data['_mode'] != 'add' && $data['_mode'] != 'edit')
			{
				throw new Exception('Invalid _mode element, must be "add" or "edit"');
			} 
			
			$action = ($data['_mode'] == 'add' ? 'Insert' : 'Update');
		}
		
		// Perform action	
		$method = $action . 'Record';
		$this->table->$method($data);
	}

	// -------------------------------------------------------------------

	/**
	*	Remove
	*
	*	Removes a record from the table.
	*
	*	@return		void
	**/
	public function Remove($id) 
	{
		return $this->table->DeleteRecord($id);	
	}

	// -------------------------------------------------------------------

	/**
	*	Validate
	*
	*	Hands of validation of the given data to the Validator class,
	*	providing it with the available field list as well. Returns a
	*	key/value list of failed fields with [field] => [error msg]
	*	(for usage with forms) or throws a single exception (for internal
	*	usage).
	*
	*	@param		mixed		$data		Array with data to validate
	*	@param		bool		$return_array	True: Return array with error messages. False: Throw exception. Default true.		
	*	@return		mixed					Array or void
	**/
	public function Validate($data, $return_array = true) 
	{
		// Check input
		if (!is_array($data) || empty($data))
		{
			throw new Exception('Cannot validate empty data set');
		}
	
		$result = Validator::Test($this->fields, $data);
		
		// Return array
		if ($return_array)
		{
			return $result;
		}
		
		// Throw exception if necessary
		if (count($result) > 0)
		{
			throw new Exception('The field(s) ' . implode(', ', array_keys($result)) . ' failed validation');
		}
	}

}

?>