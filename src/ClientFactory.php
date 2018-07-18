<?php declare(strict_types = 1);

namespace Contributte\Guzzlette;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;

class ClientFactory
{

	public const FORCE_REQUEST_COLLECTION = true;

	/** @var SnapshotStack */
	private $snapshotStack;

	/** @var bool */
	private $debug;

	public function __construct(SnapshotStack $snapshotStack, bool $debug = false)
	{
		$this->snapshotStack = $snapshotStack;
		$this->debug = $debug;
	}

	/**
	 * @param mixed[] $config
	 */
	public function createClient(array $config = []): Client
	{
		if ($this->debug) {
			$handler = $this->createHandlerStack($config['handler'] ?? null);
			$config['handler'] = $handler;
		}

		return new Client($config);
	}

	private function createHandlerStack(?HandlerStack $handlerStack = null): HandlerStack
	{
		if ($handlerStack === null) {
			$handlerStack = HandlerStack::create();
		}

		$handler = new GuzzleHandler($this->snapshotStack);
		$handlerStack->push($handler);

		return $handlerStack;
	}

}
