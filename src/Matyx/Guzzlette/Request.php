<?php

namespace Matyx\Guzzlette;

use Psr\Http\Message\RequestInterface;

class Request
{
	/** @var  RequestInterface */
	private $request;

	/** @var  \Psr\Http\Message\ResponseInterface */
	private $response;

	/** @var  float */
	private $time;


	/**
	 * Request constructor.
	 *
	 * @param \Psr\Http\Message\RequestInterface $request
	 * @param \Psr\Http\Message\ResponseInterface $response
	 * @param float $time
	 */
	public function __construct(\Psr\Http\Message\RequestInterface $request, \Psr\Http\Message\ResponseInterface $response, $time)
	{
		$this->request = $request;
		$this->response = $response;
		$this->time = $time;
	}


	/**
	 * @return \Psr\Http\Message\RequestInterface
	 */
	public function getRequest()
	{
		return $this->request;
	}


	/**
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	public function getResponse()
	{
		return $this->response;
	}


	/**
	 * @return float
	 */
	public function getTime()
	{
		return $this->time;
	}
}
