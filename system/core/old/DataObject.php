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
*	DataObject
*
*	@author      	Raymond van Velzen <raymond@latunyi.com>
*	@copyright		Latunyi
*	@package     	Meridium
**/
class DataObject 
{
    /**
    *	$data
    * 
	*	Array for holding data
	*	
    *	@access  protected
    *	@var     mixed
    **/
	protected $data;

	// -------------------------------------------------------------------

	/**
	*	Constructor
	*
	*	Set up the object.
	*
	*	@access		public
	*	@return		void
	**/
    public function __construct() 
    {
        $this->data = array();
    }

	// -------------------------------------------------------------------

	/**
	*	GetData
	*
	*	Returns the dataobject's data.
	*
	*	@return		mixed
	**/
    public function GetData() 
    {
        return $this->data;
    }

	// -------------------------------------------------------------------

	/**
	*	__get
	*
	*	Get data from the dataobject's data property.
	*
	*	@access		public
	*	@return		mixed
	**/
	public function __get($var) 
	{
        if (!isset($this->data[$var])) {
            $this->data[$var] = null;
        }
        return $this->data[$var];
	}

	// -------------------------------------------------------------------

	/**
	*	__set
	*
	*	Set data in the dataobject's data property.
	*
	*	@access		public
	*	@return		mixed
	**/
	public function __set($var, $value) 
	{
		$this->data[$var] = $value; 
	}

	// -------------------------------------------------------------------

	/**
	*	Add
	*
	*	Adds a key/value pair. Useful when a variable is used
	*	to add something.
	*
	*	@param		string	$name		Name
	*	@param		mixed	$value		Value
	*	@return		mixed
	**/
	public function Add($name, $value) 
	{
		$this->data[$name] = $value; 
	}
}	

?>