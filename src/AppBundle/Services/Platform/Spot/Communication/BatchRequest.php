<?php

namespace AppBundle\Services\Platform\Spot\Communication;

class BatchRequest implements \IteratorAggregate {
	private $requests = array();

	public function getIterator()
	{
		return new \ArrayIterator($this->requests);
	}

	/**
	 * @param Request $request
	 * @return $this
	 */
	public function add(Request $request)
	{
		$this->requests[] = $request;
		return $this;
	}

	public function reset()
	{
		$this->requests = array();
	}
}