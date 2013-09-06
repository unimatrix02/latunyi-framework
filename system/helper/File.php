<?php
/**
 *	File helper class.
 *
 *	@author      Raymond van Velzen <raymond@latunyi.com>
 *	@package     LatunyiFramework
 **/

namespace System\Helper;

/**
*	Helper for handling file-related tasks.
*
*	@author			R.N. van Velzen
*	@copyright		Latunyi
*	@package		Meridium
*
**/
class File {

	/**
	*	Checks if an uploaded file exists, is not empty, and has no
	*	error. Throws an exception if any of these is not OK.
	*
	*	@param		string	$file_id	ID of the file in the $_FILES array.
	*	@return		void
	**/
	static public function checkUpload($file_id)
	{
		// Check file was uploaded
		if (empty($_FILES[$file_id]['name']))
		{
			$msg = 'No files were uploaded.';
			throw new Exception($msg);
		}

		// Check for upload error
		if ($_FILES[$file_id]['error'] != 0)
		{
			switch ($_FILES[$file_id]['error'])
			{
				case '1':
					$msg = 'The file is too large. The maximum size is ' . ini_get('upload_max_filesize') . '.';
					break;
				case '4':
					$msg = 'No file was received.';
					break;
				case '7':
					$msg = 'The file can\'t be stored.';
					break;
				default:
					$msg = 'Upload error ' . $_FILES[$file_id]['error'];
			}
			throw new Exception($msg);
		}

		// Check for empty file
		if ($_FILES[$file_id]['size'] == 0)
		{
			$msg = 'The file is empty.';
			throw new Exception($msg);
		}

	}

	/**
	*	Checks the MIME type of a uploaded file. Throws an exception if an
	*	illegal extension is found.
	*
	*	@param		string	$file_id		ID of the file in the $_FILES array
	*	@param		mixed	$allowed_types	Array of allowed MIME types
	*	@return		void
	**/
	static public function checkMimeType($file_id, $allowed_types)
	{
		// Should use fileinfo
		$handle = finfo_open(FILEINFO_MIME);
		$type = finfo_file($handle, $_FILES[$file_id]['tmp_name']);

		if (!in_array($type, $allowed_types))
		{
			$msg = 'Dit bestandstype is niet toegestaan.';
			throw new Exception($msg);
		}
	}

	/**
	*	Checks the extension of a file. Throws exception if the file
	*	has an extension that is not allowed.
	*
	*	@param		string	$file_id		ID of the file in the $_FILES array
	*	@param		mixed	$valid_ext		Array of allowed extensions
	*	@return		void
	**/
	static public function checkExtension($file_id, $valid_ext)
	{
		// Find extension
		$ext = self::GetExt($_FILES[$file_id]['name']);

		if (!in_array(strtolower($ext), $valid_ext))
		{
			throw new Exception('Dit bestandstype is niet toegestaan. Alleen bestanden met de volgende extensies zijn toegestaan: ' . implode(', ', $valid_ext) . '.');
		}

	}

	/**
	*	Returns the extension of a file.
	*
	*	@param		string	$file_name		File name
	*	@return		string					Extension
	**/
	static public function getExt($file_name)
	{
		// Find extension
		$dot = strrpos($file_name, '.');
		return strtolower(substr($file_name, ($dot + 1)));
	}

	/**
	*	Returns the file name without the extension.
	*
	*	@param		string	$file_name		File name
	*	@return		string					Extension
	**/
	static public function getShortFileName($file_name)
	{
		// Find extension
		$dot = strrpos($file_name, '.');
		return substr($file_name, 0, $dot);
	}

	/**
	*	Moves an uploaded file to the given location/filename.
	*	Throws exception if location doesn't exist/is not writable.
	*	Returns an array with the clean filename + extension.
	*
	*	@param		string	$file_id		ID of the file in the $_FILES array
	*	@param		string	$location		Directory to move file to
	*	@param		string	$postfix		String to add to filename
	*	@return		void
	**/
	static public function moveFile($file_id, $new_file)
	{
		// Check if location exists and is writable
		if (!is_writable(dirname($new_file)))
		{
			throw new Exception('Upload location ' . dirname($new_file) . ' does not exist, or is not writable.');
		}

		// Move file
		$result = move_uploaded_file($_FILES[$file_id]['tmp_name'], $new_file);

		if (!$result)
		{
			throw new Exception('Failed to move file ' . $_FILES[$file_id]['name'] . ' to ' . $new_file);
		}
		chmod($new_file, 0777);

	}

