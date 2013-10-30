<?php
/**
 *	Base entity class.
 *
 *	@author      Raymond van Velzen <raymond@latunyi.com>
 *	@package     LatunyiFramework
 **/

namespace System\Core;

/**
 * Base class for entities.
 */
class Entity
{
	/**
	 * Constructor. Runs the mapProperties() method.
	 */
	public function __construct($data = null, $strict = false)
	{
		if (!empty($data))
		{
			$this->fillProperties($data, $strict);
		}
	}
	
	/**
	 * Fills the properies of the entity with the given array of data.
	 * 
	 * @param array $data
	 * @param bool $strict Only set existing properties
	 */
	protected function fillProperties($data, $strict = false)
	{
		if (!is_array($data) || !is_bool($strict))
		{
			return;
		}
		
		foreach ($data as $field => $value)
		{
			if (!$strict || ($strict && property_exists($this, $field)))
			{
				$this->$field = $value;
			}
		}
	}
	
}