<?php
/**
 * Entry point for all web requests 
 */

// Set document root if not set (when running on CLI)
if (!isset($_SERVER['DOCUMENT_ROOT']) || empty($_SERVER['DOCUMENT_ROOT']))
{
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
}

// Initialization
require(dirname($_SERVER['DOCUMENT_ROOT']) . '/system/includes/init.inc.php');

// Setup class loading
require(CORE_PATH . '/SplClassLoader.php');
$classLoader = new SplClassLoader('System', DOC_ROOT);
$classLoader->register();
$classLoader = new SplClassLoader('Application', DOC_ROOT);
$classLoader->register();

// Initialize application
$appl = new Application\Application();
prx($appl);


// Run application