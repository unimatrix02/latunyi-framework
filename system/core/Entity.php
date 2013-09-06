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
	public function __construct($data = null)
	{
		if (!empty($data))
		{
			$this->fillProperties($data);
		}
		
		$this->mapProperties();
	}
	
	/**
	 * Method that "renames" properties containing underscores to camelCased versions.
	 * Example: list_id ==> listId
	 */
	protected function mapProperties()
	{
		$properties = get_object_vars($this);
		foreach ($properties as $name => $value)
		{
			$oldName = $name;
			// If name has underscore in it
			if (strpos($name, '_') !== false)
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
				
				$this->$name = $this->$oldName;
				unset($this->$oldName); 
			}
		}
	} 
	
	/**
	 * Fills the properies of the entity with the given array of data.
	 * 
	 * @param array $data
	 */
	protected function fillProperties($data)
	{
		if (!is_array($data))
		{
			return;
		}
		
		foreach ($data as $field => $value)
		{
			if (property_exists($this, $field))
			{
				$this->$field = $value;
			}
		}
	}
	
}