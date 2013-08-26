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
	 * Full path/filename of the currently used log file.
	 * 
	 * @var string
	 */
	private $currentLogFile;

	/**
	 * Constructor. Checks if the given path exists,
	 * creates the directory if not.
	 *
	 * @param string $logFilePath
	 */
	public function __construct($logFilePath)
	{
		$this->logFilePath = $logFilePath;
	}
	
	/**
	 * Writes data to the log file.
	 * 
	 * @param mixed $data
	 * @return void
	 */
	public function add($data, $withDate = true)
	{
		$this->checkFileOpen();
		
		if (is_array($data) || is_object($data)) 
		{
			ob_start();
			print_r($data);
			$data = ob_get_clean();
		}

		$msg = '';
		if ($withDate)
		{
			$msg .= '[' . date('Y-m-d H:i:s') . '] ';
		}
		$msg .= $data . "\n";
		
		$result = fwrite($this->fileHandle, $msg, strlen($msg));
		
		if (false === $result)
		{
			throw new \Exception('Failed to write to log file ' . $this->currentLogFile);
		}
	}
	
	/**
	 * Checks if $this->fileHandle contains a resource;
	 * if not, opens the log file (with today's date in the name)
	 * and stores the file handle.
	 * 
	 * @throws \Exception
	 */
	private function checkFileOpen()
	{
		// Check for open file
		if (!is_resource($this->fileHandle))
		{
			// Determine file name to write to
			$targetFile = $this->logFilePath . '/app_' . date('Y-m-d') . '.log';
				
			// Check file exists and is writable
			if (!is_writable($targetFile))
			{
				// Make path and file
				@mkdir($this->logFilePath);
		
				if (!file_exists($this->logFilePath))
				{
					throw new \Exception('Can\'t create dir for log file ' . $this->logFilePath);
				}
			}
			
			$this->currentLogFile = $targetFile;
				
			// Get handle to file
			$this->fileHandle = fopen($targetFile, 'a');
				
			if (false === $this->fileHandle)
			{
				throw new \Exception('Can\'t open log file ' . $this->currentLogFile);
			}
			
			//$this->add('', false);
		}
	}

}
