<?php

/*========================================================================
                    _          _                     _
                   | |    __ _| |_ _   _ _ __  _   _(_)
                   | |   / _` | __| | | | '_ \| | | | |
                   | |__| (_| | |_| |_| | | | | |_| | |
                   |_____\__,_|\__|\__,_|_| |_|\__, |_|
                                               |___/

=========================================================================*/
/**
*	Helper functions library
*
*	@author      Raymond van Velzen <raymond@latunyi.com>
*	@package     Meridium
**/

// -----------------------------------------------------------------------

/**
*	redirect
*
*	Redirects to the specified URL, using header() and exit().
*
*	@param	string	$url		URL to go to
*	@param	bool	$with_root	Prefix WEB_ROOT
*	@return	void
**/
function redirect($url, $with_root = true)
{
	if (defined('WEB_ROOT') && $with_root)
	{
		header('Location: ' . WEB_ROOT . $url);
	}
	else
	{
		header('Location: ' . $url);
	}
	exit;
}

// -----------------------------------------------------------------------

/**
*	reload
*
*	Reload the current page using header().
*
*	@return	void
**/
function reload()
{
	$url = $_SERVER['REQUEST_URI'];
	header('Location: '.$url);
	exit;
}

// -----------------------------------------------------------------------

/**
*	session_clean
*
*	Destroys the current session and starts a new one.
*
*	@return	void
**/
function session_clean()
{
	$_SESSION = array();

	if (isset($_COOKIE[session_name()])) {
	    setcookie(session_name(), '', time()-42000, '/');
	}

	session_destroy();
	session_start();
}

// -----------------------------------------------------------------------

/**
*	s_unset
*
*	Unset one or more variables from $_SESSION with isset() check.
*	Will iterate over over supplied arguments.
*
*	@return	void
**/
function s_unset()
{
	$args = func_get_args();
	for ($i = 0; $i < func_num_args(); $i++)
	{
		if (isset($_SESSION[$args[$i]]))
		{
			unset($_SESSION[$args[$i]]);
		}
	}
}

// -----------------------------------------------------------------------

/**
*	s_isset
*
*	Check if a variable is set in the session.
*
*	@param	string	$name	Variable name
*	@return	bool			True: exists
**/
function s_isset($name)
{
	return isset($_SESSION[$name]);
}

// -----------------------------------------------------------------------

/**
*	get_subdirs
*
*	Returns all subdirs or empty array, non-recursive.
*
*	@param	string 	$root_dir	Root directory
*	@param	mixed	$dir_list	Array with dirs, by ref, default empty array
*	@return	mixed				Array
**/
//
function get_subdirs($root_dir)
{
	// Check if root_dir exists
	if (!file_exists($root_dir))
	{
		trigger_error('GetSubdirs(): $root_dir "' . $root_dir . '" does not exist.', E_USER_WARNING);
		return array();
	}
	$handle = @opendir($root_dir);
	if ($handle === false)
	{
		trigger_error('GetSubdirs(): Cannot open $root_dir "' . $root_dir . '".', E_USER_WARNING);
		return array();
	}

	$dir_list = array();
	while ( ($file = readdir($handle) ) !== false)
	{
		if (filetype($root_dir . '/' . $file) == 'dir'
			&& substr($file, 0, 1) !== '.')
		{
			$dir_list[] = $root_dir . '/' . $file;
		}
	}
	@closedir($root_dir);
	return $dir_list;
}

// -----------------------------------------------------------------------

/**
*	Iterates over the given array $data. Takes out all keys that start with
*	$filter and returns it.
**/
function filter_array($data, $filter)
{
	$clean = array();
	foreach ($data as $key => $val)
	{
		if (substr($key, 0, strlen($filter)) == $filter)
		{
			$clean[substr($key, strlen($filter))] = $val;
		}
	}

	// Check: not all empty
	$is_empty = true;
	foreach ($clean as $item)
	{
		if (strlen($item) > 0)
		{
			$is_empty = false;
			break;
		}
	}
	if ($is_empty)
	{
		return array();
	}

	return $clean;
}

// -----------------------------------------------------------------------

/**
*	now
*
*	Returns current date/time as YYYY-MM-DD HH:MM:SS.
**/
function now()
{
	return date('Y-m-d H:i:s');
}

