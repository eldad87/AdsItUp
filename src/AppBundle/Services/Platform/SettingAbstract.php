<?php
namespace AppBundle\Services\Platform;

use Symfony\Component\Validator\Constraints as Assert;

abstract class SettingAbstract {

	/**
	 * @Assert\NotBlank()
	 * @Assert\EqualTo(value = "Spot")
	 * @var string
	 */
	protected $type;

	public function __construct(array $setting)
	{
		//Apply given array to current object properties
		foreach($setting AS $key=>$val) {
			if(property_exists($this, $key)) {
				$this->{$key} = $val;
			}
		}
	}

	/**
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}
}