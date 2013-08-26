<?php
namespace Application\Controller;

class Test extends \System\Core\BaseController
{
	public function showHomepage()
	{
		$this->foo = 'bar';
		$this->result = 'yeahhh!';
	}
}

?>