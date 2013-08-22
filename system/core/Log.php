<?php
namespace System\Core;

/**
 * Class for logging.
 */
class Log
{
	private $logFilePath;

	/**
	 * Handle of the log file
	 * @var resource
	 */
	private $fileHandle;

	/**
	 * Constructor. Checks if the given path exists,
	 * creates the directory if not.
	 *
	 * @param string $logFilePath
	 */
	public function __construct($logFilePath)
	{

	}

}
