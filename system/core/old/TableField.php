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
*	The TableField class represents a column/field in a database table.
*
*	@author      	Raymond van Velzen <raymond@latunyi.com>
*	@copyright		Latunyi
*	@package     	Meridium
**/
class TableField {

	// Field name from db
	private $name;

	// Data type
	private $type;

	// Foreign or not
	private $foreign = false;

	// Alias (for foreign fields
	private $alias;

	// Foreign table name
	private $table;

	// Join from (foreign key in this table)
	private $join_from;

	// Join to (primary key in foreign table)
	private $join_to;
	
	// Join from table (second-level table for joins)
	private $join_from_table;

	// Join type (INNER, LEFT)
	private $join_type;

	// -------------------------------------------------------------------

	/**
	*	Constructor
	**/
	public function __construct(&$data) {

		// Copy data 
		$props = get_class_vars(get_class($this));
		foreach($props as $key => $val) {
			if (isset($data[$key])) {
				$this->$key = $data[$key];
			}
		}

	}
	
	// -------------------------------------------------------------------

	/**
	*	Retrieve properties
	**/
	public function __call($method, $args) {

		// Check for set/get and property name
		$prefix = strtolower(substr($method, 0, 3));
        $property = strtolower(substr($method, 3));

		// No prefix or property: exit
        if (empty($prefix) || empty($property)) {
            return;
        }

		// Return property value, if present
		if ($prefix == "get" && isset($this->$property)) {
            return $this->$property;
        }

	}

	// -------------------------------------------------------------------

}

?>