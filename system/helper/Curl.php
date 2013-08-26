<?php
namespace System\Helper;

/**
 *	Wrapper class for working with Curl.
 */
class Curl
{
	/**
	*	@var	Handle for curl resource 
	**/
	private	$handle;

	/**
	*	@var	List of cookies
	**/
	private	$cookies = array();

	/**
	*	Constructor
	*
	*	@param		string		$url		URL to connect to
	*	@param		string		$method		HTTP method, default GET
	*	@return		void
	**/
    public function __construct($url, $method = 'get') 
    {
    	$this->handle = curl_init();
    	
    	// Set URL
    	curl_setopt($this->handle, CURLOPT_URL, $url);
    	
    	// Don't print output directly
    	curl_setopt($this->handle, CURLOPT_RETURNTRANSFER, true);
    	
    	// Set method
		if ($method == 'post')
		{
	    	curl_setopt($this->handle, CURLOPT_POST, true);
    	}
	}

	// -------------------------------------------------------------------

	/**
	*	SetPostData
	*
	*	Adds data for a POST request.
	*
	*	@param		mixed	$data		Data to POST
	*	@return		void
	**/
    public function setPostData($data) 
    {
    	curl_setopt($this->handle, CURLOPT_POSTFIELDS, $data);
    }
	// -------------------------------------------------------------------

	/**
	*	SetLogin
	*
	*	Adds username/password for HTTP authentication.
	*
	*	@param		string		$username
	*	@param		string		$password
	*	@return		void
	**/
    public function setLogin($username, $password) 
    {
    	curl_setopt($this->handle, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
    	curl_setopt($this->handle, CURLOPT_USERPWD, $username . ':' . $password);
    }

	// -------------------------------------------------------------------

	/**
	*	SetCookie
	*
	*	Adds a cookie to the request.
	*
	*	@param		string		$name
	*	@param		string		$value
	*	@return		void
	**/
    public function setCookie($name, $value) 
    {
    	$this->cookies[] = $name . '=' . $value;
    }
    
	// -------------------------------------------------------------------

	/**
	*	Execute
    *
    *	Executes the request and returns the result.
    *
	*	@return		string		Request result
	**/
    public function execute() 
    {
    	// Set cookies, if necessary
    	if (!empty($this->cookies))
    	{
    		curl_setopt($this->handle, CURLOPT_COOKIE, implode('; ', $this->cookies));
    	}
    
    	$result = curl_exec($this->handle);
    	if ($result === false)
    	{
    		return 'Error ' . curl_errno($this->handle) . ': ' . curl_error($this->handle);
    	}
    	return $result;
    }
    
	// -------------------------------------------------------------------

	/**
	*	Destructor
    *
    *	Closes the resource.
    *
	*	@return		void
	**/
    public function __destruct()
    {
    	curl_close($this->handle);
    }
}	

?>