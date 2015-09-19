<?php

namespace AppBundle\Services\Platform\Spot\Communication;

class ResponseException extends \Exception
{
	private $response;
	public function __construct($message = "", $response="", $code = 0, \Exception $previous = null)
	{
		$this->response = $response;
		parent::__construct($message, $code, $previous);
	}

	public function getResponse()
	{
		return $this->response;
	}
}
