<?php
namespace System\Core;

/**
 * Class for loading of configuration data.
 */
class Config
{
	/**
	 * List of configuration file data.
	 * @var array
	 */
	private $data;
	
	public function __construct()
	{
		$this->data = array();
		
		// Load the core config file
		$this->loadFile('core', SYSTEM_PATH . '/config/routing');
		
		// Load the application config file
		
		// Load the application action config file
	}

	/**
	 * Loads the contents of the given configuration file
	 * and stores it in the $data property under the given name.
	 * First looks for a PHP include file, if not available, 
	 * looks for a YAML file, loads the data, and dumps it 
	 * as a PHP include file.
	 * 
	 * @param string  $name
	 * @param string  $path		Path to file, without extension
	 */
	private function loadFile($name, $path)
	{
		$phpInclude = $path . '.inc.php';
		$yamlFile = $path . '.yml';
		
		// Look for PHP include first
		$createNewPhpInclude = true;
		if (file_exists($phpInclude))
		{
			// Check PHP include is newer than YAML file
			$includeMtime = filemtime($phpInclude);
			
			// Check YAML file exists
			if (!file_exists($yamlFile))
			{
				throw new Exception('Failed to find YAML config file ' . $yamlFile);
			}
			
			$yamlMtime = filemtime($yamlFile);

			// Use PHP include if newer than YAML
			if ($includeMtime > $yamlMtime)
			{
				$createNewPhpInclude = false;
			}
		}
		
		if ($createNewPhpInclude)
		{
			// Check YAML file exists
			if (!file_exists($yamlFile))
			{
				throw new Exception('Failed to find YAML config file ' . $yamlFile);
			}

			$data = yaml_parse_file($yamlFile);
			if (false == $data)
			{
				throw new Exception('Failed to read YAML config file ' . $yamlFile);
			}
			
			$result = file_put_contents($phpInclude, '<?php $config = ' . var_export($data, true) . ';');
		}

		require($phpInclude);
		
		// Check for $config
		if (!isset($config))
		{
			$createNewPhpInclude = true;
			throw new Exception('Local variable $config was not created by configuration file');
		}
		
		$this->data[$name] = $config;
	}
	
}
