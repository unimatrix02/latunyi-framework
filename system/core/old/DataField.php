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
*	The DataField class is a simple container class for 
*	holding a field definition.
*
*	@author      	Raymond van Velzen <raymond@latunyi.com>
*	@copyright		Latunyi
*	@package     	Meridium
**/
class DataField
{
    /**
    *	$name
    * 
	*	Name of the field
	*
    *	@var     string
    **/
	public $name;

    /**
    *	$type
    * 
	*	Type of the field. Valid types are 'string', 'date', 'datetime', 'int'
	*
    *	@var     string
    **/
	public $type;

    /**
    *	$length
    * 
	*	Length of of the field. Only used for strings.
	*
    *	@var     string
    **/
	public $length;

    /**
    *	$validate
    * 
	*	Validate or not, default true. Ignored for foreign fields
	*
    *	@var     bool
    **/
	public $validate = true;

    /**
    *	$required
    * 
	*	Field is required or not, default true
	*
    *	@var     bool
    **/
	public $required = true;

    /**
    *	$check
    * 
	*	Which check to perform in Validator, default IsNotEmpty
	*
    *	@var     bool
    **/
	public $check = 'IsNotEmpty';

    /**
    *	$message
    * 
	*	Message to return if validation failed. 
	*
    *	@var     string
    **/
	public $message = '';

    /**
    *	$primary_key
    * 
	*	Primary key or not, default false
	*
    *	@var     bool
    **/
	public $primary_key = false;

    /**
    *	$foreign_field
    * 
	*	ForeignField object, if foreign
	*
    *	@var     object
    **/
	public $foreign_field;

    /**
    *	$query
    * 
	*	Query for custom fields
	*
    *	@var     string
    **/
	public $query;

	// -------------------------------------------------------------------

	/**
	*	Constructor
	*
	*	Initializes the object.
	*
	*	@param		string		$name		Name of field
	*	@param		bool		$type		Type of field
	*	@return		void
	**/
	public function __construct($name, $type) 
	{
		$this->name = $name;
		$this->type = $type;

		// Set primary key if 'id'
		if ($name == 'id')
		{
			$this->SetPk();
		}

		// Set check to IsNumber if int
		if ($type == 'int')
		{
			$this->check = 'IsNumber';
		}
	}

	// -------------------------------------------------------------------

	/**
	*	SetPk
	*
	*	Sets primary key to true and sets validate to false.
	*
	*	@return		void
	**/
	public function SetPk() 
	{
		$this->primary_key = true;
		$this->validate = false;
	}

	// -------------------------------------------------------------------

	/**
	*	IsPrimaryKey
	*
	*	Returns value of primary_key field.
	*
	*	@return		void
	**/
	public function IsPrimaryKey() 
	{
		return $this->primary_key;
	}

	// -------------------------------------------------------------------

	/**
	*	IsForeign
	*
	*	Returns true if foreign_field is not empty.
	*
	*	@return		void
	**/
	public function IsForeign() 
	{
		return !empty($this->foreign_field);
	}

	// -------------------------------------------------------------------

	/**
	*	__call
	*
	*	Magic method to retrieve properties
	*
	*	@param	string		$method		Name of method
	*	@param	mixed		$args		Arguments
	*	@return	mixed					Value of property					
	**/
	public function __call($method, $args) {

		// Check for set/get and property name
		$prefix = strtolower(substr($method, 0, 3));
        $property = strtolower(substr($method, 3));

		// No prefix or property: exit
        if (empty($prefix) || empty($property)) 
        {
            return;
        }

		// Return property value, if present
		if ($prefix == "get" && isset($this->$property)) 
		{
            return $this->$property;
        }
	}

}

?>