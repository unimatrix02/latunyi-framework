<?php
/**
 *	Type entity class.
 *
 *	@author      Raymond van Velzen <raymond@latunyi.com>
 *	@package     LatunyiFramework
 **/

namespace Application\Domain\Entity;

/**
 * Type entity.
 */
class Type extends \System\Core\Entity  
{
	/**
	 * ID, autoincrement
	 * @var int
	 */
	public $id;
	
	/**
	 * Name
	 * @var string
	 */
	public $name;
}
