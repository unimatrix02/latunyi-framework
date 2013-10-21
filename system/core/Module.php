<?php
/**
 *	Module class.
 *
 *	@author      Raymond van Velzen <raymond@latunyi.com>
 *	@package     LatunyiFramework
 **/

namespace System\Core;

/**
 * Class for module data.
 */
class Module
{
	/**
	 * Identifier
	 * @var string
	 */
	protected $id;

	/**
	 * Root path
	 * @var string
	 */
	protected $rootPath;

	/**
	 * Namespace for controllers (within \\Application\Controller)
	 * @var string
	 */
	protected $controllerNamespace;

	/**
	 * Path to templates (within /application/templates)
	 * @var string
	 */
	protected $templatePath;

	/**
	 * To allow only requests over SSL.
	 * @var bool
	 */
	protected $secure;

	/**
	 * Username for HTTP basic auth.
	 * @var string
	 */
	protected $username;

	/**
	 * Password for HTTP basic auth.
	 * @var string
	 */
	protected $password;

	/**
	 * Constructor, sets the ID and path properties.
	 *
	 * @param string $id
	 * @param array $data
	 * @throws \Exception
	 */
	public function __construct($id, $data)
	{
		$this->id = $id;
		$this->secure = false;

		$properties = array(
			'root_path' => 'rootPath',
			'controller_ns' => 'controllerNamespace',
			'template_path' => 'templatePath',
			'secure' => 'secure',
			'username' => 'username',
			'password' => 'password',
		);
		foreach($properties as $index => $name)
		{
			if (!array_key_exists($index, $data))
			{
				throw new \Exception("Module $index is missing.");
			}
			$this->$name = (string)$data[$index];
		}
	}

	/**
	 * Returns the ID.
	 *
	 * @return string
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Retursn the root path.
	 *
	 * @return string
	 */
	public function getRootPath()
	{
		return $this->rootPath;
	}

	/**
	 * Returns the controller namespace.
	 *
	 * @return string
	 */
	public function getControllerNamespace()
	{
		return $this->controllerNamespace;
	}

	/**
	 * Returns the template path.
	 *
	 * @return string
	 */
	public function getTemplatePath()
	{
		return $this->templatePath;
	}

	/**
	 * Returns true if the controller namespace is not empty.
	 *
	 * @return bool
	 */
	public function hasControllerNamespace()
	{
		return !empty($this->controllerNamespace);
	}

	/**
	 * Returns true if template path is not empty.
	 *
	 * @return bool
	 */
	public function hasTemplatePath()
	{
		return !empty($this->templatePath);
	}

	/**
	 * Returns true if the module requires secure requests.
	 *
	 * @return bool
	 */
	public function isSecure()
	{
		return $this->secure;
	}

	/**
	 * Returns the username.
	 *
	 * @return string
	 */
	public function getUsername()
	{
		return $this->username;
	}

	/**
	 * Returns the password.
	 *
	 * @return string
	 */
	public function getPassword()
	{
		return $this->password;
	}

	/**
	 * Returns true if username and password are not empty.
	 *
	 * @return bool
	 */
	public function hasLogin()
	{
		return (!empty($this->username) && !empty($this->password));
	}
}