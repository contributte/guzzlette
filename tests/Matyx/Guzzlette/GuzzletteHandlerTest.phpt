<?php

namespace Matyx\Guzzlette;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Matyx\Guzzlette\ClientFactory;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../../bootstrap.php';

/**
 * @testCase
 */
class GuzzletteHandlerTest extends TestCase {

	public function testHandler() {
		$requestStack = new RequestStack();
		$guzzlette = new ClientFactory($requestStack);

		$mock = new MockHandler([
			new Response(200, ['X-Foo' => 'Bar']),
			new Response(202, ['Content-Length' => 0]),
			new RequestException("Error Communicating with Server", new \GuzzleHttp\Psr7\Request('GET', 'test'))
		]);

		$handler = HandlerStack::create($mock);

		$client = $guzzlette->createClient(['handler' => $handler]);

		// The first request is intercepted with the first response.
		Assert::same(200, $client->request('GET', '/')->getStatusCode());
		Assert::same(202, $client->request('GET', '/')->getStatusCode());

		Assert::same(2, count($requestStack->getRequests()));
		Assert::notSame(0.0, $requestStack->getTotalTime());
	}
}


(new GuzzletteHandlerTest())->run();
