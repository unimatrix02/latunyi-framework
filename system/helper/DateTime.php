<?php
/**
 *	Date/time helper class.
 *
 *	@author      Raymond van Velzen <raymond@latunyi.com>
 *	@package     LatunyiFramework
 **/

namespace System\Helper;

/**
*	DateTime
*
*	Class with methods for common date/time needs.
*
*	@abstract
*	@author      Raymond van Velzen <raymond@latunyi.com>
**/
class DateTime
{
	/**
	*	Returns the unix timestamp for the first day of the
	*	given week in the given year (or the current week 
	*	of the current year if no week or is given).
	*
	*	@param		int		$weeknr		Week number, default 0
	*	@param		int		$year		Year, default 0
	*	@return		int					Unix timestamp
	**/
    static public function getFirstDayOfWeek($weeknr = 0, $year = 0) 
    {
    	// Use current week if 0
    	if ($weeknr == 0)
    	{
    		$weeknr = date('W');
    	}

    	// Use current year if 0
    	if ($year == 0)
    	{
    		$year = date('Y');
    	}
    	
    	// Make ts for first day of this year
    	$firstday = mktime(0, 0, 0, 1, 1, $year);
    	
    	// Get weekday nr of first day
    	$firstday_weekday = date('N', $firstday);
    	
    	// If first day is a monday
    	if ($firstday_weekday == 1)
    	{
    		$first_monday = $firstday;
    	}
    	else
    	{
    		// Find previous monday
    		$first_monday = $firstday - (($firstday_weekday - 1) * 86400);
    	}

    	// Correct for week 52/53 cases
    	if (date('W', $firstday) != 1)
    	{
    		// Shift +1 week
    		$first_monday += (7 * 86400);
    	}

    	// Jump to correct monday
    	if ($weeknr > 1)
    	{
    		return $first_monday + (($weeknr - 1) * 7 * 86400);
    	}
    	else
    	{
    		return $first_monday;
    	}
	}

	/**
	*	Returns the unix timestamp for the last day of the
	*	given week in the given year (or the current week 
	*	of the current year if no week or is given).
	*
	*	@param		int		$weeknr		Week number, default 0
	*	@param		int		$year		Year, default 0
	*	@return		int					Unix timestamp
	**/
    static public function getLastDayOfWeek($weeknr = 0, $year = 0) 
    {
    	$first_day = self::GetFirstDayOfWeek($weeknr, $year);
    	return $first_day + (6 * 86400);
    }
    
	/**
    *	Converts a number of hours (float) to the 00:00 format.
    *
	*	@param		float	$number		Number of hours
	*	@return		string				Time string
	**/
    static public function convertToTime($number) 
    {
    	$hours = floor($number);
    	$number = $number - $hours;
    	$minutes = round($number * 60);
		return sprintf('%02d', $hours) . ':' . sprintf('%02d', $minutes);
    }

	/**
    *	Converts a time (00:00 format) to a number of hours (float).
    *
	*	@param		string	$time		Time string
	*	@return		float				Number of hours
	**/
    static public function convertToFloat($time) 
    {
    	$parts = explode(':', $time);
    	if (count($parts) > 1)
    	{
	    	$hours = $parts[0];
	    	$minutes = $parts[1];
	    }
	    else
	    {
	    	$hours = $parts[0];
	    	$minutes = 0;
	    }
		return $hours + ($minutes / 60);
    }

	/**
	*	Iterates over the given array and converts any dates
	*	(dd-mm-yyyy) to ISO format (yyyy-mm-dd).
	*
	*	@param	mixed	$data	Array with data, by ref
	*	@return	void
	*
	**/
	static public function convertDates(&$data) 
	{
		if (!is_array($data))
		{
			return;
		}
		
		foreach ($data as &$value)
		{
			if (preg_match('/\d\d-\d\d-\d\d\d\d/', $value))
			{
				$value = self::ToIsoDate($value);
			}
		}
	} 

	/**
    *	Converts a parseable date string to an ISO date.
    *
	*	@param		string	$date		Date string
	*	@return		string				ISO date string
	**/
    static public function toIsoDate($date) 
    {
    	if (empty($date))
    	{
    		return '';
    	}
		return date('Y-m-d', strtotime($date));
    }

	/**
    *	Returns a ISO-formatted date from the given
    *	year, month and day parts. Returns false
    *	if the combination is not valid.
    *
	*	@param		int		$year		Year
	*	@param		int		$month		Month
	*	@param		int		$day		Day
	*	@return		string|bool			ISO date string or false
	**/
    static public function makeIsoDateFromParts($year, $month, $day) 
    {
    	$date = mktime(0, 0, 0, $month, $day, $year);
    	
    	// Verify parts form valid date
    	if (date('j', $date) != (int)$day || date('n', $date) != (int)$month || date('Y', $date) != (int)$year)
    	{
    		return false;
    	}
    	
		return date('Y-m-d', $date);
    }
}	
