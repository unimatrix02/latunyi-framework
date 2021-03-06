<?php
/**
 *	Object list class.
 *
 *	@author      Raymond van Velzen <raymond@latunyi.com>
 *	@package     LatunyiFramework
 **/

namespace System\Core;

/**
 * Class to represent a list of objects
 */
class ObjectList implements \Iterator
{
	/**
	 * Current position in the list.
	 * @var int
	 */
	private $position = 0;
	
	/**
	 * Array of objects.
	 * @var array
	 */
	protected $objects;
	
	/**
	 * Constructor. Sets the position to 0 and initializes the list of objects as an empty array.
	 */
	public function __construct()
	{
		$this->position = 0;
		$this->objects = array();
	}

	/**
	 * Adds an object to the list.
	 * 
	 * @param object $object
	 */
 	public function add($object)
 	{
 		$this->objects[] = $object;
 	}

 	/**
 	 * Returns the current object.
 	 * 
 	 * @see Iterator::current()
 	 */
	public function current()
	{
		return $this->objects[$this->position];
	}

	/**
	 * Returns the current position.
	 * 
	 * @see Iterator::key()
	 */
	public function key()
	{
		return $this->position;
	}

	/**
	 * Increases the position by one.
	 * 
	 * @see Iterator::next()
	 */
	public function next()
	{
		$this->position++;
	}

	/**
	 * Resets the position to 0.
	 * 
	 * @see Iterator::rewind()
	 */
	public function rewind()
	{
		$this->position = 0;		
	}

	/**
	 * Checks if an object exists at the current position.
	 * 
	 * @see Iterator::valid()
	 */
	public function valid()
	{
		return isset($this->objects[$this->position]);
	}
 	
}