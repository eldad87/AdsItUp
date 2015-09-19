<?php

namespace AppBundle\Services\Platform\Spot\Communication;

class BatchResponse implements \IteratorAggregate {
	private $responses = array();

	public function getIterator()
	{
		return new \ArrayIterator($this->responses);
	}

	/**
	 * @param Response $response
	 * @return $this
	 */
	public function add(Response $response, $offset)
	{
		$this->responses[$offset] = $response;
		return $this;
	}

	public function reset()
	{
		$this->responses = array();
	}
}