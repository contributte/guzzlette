<?php

namespace Matyx\Guzzlette;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../../bootstrap.php';

/**
 * @testCase
 */
class TracyPanelTest extends TestCase {

	public function testTracyPanel() {
		$guzzlette = new \Matyx\Guzzlette\Guzzlette(\Matyx\Guzzlette\Guzzlette::FORCE_REQUEST_COLLECTION);

		$mock = new MockHandler([
			new Response(200, ['X-Foo' => 'Bar', 'Content-Type' => 'application/json'], '{"status": "ok"}'),
			new Response(200, ['X-Foo' => 'Bar', 'Content-Type' => 'text/plain'], 'ok'),
		]);

		$handler = HandlerStack::create($mock);

		$client = $guzzlette->createGuzzleClient(['handler' => $handler]);

		$stack = $guzzlette->getRequestStack();

		$panel = new TracyPanel($stack);
		Assert::same(false, $panel->getTab());

		$client->request('GET', '/');
		$client->request('GET', '/');

		Assert::same(2,count($stack->getRequests()));
		Assert::notSame(false, $panel->getTab());

		// Is panel rendering OK?
		$panel->getPanel();
	}
}


(new TracyPanelTest())->run();
