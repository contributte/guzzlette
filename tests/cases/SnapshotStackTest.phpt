<?php declare(strict_types = 1);

namespace Tests\Contributte\Guzzlette;

use Contributte\Guzzlette\Snapshot;
use Contributte\Guzzlette\SnapshotStack;
use Mockery;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../bootstrap.php';

/**
 * @TestCase
 */
class SnapshotStackTest extends TestCase
{

	public function testStack(): void
	{
		$requestMock = Mockery::mock(RequestInterface::class);
		$responseMock = Mockery::mock(ResponseInterface::class);

		$snapshot = new Snapshot($requestMock, $responseMock, 10);

		$stack = new SnapshotStack();
		$stack->addSnapshot($snapshot);
		$stack->addSnapshot($snapshot);
		$stack->addSnapshot($snapshot);
		$stack->addSnapshot($snapshot);

		Assert::equal(40.0, $stack->getTotalTime());
		Assert::equal(4, count($stack->getSnapshots()));
	}

}


(new SnapshotStackTest())->run();
