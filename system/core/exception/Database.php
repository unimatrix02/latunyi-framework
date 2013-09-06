<?php
/**
 *	Database exception class.
 *
 *	@author      Raymond van Velzen <raymond@latunyi.com>
 *	@package     LatunyiFramework
 **/

namespace System\Core\Exception;

/**
 * Exception for database errors.
 */
class Database extends \Exception
{
	/**
	 * Constructor, cuts of part of the PDO error message
	 * 
	 * @param \PDOException $ex
	 */
	public function __construct(\PDOException $ex)
	{
		$msg = 'Database: ' . substr($ex->getMessage(), 23);
		parent::__construct($msg, $ex->getCode());
	}
}
