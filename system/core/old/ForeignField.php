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
*	The ForeignField class is a simple container class for 
*	holding a field definition for a foreign field.
*
*	@author      	Raymond van Velzen <raymond@latunyi.com>
*	@copyright		Latunyi
*	@package     	Meridium
**/
class ForeignField
{
    /**
    *	$alias
    * 
	*	Alias for this field
	*
    *	@var     string
    **/
	public $alias;

    /**
    *	$table
    * 
	*	Foreign table name
	*
    *	@var     string
    **/
	public $table;

    /**
    *	$join_from
    * 
	*	Field to join from to foreign table
	*
    *	@var     string
    **/
	public $join_from;

    /**
    *	$join_to
    * 
	*	Field to join to (primary key in foreign table), default 'id'
	*
    *	@var     string
    **/
	public $join_to = 'id';
	
    /**
    *	$join_from_table
    * 
	*	Join from table (second-level table for joins)
	*
    *	@var     string
    **/
	public $join_from_table;

    /**
    *	$join_type
    * 
	*	Join type (INNER, LEFT), default left
	*
    *	@var     string
    **/
	public $join_type = 'inner';

	// -------------------------------------------------------------------

	/**
	*	Constructor
	*
	*	Initializes the object.
	*
	*	@param		string		$alias		Alias of field
	*	@param		bool		$table		Table to join with
	*	@return		void
	**/
	public function __construct($alias, $table) 
	{
		$this->alias = $alias;
		$this->table = $table;
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
		if ($prefix == 'get' && isset($this->$property)) 
		{
            return $this->$property;
        }
	}

}

?>