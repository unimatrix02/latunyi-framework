<?php
/**
 *	Initialization
 *
 *	@author      Raymond van Velzen <raymond@latunyi.com>
 *	@package     LatunyiFramework
 **/

// Determine root for this site
$base = dirname($_SERVER['PHP_SELF']);
if ($base == '/')
{
	$base = '';
}

// Web root = web directory
define('WEB_ROOT', $_SERVER['DOCUMENT_ROOT'] . $base);

// Document root = root directory of virtual host
/**
 * Document root of virtual host
 * @var string
 */
define('DOC_ROOT', dirname($_SERVER['DOCUMENT_ROOT']));

// Public root = subdir of virtual host, if any
define('PUBLIC_ROOT', $base);

/**
 * Path to system directory
 * @var string
 */
define('SYSTEM_PATH', DOC_ROOT . '/system');

/**
 * Path to system core directory
 * @var string 
 */
define('CORE_PATH', DOC_ROOT . '/system/core');

/**
 * Path to application directory
 * @var string
 */
define('APP_PATH', DOC_ROOT . '/application');

// Special includes
if (!function_exists('pr'))
{
	include_once(SYSTEM_PATH . '/includes/debug.inc.php');
}
include_once(SYSTEM_PATH . '/includes/functions.inc.php');
