<?php

namespace Matyx\Guzzlette;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Matyx\Guzzlette\Tracy\Panel;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../../bootstrap.php';

/**
 * @TestCase
 */
class TracyPanelTest extends TestCase
{
	public function testTracyPanel()
	{
		$requestStack = new RequestStack();
		$guzzlette = new \Matyx\Guzzlette\ClientFactory($requestStack, true);

		$mock = new MockHandler([
			new Response(200, ['X-Foo' => 'Bar', 'Content-Type' => 'application/json'], '{"status": "ok"}'),
			new Response(200, ['X-Foo' => 'Bar', 'Content-Type' => 'text/plain'], 'ok'),
		]);

		$handler = HandlerStack::create($mock);

		$client = $guzzlette->createClient(['handler' => $handler]);

		$panel = new Panel($requestStack);
		Assert::same(false, $panel->getTab());

		$client->request('GET', '/');
		$client->request('GET', '/');

		Assert::same(2, count($requestStack->getRequests()));
		Assert::notSame(false, $panel->getTab());

		// Is panel rendering OK?
		$panel->getPanel();
	}
}


(new TracyPanelTest())->run();