// -----------------------------------------------------------------------

/**
*	date_iso
*
*	Returns an ISO date. If the given date is DD-MM-YYYY, it will be
*	reversed.
**/
function date_iso($str)
{
	if (!empty($str))
	{
		return date('Y-m-d', strtotime($str));
	}
	else
	{
		return '';
	}
}

// -----------------------------------------------------------------------

/**
*	date_dutch
*
*	Returns an Dutch date (DD-MM-YYYY).
**/
function date_dutch($str)
{
	if (!empty($str))
	{
		return date('d-m-Y', strtotime($str));
	}
	else
	{
		return '';
	}
}

// -----------------------------------------------------------------------

/**
*	regex_matches
*
*	Returns all matches as array using preg_match.
*
*	@param	string	$pattern	Pattern to match on
*	@param	string	$input		Input string
*	@return	mixed				Matches
**/
function regex_matches($pattern, $input)
{
	$matches = array();
	preg_match_all($pattern, $input, $matches);
	if (!empty($matches))
	{
		return $matches;
	}
	return $matches;
}

// -----------------------------------------------------------------------

/**
*	regex_matches
*
*	Returns the first match as string using preg_match.
*
*	@param	string	$pattern	Pattern to match on
*	@param	string	$input		Input string
*	@return	string				First match
**/
function regex_match($pattern, $input)
{
	$matches = regex_matches($pattern, $input);
	if (!empty($matches))
	{
		if (is_array($matches[0]))
		{
			if (!empty($matches[0]))
			{
				return $matches[0][0];
			}
			else
			{
				return '';
			}
		}
		else
		{
			return $matches[0];
		}
	}
	else
	{
		return '';
	}
}

// -----------------------------------------------------------------------

/**
*	regex_check
*
*	Returns true if the given input matches the pattern.
*
*	@param	string	$pattern	Pattern to match on
*	@param	string	$input		Input string
*	@return	bool				True: match found
**/
function regex_check($pattern, $input)
{
	$matches = regex_match($pattern, $input);
	if (!empty($matches))
	{
		return true;
	}
	else
	{
		return false;
	}
}

// -----------------------------------------------------------------------

/**
*	str_contains
*
*	Returns true if the 'needle' is found in the 'haystack'.
*
*	@param	string	$haystack	String to search in
*	@param	string	$needle		String to search for
*	@return	bool				True: needle found in haystack
**/
function str_contains($haystack, $needle)
{
	return (strpos($haystack, $needle) !== false);
}

// -----------------------------------------------------------------------

/**
*	unique_id
*
*	Wrapper for uniqid, used with mt_rand() as source of number, and
*	base_convert for brevity.
*
*	@return	string	Unique ID
**/
function unique_id()
{
	$x = uniqid(mt_rand(0, 100), true);
	return strtoupper(base_convert($x, 10, 36));
}

// -----------------------------------------------------------------------

/**
*	convert_filesize
*
*	Returns a human-readable size (... KB, MB, GB).
*
*	@param	int		$bytes		Number of bytes
*	@return	string				Size in KB/MB/GB
**/
function convert_filesize($bytes)
{
    if ($bytes <= 0)
    {
        return '0 byte';
   	}
    $convention = 1024;
    $s = array('B', 'KB', 'MB', 'GB');
    $e = floor(log($bytes, $convention));
    return round($bytes / pow($convention, $e), 1) . ' ' . $s[$e];
}

// -----------------------------------------------------------------------

/**
*	call_static_method
*
*	Calls a static method from a class using call_user_func
*	and returns the result.
*
*	@param	string	$class		Class name
*	@param	string	$method		Method to call
*	@return	mixed				Result of call_user_func
**/
function call_static_method($class, $method)
{
	$func = array($class, $method);
	return call_user_func($func);
}

// -----------------------------------------------------------------------

/**
*	get_static_property
*
*	Returns the value of a static property of a class using a reflection
*	class.
*
*	@param	string	$class		Class name
*	@param	string	$property	Property to read
*	@return	mixed				Value of property
**/
function get_static_property($class, $property)
{
	$refl = new ReflectionClass($class);
	$val =  $refl->getStaticPropertyValue($property);
	unset($refl);
	return $val;
}

// -----------------------------------------------------------------------

