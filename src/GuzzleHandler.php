<?php declare(strict_types = 1);

namespace Contributte\Guzzlette;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class GuzzleHandler
{

	private SnapshotStack $snapshotStack;

	public function __construct(SnapshotStack $snapshotStack)
	{
		$this->snapshotStack = $snapshotStack;
	}

	public function __invoke(callable $nextHandler): callable
	{
		return function (RequestInterface $request, array $options) use ($nextHandler) {
			$startTime = microtime(true);

			return $nextHandler($request, $options)->then(function (ResponseInterface $response) use ($startTime, $request): ResponseInterface {
				$this->snapshotStack->addSnapshot(new Snapshot(
					$request,
					$response,
					microtime(true) - $startTime
				));

				return $response;
			});
		};
	}

}
