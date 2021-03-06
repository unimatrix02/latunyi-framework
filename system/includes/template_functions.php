<?php
/**
 *	Template functions.
 *
 *	@author      Raymond van Velzen <raymond@latunyi.com>
 *	@package     LatunyiFramework
 **/


/**
 *	Returns each of the given values in turn.
 *
 *	@return		mixed				Field => message list of failed fields
 **/
function cycle()
{
	static $cycle_values;
	static $current_index;

	// Set the value list if empty and start counting at 0
	if (!isset($cycle_values))
	{
		if (func_num_args() > 0)
		{
			$cycle_values = func_get_args();
			$current_index = 0;
		}
	}

	// If available, return the element at the current index
	// and increase it, resetting to 0 when the last element is reached
	if (isset($cycle_values[$current_index]))
	{
		$retval = $cycle_values[$current_index];
		$current_index++;
		if ($current_index == count($cycle_values))
		{
			$current_index = 0;
		}
		return $retval;
	}
}

/**
 *	select_options
 *
 *	Renders options for a select element.
 *
 *	@param		mixed	$opt		Array of options (key => value)
 *	@param		mixed	$selected	Selected value, default null
 *	@return		string
 **/
function select_options($options, $selected = null)
{
	$str = '';
	foreach ($options as $key => $value)
	{
		$str .= '<option value="' . $key . '"';
		if (isset($selected) && $selected == $key)
		{
			$str .= ' selected="true"';
		}
		$str .= '>' . $value . '</option>';
	}

	return $str;
}

/**
 *	checkboxes
 *
 *	Renders checkboxes with labels.
 *
 *	@param		string	$name		Name
 *	@param		mixed	$opt		Array of options (key => value)
 *	@param		mixed	$selected	Selected value, default null
 *	@return		string
 **/
function checkboxes($name, $options, $selected = null)
{
	$str = '';
	$i = 1;
	foreach ($options as $value => $label)
	{
		$id = $name . '_' . $i++;
		$str .= '<label for="' . $id . '">';
		$str .= '<input name="' . $name . '[]" type="checkbox" id="' . $id . '" value="' . $value . '"';
		if (isset($selected) && $selected == $value)
		{
			$str .= ' checked="true"';
		}
		$str .= '/> <span> ' . $label . '</span></label>' . "\n";
	}

	return $str;
}

/**
 *	paginate
 *
 *	Renders a list of page numbers for navigating a paged list.
 *
 *	@param		int		$total		Total number of items
 *	@param		int		$page_size	Number of items on each page
 *	@param		int		$cur_page	Current page number
 *	@param		string	$url		URL of list page
 *	@param		string	$prev		Translation for "previous"
 *	@param		string	$next		Translation for "next"
 *	@return		string				Navigation for pages
 **/
function paginate($total, $page_size, $cur_page, $url, $prev = 'Previous', $next = 'Next')
{
	// Build page numbers
	$num_pages = ceil($total / $page_size);

	if ($num_pages <= 1)
	{
		return '';
	}

	$str = '';

	$str .= '<table class="page_nav"><tr><td class="page_nav_left">';

	// Show previous link
	if ($cur_page > 1)
	{
		$str .= '<a class="page_prev" href="' . $url . '/' . ($cur_page - 1) . '">&laquo; ' . $prev . '</a>';
	}
	else
	{
		$str .= '&nbsp;';
	}

	$str .= '</td><td class="page_numbers_wrapper"><table class="page_numbers"><tr>';

	for ($i = 1; $i <= $num_pages; $i++)
	{
	if ($i != $cur_page)
	{
	$str .= '<td>';
	$str .= '<a href="' . $url . '/' . $i . '">' . $i . '</a>';
	$str .= '</td>';
	/*
	if (abs($cur_page - $i) <= 3)
	{
	$str .= '<td>';
	$str .= '<a href="' . $url . '/' . $i . '">' . $i . '</a>';
	$str .= '</td>';
	}
	(*/
		}
		else
	{
		$str .= '<td class="current_page_number"><div>' . $i;
		$str .= '</div></td>';
	}
	}
	$str .= '</tr></table></td><td class="page_nav_right">';

	// Show next link
	if ($cur_page < $num_pages)
	{
	$str .= '<a class="page_next" href="' . $url . '/' . ($cur_page + 1) . '">' . $next . ' &raquo;</a>';
	}
	else
	{
	$str .= '&nbsp;';
	}

	$str .= '</td></tr></table>';

	return $str;
}

/**
 * Prints a single message, or a field from DataContainer.
 * 
 * @param string $field_or_msg
 * @param DataContainer|null $errors
 * @return string
 */
function print_error($field_or_msg, $errors = null)
{
	$output = '';
	if ($errors instanceOf \System\Core\Datacontainer)
	{
		$field = $field_or_msg;
		if ($errors->has($field))
		{
			$output = $errors->$field;
		}
	}
	elseif (is_array($errors))
	{
		$field = $field_or_msg;
		if (!empty($errors[$field]))
		{
			$output = $errors[$field];
		}
	}
	elseif ($errors == null)
	{
		$output = $field_or_msg;
	}
	if (!empty($output))
	{
		return '<div class="error">' . $output . '</div>';
	}
}