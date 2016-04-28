<?php

namespace Matyx\Guzzlette;

class Request {
	/** @var  \GuzzleHttp\Psr7\Request */
	public $request;

	/** @var  \GuzzleHttp\Psr7\Response */
	public $response;

	/** @var  float */
	public $time;
}