<?php
/**
 * Entry point for all web requests
 */

$startTime = microtime(true);

// Set document root if not set (when running on CLI)
$isWebRequest = true;
if (!isset($_SERVER['DOCUMENT_ROOT']) || empty($_SERVER['DOCUMENT_ROOT']))
{
	$isWebRequest = false;
	
	$currentDir = getcwd();
	$scriptDir = dirname($_SERVER['PHP_SELF']);

	if ($scriptDir == '.')
	{
		$documentRoot = $currentDir;
	}
	else
	{
		$documentRoot = $currentDir . '/' . $scriptDir;
	}

	// Cutoff /web
	$_SERVER['DOCUMENT_ROOT'] = $documentRoot;
	
	// If given, take parameter as request URI
	if (isset($argv))
	{
		if (!isset($argv[1]))
		{
			die('Error: Missing second parameter for request path' . PHP_EOL); 
		}
		else
		{
			$_SERVER['REQUEST_URI'] = $argv[1];
		}
	}
}

// Initialization
require(dirname($_SERVER['DOCUMENT_ROOT']) . '/system/includes/init.inc.php');

// Setup class loading
require(CORE_PATH . '/SplClassLoader.php');
$classLoader = new SplClassLoader('System', DOC_ROOT);
$classLoader->register();
$classLoader = new SplClassLoader('Application', DOC_ROOT);
$classLoader->register();

try
{
	$appl = new Application\Application($isWebRequest);
	$appl->initialize();
	$appl->routeRequest();
	$appl->createController();
	$appl->runControllerMethod();
	$appl->renderResponse();
	$appl->sendOutput();
}
catch (\Exception $ex)
{
	pr('Error: ' . $ex->getMessage());
	pr('Location: ' . $ex->getFile() . ', line ' . $ex->getLine());
}	

$endTime = microtime(true);
$duration = $endTime - $startTime;
//pr('Duration: ' . $duration);
//pr(GetMemory());
