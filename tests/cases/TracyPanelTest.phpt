<?php declare(strict_types = 1);

namespace Tests\Contributte\Guzzlette;

use Contributte\Guzzlette\ClientFactory;
use Contributte\Guzzlette\SnapshotStack;
use Contributte\Guzzlette\Tracy\Panel;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../bootstrap.php';

/**
 * @TestCase
 */
class TracyPanelTest extends TestCase
{

	public function testTracyPanel(): void
	{
		$snapshotStack = new SnapshotStack();
		$factory = new ClientFactory($snapshotStack, true);

		$mock = new MockHandler([
			new Response(200, ['X-Foo' => 'Bar', 'Content-Type' => 'application/json'], '{"status": "ok"}'),
			new Response(200, ['X-Foo' => 'Bar', 'Content-Type' => 'text/plain'], 'ok'),
		]);

		$handler = HandlerStack::create($mock);

		$client = $factory->createClient(['handler' => $handler]);

		$panel = new Panel($snapshotStack);
		Assert::same('', $panel->getTab());

		$client->request('GET', '/');
		$client->request('GET', '/');

		Assert::same(2, count($snapshotStack->getSnapshots()));
		Assert::notSame('', $panel->getTab());

		// Is panel rendering OK?
		$panel->getPanel();
	}

}


(new TracyPanelTest())->run();
