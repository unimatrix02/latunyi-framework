<?php
/**
 *	Validator helper class.
 *
 *	@author      Raymond van Velzen <raymond@latunyi.com>
 *	@package     LatunyiFramework
 **/

namespace System\Helper;

/**
 * The Validator class offers standard functions to validate values.
 */
class Validator 
{
	/**
	*	Checks if the given value is an empty string.
	*
	*	@param		mixed	$value	Input value
	*	@return		bool			Validation result
	**/
	static public function isNotEmpty($value) 
	{
		if (is_array($value))
		{
			return !empty($value);
		}
		
		$value = (string)$value;
		$value = trim($value);
		return strlen($value) > 0;

		throw new \Exception('Validation error: Invalid value used for isNotEmpty()');
	}

	/**
	*	Checks if the given value is a valid number.
	*
	*	@param		mixed	$value	Input value
	*	@return		bool			Validation result
	**/
	static public function isNumber($value) 
	{
		return is_numeric($value);
	}

	/**
	*	Checks if the given value is a valid integer.
	*
	*	@param		mixed	$value	Input value
	*	@return		bool			Validation result
	**/
	static public function isInteger($value) 
	{
		return regex_check('/^\d+$/', $value);
	}

	/**
	*	Checks if the given value is a valid amount, such as 3 or 3.40
	*	or 3,40.
	*
	*	@param		mixed	$value	Input value
	*	@return		bool			Validation result
	**/
	static public function isAmount($value) 
	{
		return regex_check('/^\d+((\.|,)\d{1,2}){0,1}$/', $value);
	}

	/**
	*	Checks if the given value only contains A-Z characters.
	*
	*	@param		mixed	$value	Input value
	*	@return		bool			Validation result
	**/
	static public function isWord($value) 
	{
		$value = trim($value);
		return regex_check('/^[A-Z]+$/i', $value);
	}

	/**
	*	Checks if the given string is a valid date. Input is assumed
	*	to be an Dutch formatted date (DD-MM-YYYY).
	*
	*	@param		string	$str	Input string	
	*	@return		bool			Validation result
	**/
	static public function isDutchDate($value) 
	{
		// Check basic format
		if (preg_match('/^\d{2}-\d{2}-\d{4}$/', $value) == 0)
		{
			return false;
		}

		// Check valid
		return (date('d-m-Y', strtotime($value)) == $value);
	}

	/**
	*	Checks if the given string is a valid date. Input is assumed
	*	to be an ISO formatted date (YYYY-MM-DD).
	*
	*	@param		string	$str	Input string	
	*	@return		bool			Validation result
	**/
	static public function isDate($value) 
	{
		// Check basic format
		if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) == 0)
		{
			return false;
		}

		// Check valid
		list($year, $month, $day) = explode('-', $value);
		return checkdate($month, $day, $year);
	}

	/**
	*	Checks if the given string is a valid locale, like "en_us".
	*
	*	@param		string	$str	Input string	
	*	@return		bool			Validation result
	**/
	static public function isLocale($value) 
	{
		// Check basic format
		if (preg_match('/^[a-z]{2}_[a-z]{2}$/', $value) == 0)
		{
			return false;
		}
		return true;
	}

	/**
	*	Checks if the given start/end date constitute a valid period
	*	(start < end).
	*
	*	@param		string	$startDate	Start date, ISO format
	*	@param		string	$endDate	End date, ISO format
	*	@return		bool				Validation result
	**/
	static public function isValidPeriod($startDate, $endDate) 
	{
		// Check for start and end keys
		if (empty($startDate) || empty($endDate))
		{
			return false;
		}
	
		$start = strtotime($startDate);
		$end = strtotime($endDate);
		return ($start < $end);
	}

	/**
	*	Checks if the given email address is valid.
	*
	*	@param		string	$email	Email address
	*	@return		bool			Validation result
	**/
	static public function isValidEmail($email) 
	{
		$pattern = '/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-\.])+\.)+([a-zA-Z0-9]{2,4})+$/';
		return regex_check($pattern, $email);
	}

}