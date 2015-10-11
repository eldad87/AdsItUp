<?php

namespace AppBundle\Services\Platform\Pixel;

class PixelSetting {
	const ORIGIN_TYPE_CLIENT = 1;
	const ORIGIN_TYPE_SERVER = 2;
	const ORIGIN_TYPE_CLI = 3;

	const DESTINATION_TYPE_CLIENT = 1;
	const DESTINATION_TYPE_SERVER = 2;

	const ACTION_GET = 1;
	const ACTION_POST = 2;

	const EVENT_LEAD = 1;
	const EVENT_CUSTOMER = 2;
	const EVENT_DEPOSIT = 3;
	const EVENT_GAME = 4;

	const FIRE_CONDITION_ANY = 1;
	const FIRE_CONDITION_MATCH_A_COMMISSION_PLAN = 2;
	const FIRE_CONDITION_ON_QUALIFICATION = 3;

	/**
	 * @Assert\NotBlank()
	 * @Assert\Choice(choices = {"1", "2"})
	 *
	 * @ORM\Column(type="integer", options={"default" = 1})
	 *
	 * @GRID\Column(title="Destination Type", operatorsVisible=false, filter="select", selectFrom="values", values={"1"="Client","2"="Server"})
	 */
	protected $destinationType;

	/**
	 * @Assert\NotBlank()
	 * @Assert\Choice(choices = {"1", "2"})
	 *
	 * @ORM\Column(type="integer", options={"default" = 1})
	 *
	 * @GRID\Column(title="Action", operatorsVisible=false, filter="select", selectFrom="values", values={"1"="GET","2"="POST"})
	 */
	protected $action;

	/**
	 * @Assert\NotBlank()
	 * @Assert\Url()
	 *
	 * @ORM\Column(type="text")
	 *
	 * @GRID\Column(title="URL", type="text", operatorsVisible=false)
	 */
	protected $url;

	/**
	 * @Assert\NotBlank()
	 * @Assert\Choice(choices = {"1", "2", "3"})
	 *
	 * @ORM\Column(type="integer", options={"default" = 1})
	 *
	 * @GRID\Column(title="Type", operatorsVisible=false, filter="select", selectFrom="values", values={"1"="Any","2"="On Qualification","3"="Match a Commission Plan"})
	 */
	protected $fireCondition;

	/**
	 * @return mixed
	 */
	public function getDestinationType()
	{
		return $this->destinationType;
	}

	/**
	 * @param mixed $destinationType
	 * @return PixelSetting
	 */
	public function setDestinationType($destinationType)
	{
		$this->destinationType = $destinationType;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getAction()
	{
		return $this->action;
	}

	/**
	 * @param mixed $action
	 * @return PixelSetting
	 */
	public function setAction($action)
	{
		$this->action = $action;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getUrl()
	{
		return $this->url;
	}

	/**
	 * @param mixed $url
	 * @return PixelSetting
	 */
	public function setUrl($url)
	{
		$this->url = $url;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getFireCondition()
	{
		return $this->fireCondition;
	}

	/**
	 * @param mixed $fireCondition
	 * @return PixelSetting
	 */
	public function setFireCondition($fireCondition)
	{
		$this->fireCondition = $fireCondition;
		return $this;
	}


}