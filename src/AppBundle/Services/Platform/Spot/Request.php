<?php

namespace AppBundle\Services\Platform\Spot;

class Request {
	const COMMAND_VIEW = 'view';
	const COMMAND_ADD = 'add';
	const COMMAND_EDIT = 'edit';

	private $parameters;
	private $model;
	private $command;

	public function __construct($model, $command, array $parameters = array())
	{
		$this->model = $model;
		$this->command = $command;
		$this->parameters = $parameters;
	}

	public function getParameters()
	{
		return $this->parameters;
	}

	/**
	 * @param $parameters
	 * @return $this
	 */
	public function setParameters($parameters)
	{
		$this->parameters =  $parameters;
		return $this;
	}

	public function getCommand()
	{
		return $this->command;
	}

	public function getModel()
	{
		return $this->model;
	}
}
