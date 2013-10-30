<?php
/**
 *	Item DataMapper class.
 *
 *	@author      Raymond van Velzen <raymond@latunyi.com>
 *	@package     LatunyiFramework
 **/

namespace Application\Database;

/**
 * Child class for the Item DataMapper.
 */
class ItemDataMapper extends \System\Core\Database\DataMapper
{
	/**
	 * Constructor, sets the map and entity class.
	 */
	public function __construct()
	{
		$map = array(
			'typeId'        => 'type_id',
			'startDate'     => 'start_date',
			'endDate'       => 'end_date'
		);

		parent::__construct($map, 'Item');
	}
}