/**
*	add_to_include_path
*
*	Adds the given path to the include path.
*
*	@param	string	$path		Path to add
*	@return	void
**/
function add_to_include_path($path)
{
	set_include_path(get_include_path() . PATH_SEPARATOR . $path);
}

// -----------------------------------------------------------------------

/**
*	timer
*
*	Remembers the current time on first call, and returns the difference
*   on the second call.
*
*	@param	string	$class		Class name
*	@param	string	$property	Property to read
*	@return	mixed				Value of property
**/
function timer()
{
    static $a;
    if (empty($a))
    {
    	$a = microtime(true);
    }
    else
    {
    	$time = (string)(microtime(true) - $a);
    	unset($a);
    	return $time;
    }
}

// -----------------------------------------------------------------------

/**
 * @brief Generates a Universally Unique IDentifier, version 4.
 *
 * This function generates a truly random UUID. The built in CakePHP String::uuid() function
 * is not cryptographically secure. You should uses this function instead.
 *
 * @see http://tools.ietf.org/html/rfc4122#section-4.4
 * @see http://en.wikipedia.org/wiki/UUID
 * @return string A UUID, made up of 32 hex digits and 4 hyphens.
 */
function get_uuid() {

    $pr_bits = null;
    $fp = @fopen('/dev/urandom','rb');
    if ($fp !== false) {
        $pr_bits .= @fread($fp, 16);
        @fclose($fp);
    } else {
        // If /dev/urandom isn't available (eg: in non-unix systems), use mt_rand().
        $pr_bits = "";
        for($cnt=0; $cnt < 16; $cnt++){
            $pr_bits .= chr(mt_rand(0, 255));
        }
    }

    $time_low = bin2hex(substr($pr_bits,0, 4));
    $time_mid = bin2hex(substr($pr_bits,4, 2));
    $time_hi_and_version = bin2hex(substr($pr_bits,6, 2));
    $clock_seq_hi_and_reserved = bin2hex(substr($pr_bits,8, 2));
    $node = bin2hex(substr($pr_bits,10, 6));

    /**
     * Set the four most significant bits (bits 12 through 15) of the
     * time_hi_and_version field to the 4-bit version number from
     * Section 4.1.3.
     * @see http://tools.ietf.org/html/rfc4122#section-4.1.3
     */
    $time_hi_and_version = hexdec($time_hi_and_version);
    $time_hi_and_version = $time_hi_and_version >> 4;
    $time_hi_and_version = $time_hi_and_version | 0x4000;

    /**
     * Set the two most significant bits (bits 6 and 7) of the
     * clock_seq_hi_and_reserved to zero and one, respectively.
     */
    $clock_seq_hi_and_reserved = hexdec($clock_seq_hi_and_reserved);
    $clock_seq_hi_and_reserved = $clock_seq_hi_and_reserved >> 2;
    $clock_seq_hi_and_reserved = $clock_seq_hi_and_reserved | 0x8000;

    return strtoupper(sprintf('%08s-%04s-%04x-%04x-%012s',
        $time_low, $time_mid, $time_hi_and_version, $clock_seq_hi_and_reserved, $node));
}

// -----------------------------------------------------------------------

/**
*	__
*
*	Extension of gettext() to support placeholder replacement.
*
*	@param	string	$string		String to translate
*	@return	string				Translated string
**/
function __($string)
{
    $arg = array();
    for($i = 1 ; $i < func_num_args(); $i++)
        $arg[] = func_get_arg($i);

    return vsprintf(gettext($string), $arg);
}

// -----------------------------------------------------------------------

// Returns the value of the given variable, or empty string if not set
function val(&$x)
{
	return (isset($x) ? $x : '');
}


function arrayToObject($array)
{
    if (!is_array($array)) {
        return $array;
    }

    $object = new stdClass();
    if (is_array($array) && count($array) > 0)
    {
        foreach ($array as $name=>$value) {
            $name = strtolower(trim($name));
            if (!empty($name)) {
                $object->$name = arrayToObject($value);
            }
        }
        return $object;
    }
    else {
        return false;
    }
}

/**
 * Returns the local server's hostname.
 *
 * @return string
 */
function getServerHostName()
{
	$str = shell_exec('hostname');
	$str = str_replace("\n", '', $str);
	return $str;
}