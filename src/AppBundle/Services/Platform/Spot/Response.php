<?php

namespace AppBundle\Services\Platform\Spot;

class response {
	private $success;
	private $data;
	private $message;

	public function __construct($success, array $data=array(), $message=null)
	{
		$this->success = $success;
		$this->data = $data;
		$this->message = $message;
	}

	/**
	 * @return boolean
	 */
	public function getSuccess()
	{
		return $this->success;
	}

	/**
	 * @return array
	 */
	public function getData()
	{
		return $this->data;
	}

	/**
	 * @return string
	 */
	public function getMessage()
	{
		return $this->message;
	}
}