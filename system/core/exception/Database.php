<?php
namespace System\Core\Exception;

class Database extends \Exception
{
	public function __construct(\PDOException $ex)
	{
		$msg = 'Database: ' . substr($ex->getMessage(), 23);
		parent::__construct($msg, $ex->getCode());
	}
}

?>