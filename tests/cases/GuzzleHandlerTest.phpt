<?php declare(strict_types = 1);

namespace Tests\Contributte\Guzzlette;

use Contributte\Guzzlette\ClientFactory;
use Contributte\Guzzlette\SnapshotStack;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../bootstrap.php';

/**
 * @TestCase
 */
class GuzzleHandlerTest extends TestCase
{

	public function testHandlerDebug(): void
	{
		$snapshotStack = new SnapshotStack();
		$factory = new ClientFactory($snapshotStack, true);

		$mock = new MockHandler([
			new Response(200, ['X-Foo' => 'Bar']),
			new Response(202, ['Content-Length' => 0]),
			new RequestException('Error Communicating with Server', new Request('GET', 'test')),
		]);

		$handler = HandlerStack::create($mock);

		$client = $factory->createClient(['handler' => $handler]);

		// The first request is intercepted with the first response.
		Assert::same(200, $client->request('GET', '/')->getStatusCode());
		Assert::same(202, $client->request('GET', '/')->getStatusCode());

		Assert::same(2, count($snapshotStack->getSnapshots()));
		Assert::notSame(0.0, $snapshotStack->getTotalTime());
	}


	public function testHandlerProduction(): void
	{
		$snapshotStack = new SnapshotStack();
		$factory = new ClientFactory($snapshotStack);

		$mock = new MockHandler([
			new Response(200, ['X-Foo' => 'Bar']),
			new Response(202, ['Content-Length' => 0]),
			new RequestException('Error Communicating with Server', new Request('GET', 'test')),
		]);

		$handler = HandlerStack::create($mock);

		$client = $factory->createClient(['handler' => $handler]);

		Assert::same(200, $client->request('GET', '/')->getStatusCode());
		Assert::same(202, $client->request('GET', '/')->getStatusCode());

		Assert::same(0, count($snapshotStack->getSnapshots()));
		Assert::same(0.0, $snapshotStack->getTotalTime());
	}

}


(new GuzzleHandlerTest())->run();
