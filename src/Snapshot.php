<?php declare(strict_types = 1);

namespace Contributte\Guzzlette;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Snapshot
{

	/** @var RequestInterface */
	private $request;

	/** @var ResponseInterface */
	private $response;

	/** @var float */
	private $time;

	public function __construct(RequestInterface $request, ResponseInterface $response, float $time)
	{
		$this->request = $request;
		$this->response = $response;
		$this->time = $time;
	}

	public function getRequest(): RequestInterface
	{
		return $this->request;
	}

	public function getResponse(): ResponseInterface
	{
		return $this->response;
	}

	public function getTime(): float
	{
		return $this->time;
	}

}
