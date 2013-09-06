<?php
/**
 *	Query parameters class.
 *
 *	@author      Raymond van Velzen <raymond@latunyi.com>
 *	@package     LatunyiFramework
 **/

namespace System\Core\Database;

use \System\Core\ObjectList;

/**
 * Class to represent a list of query conditions
 */
class QueryParams extends ObjectList
{
	/**
	 * Shortcut to add QueryParams
	 * 
	 * @param	string	$fieldName
	 * @param	string	$fieldValue
	 * @param	string	$operator		Operator, like =, =>, etc
	 */
 	public function add($fieldName, $fieldValue, $operator = '=')
 	{
 		$this->objects[] = new QueryParam($fieldName, $fieldValue, $operator);
 	}

 	/**
 	 * Returns the object list as an array.
 	 * 
 	 * @return array
 	 */
 	public function asArray()
 	{
 		$list = array();
 		foreach ($this->objects as $cond)
 		{
 			if (is_array($cond->fieldValue))
 			{
 				$cond->fieldValue = implode(',', $cond->fieldValue);
 			}
 			$list[$cond->fieldName] = $cond->fieldValue;
 		}
 		return $list;
 	}
}