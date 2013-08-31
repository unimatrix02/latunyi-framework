<?php
namespace Application\Domain\Entity;

class Item extends \System\Core\Entity
{
	public $id = 0;
	public $name;
	public $value;
	public $typeId;
	public $startDate;
	public $endDate;
}
