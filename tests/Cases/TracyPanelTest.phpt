<?php declare(strict_types = 1);

namespace Tests\Cases;

use Contributte\Guzzlette\ClientFactory;
use Contributte\Guzzlette\Snapshot;
use Contributte\Guzzlette\SnapshotStack;
use Contributte\Guzzlette\Tracy\Panel;
use Contributte\Tester\Toolkit;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

Toolkit::test(function (): void {
	$snapshotStack = new SnapshotStack();
	$factory = new ClientFactory($snapshotStack, true);

	$mock = new MockHandler([
		new Response(200, ['X-Foo' => 'Bar', 'Content-Type' => 'application/json'], '{"status": "ok"}'),
		new Response(200, ['X-Foo' => 'Bar', 'Content-Type' => 'text/plain'], 'ok'),
	]);

	$handler = HandlerStack::create($mock);

	$client = $factory->createClient(['handler' => $handler]);

	$panel = new Panel($snapshotStack);
	Assert::contains('fill="#aaa"', $panel->getTab());
	Assert::equal('', $panel->getPanel());

	$client->request('GET', '/');
	$client->request('GET', '/');

	Assert::same(2, count($snapshotStack->getSnapshots()));
	Assert::contains('fill="#6bcbfd"', $panel->getTab());

	$snapshot = $snapshotStack->getSnapshots()[1];
	Assert::type(Snapshot::class, $snapshot);
	Assert::type(RequestInterface::class, $snapshot->getRequest());
	Assert::type(ResponseInterface::class, $snapshot->getResponse());

	// Is panel rendering OK?
	$panel->getPanel();
});
