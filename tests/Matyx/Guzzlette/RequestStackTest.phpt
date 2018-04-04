<?php

namespace Matyx\Guzzlette;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../../bootstrap.php';

/**
 * @TestCase
 */
class RequestStackTest extends TestCase
{
	public function testStack()
	{
		$requestMock = \Mockery::mock(RequestInterface::class);
		$responseMock = \Mockery::mock(ResponseInterface::class);

		$request = new Request($requestMock, $responseMock, 10);

		$stack = new RequestStack();
		$stack->addRequest($request);
		$stack->addRequest($request);
		$stack->addRequest($request);
		$stack->addRequest($request);

		Assert::equal(40, $stack->getTotalTime());
		Assert::equal(4, count($stack->getRequests()));
	}
}


(new RequestStackTest())->run();
