<?php
/**
 *	Asset manager class.
 *
 *	@author      Raymond van Velzen <raymond@latunyi.com>
 *	@package     LatunyiFramework
 **/

namespace System\Core;

/**
 * The AssetManager takes a list of CSS/JS files,
 * uses their combined names to check for a merged file with their contents,
 * checks if the merged file is still up to date, and
 * if the file doesn't exist or is out of date, merges the files
 * together.
 */
class AssetManager
{
	/**
	 * Asset mgmt configuration
	 * @var \System\Core\DataContainer
	 */
	protected $config;
	
	/**
	 * Response object
	 * @var \System\Core\REs
	 */
	protected $response;
	
	/**
	 * Array with keys for CSS and JS files
	 * @var array
	 */
	protected $files;
	
	/**
	 * Constructor, receives the asset configuration and additional styles and scripts.
	 */
	public function __construct(\System\Core\DataContainer $assetConfig, $styles, $scripts)
	{
		if (!is_array($styles) || !is_array($scripts))
		{
			throw new \Exception('Styles and/or scripts are not arrays');
		}

		$this->files = array();
		$this->files['styles'] = $styles;
		$this->files['scripts'] = $scripts;

		$this->files['remote_styles'] = array();
		$this->files['remote_scripts'] = array();

		$this->config = $assetConfig;
	}
	
	/**
	 * Merges all CSS/JS assets (if enabled in config), and returns an StdClassobject
	 * with keys for each type (styles/scripts), with an array with either all files, 
	 * or a list with a single combined file.
	 * 
	 * $return object 
	 */
	public function mergeAssets()
	{
		$result = new \StdClass();
		$types = array('styles', 'scripts');

		foreach ($types as $type)
		{
			$this->files[$type] = $this->getFileList($type);

			// Move remote files into separate list
			foreach ($this->files[$type] as $index => $file)
			{
				if (substr($file, 0, 2) == '//')
				{
					$this->files['remote_' . $type][] = $file;
					unset($this->files[$type][$index]);
				}
			}

			// If there are files to process...
			if (!empty($this->files[$type]))
			{
				if ($this->config->merging === true)
				{
					$this->files[$type] = array($this->mergeFiles($type));
				}

				$this->addVersionNumbers($this->files[$type], $type);
				$this->addPath($this->files[$type], $type);
			}

			$result->$type = $this->files[$type];

			// Prepend remote files, if any, before local files
			if (!empty($this->files['remote_' . $type]))
			{
				$result->$type = array_merge($this->files['remote_' . $type], $this->files[$type]);
			}
		}

		return $result;
	}

	/**
	 * Combines the list of default files and additional files for the given type.
	 * 
	 * @param string $type
	 * @return array
	 */
	private function getFileList($type)
	{
		// Get list of default files
		$defaultFiles = array();
		if ($this->config->has($type))
		{
			$defaultFiles = $this->config->$type->asArray();
		}

		// Get list of additional files from response
		$additionalFiles = $this->files[$type];
			
		// Merge default and additional files
		$files = array_merge($defaultFiles, $additionalFiles);
		
		return $files;
	}
	
	/**
	 * Takes a list of file names of the given type, prepends the correct dir, 
	 * finds their last modified timestamp, and appends that as a query string parameter 
	 * to the filename.
	 * 
	 * @param string $list List, by reference
	 * @param string $type
	 * @throws \Exception
	 */
	private function addVersionNumbers(&$list, $type)
	{
		// Add timestamp as version number
		$path = WEB_ROOT . constant(strtoupper($type) . '_PATH');
		foreach ($list as &$file)
		{
			$filePath = $path . '/' . $file;
			if (!file_exists($filePath))
			{
				throw new \Exception('Can\'t find file ' . $filePath);
			}
			$mtime = filemtime($filePath);
			$file .= '?v=' . $mtime;
		}
		unset($file);
	}
	
	/**
	 * Adds the path to the CSS/JS file to the filename.
	 * 
	 * @param array $list	List, by reference
	 * @param string $type
	 */
	private function addPath(&$list, $type)
	{
		$path = PUBLIC_ROOT . constant(strtoupper($type) . '_PATH');
		foreach ($list as &$file)
		{
			$file = $path . '/' . $file;
		}
		unset($file);
	}
	
	/**
	 * Takes the loaded list of styles, concatenates their file names,
	 * makes a hash from that string, checks for a CSS file with that name;
	 * If exists, compare timestamp of combined file with timestamps of
	 * each of the input files. If not existing or out of date, the contents
	 * of all files are combined into one file, named with the hash.
	 *
	 * @param string $type
	 * @return string
	 * @throws \Exception
	 */
	private function mergeFiles($type)
	{
		$hash = md5(implode(';', $this->files[$type]));
		$combinedFileName = $hash . '.' . ($type == 'styles' ? 'css' : 'js');
		
		// Check file already exists
		$normalPath = WEB_ROOT . constant(strtoupper($type) . '_PATH');
		$shortCombinedFileName = $combinedFileName;
		$combinedFileName = $normalPath . '/' . $combinedFileName;
		
		$makeNew = true;
		if (file_exists($combinedFileName))
		{
			if ($this->config->has('autorefresh') && $this->config->autorefresh === true)
			{
				// Get modified timestamp
				$combinedMtime = filemtime($combinedFileName);
				
				// Compare with modified timestamps of each file
				$makeNew = false;
				foreach ($this->files[$type] as $sourceFile)
				{
					$sourceMtime = filemtime($normalPath . '/' . $sourceFile);
					if ($sourceMtime > $combinedMtime)
					{
						$makeNew = true;
						break;
					}
				}
			}
			else
			{
				$makeNew = false;
			}
		}
		
		if ($makeNew)
		{
			// Get contents of each file
			$content = '';
			foreach ($this->files[$type] as $sourceFile)
			{
				$content .= '/* ***** ' . $sourceFile . ' ***** */' . "\n";
				$content .= file_get_contents($normalPath . '/' . $sourceFile);
				$content .= "\n";
			}
			
			// Write to combined file
			file_put_contents($combinedFileName, $content);

			// Check file exists
			if (!file_exists($combinedFileName))
			{
				throw new \Exception('AssetManager: Can\'t find combined file ' . $combinedFileName);
			}
			
			if ($this->config->minify === true)
			{
				$this->minifyFiles(array($shortCombinedFileName), $type);
			}
		}
		
		return $shortCombinedFileName;
	}
	
	/**
	 * Minifies the given list of files of the given type.
	 * The commands for the minifier are taken from the config. 
	 * 
	 * @param array $files
	 * @param string $type
	 * @throws \Exception
	 */
	private function minifyFiles($files, $type)
	{
		if (!is_array($files))
		{
			throw new \Exception('AssetManager: Files is not an array.');
		}
		foreach ($files as $file)
		{
			$path = WEB_ROOT . constant(strtoupper($type) . '_PATH');
			$file = $path . '/' . $file;
			$cmd = str_replace('[filename]', $file, $this->config->minify_commands->$type);
			$result = shell_exec($cmd);
		}
	}
	
}