	/**
	*	Copies a file. Throws an exception if the file to copy doesn't exist,
	* 	can't be read, the location for the new file doesn't exist, or isn't
	*	writable.
	*
	*	@param		string	$orig_file		Full path of the file to copy
	*	@param		string	$new_file		Full path of the copy
	*	@return		void
	**/
	static public function copyFile($orig_file, $new_file)
	{
		// Check if original file exists
		if (!is_readable($orig_file))
		{
			throw new Exception('Error: File "' . $orig_file . '" doesn\'t exist or is not readable.');
		}

		// Check if new location exists and is writable
		if (!is_writable(dirname($new_file)))
		{
			throw new Exception('Error: File can\'t be copied to "' . $new_file . '".');
		}

		// Copy file
		$result = copy($orig_file, $new_file);
		if (!$result) {
			throw new Exception('An error occured while copying "' . $orig_file . '" to "' . $new_file . '".');
		}
		chmod($new_file, 0777);

	}

	/**
	*	Reads the contents of two files and compares the MD5 hash.
	*	Throws exception if the files don't exist.
	*
	*	@param		string	$file1		File name
	*	@param		string	$file2		File name
	*	@return		bool				True: MD5 hash is equal
	**/
	static public function compareFiles($file1, $file2)
	{
		if (!file_exists($file1))
		{
			throw new Exception('Compare error: File "' . $file1 . '" does not exist.');
		}

		if (!file_exists($file2))
		{
			throw new Exception('Compare error: File "' . $file2 . '" does not exist.');
		}

		$content1 = file_get_contents($file1);
		$content2 = file_get_contents($file2);
		if (md5($content1) != md5($content2))
		{
			pr(md5($content1));
			prx(md5($content2));
			return false;
		}
		return true;

	}

	/**
	*	Returns a list of all files in a directory (not recursive).
	*
	*	@param		string	$dir		Dir to read
	*	@return		mixed				Array with file names or false for error
	**/
	static public function getFilesInDir($dir)
	{
		// Check dir exists
		if (!file_exists($dir))
		{
			return false;
		}

		// Open dir
		$handle = opendir($dir);
		if ($handle === false)
		{
			return false;
		}

		// Read files
		$list = array();
		while (false !== ($item = readdir($handle)))
		{
			if (substr($item, 0, 1) != '.')
			{
				$list[] = $item;
			}
		}
		closedir($handle);

		return $list;
	}

	/**
	*	Returns base36-encoded time value.
	*
	*	@return		string		Unique identifier.
	**/
	static public function createUniqueId()
	{
		return strtolower(base_convert(time(), 10, 36));
	}

	/**
	*	Removes all variants of the given image (like .._small, .._large)
	*	from the given location.
	*
	*	@param		string	$path	Path to files
	*	@param		string	$file	File to remove
	*	@return		void
	**/
	static public function removeImages($path, $file)
	{
		$filename = self::GetShortFileName($file);
		$ext = self::GetExt($file);

		$vars = array('_small', '_large');
		foreach ($vars as $var)
		{
			@unlink($path . '/' . $filename . $var . '.' .$ext);
		}
	}

	/**
	*	Creates a URL-friendly name.
	*
	*	@param		string	$name	Name to convert
	*	@return		string			Converted name
	**/
	static public function makeUrlFriendlyName($name)
	{
		// lower the string
		$name = strtolower($name);
		// remove all non-alphanumeric chars at begin & end of string
		$name = preg_replace('/^\W+|\W+$/', '', $name);
		// remove all chars except letters a-z, digits, _ and space
		$name = preg_replace("/[^a-z0-9\\_\s]/i", ' ', $name);
		// compress internal whitespace and replace with -:
		$name = preg_replace('/\s+/', '-', $name);
		return $name;
	}

	/**
	 * Downloads an image from the given URL and saves it to the given file.
	 *
	 * @param string	$url	URL
	 * @param string	$file	Full file path
	 */
	static public function downloadImageToFile($url, $file)
	{
		$content = file_get_contents($url);
		if (false === $content)
		{
			throw new Exception('Error while downloading image from ' . $url);
		}
		$result = file_put_contents($file, $content);
		if (false === $result)
		{
			throw new Exception('Error while writing image data to ' . $file);
		}
	}
}