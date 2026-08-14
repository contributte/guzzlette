<?php declare(strict_types = 1);

namespace Contributte\Guzzlette;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\MessageFormatterInterface;
use GuzzleHttp\Middleware;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class ClientFactory
{

	public const FORCE_REQUEST_COLLECTION = true;

	/** @var array<string, callable> */
	private array $stackFns = [];

	/** @var array<string, mixed> */
	private array $options = [];

	private SnapshotStack $snapshotStack;

	private bool $debug;

	private ?LoggerInterface $logger = null;

	private ?MessageFormatterInterface $formatter = null;

	/** @var HandlerStack<callable(RequestInterface, array<array-key, mixed>): PromiseInterface<ResponseInterface, mixed>>|null */
	private ?HandlerStack $handlerStack = null;

	public function __construct(SnapshotStack $snapshotStack, bool $debug = false)
	{
		$this->snapshotStack = $snapshotStack;
		$this->debug = $debug;
	}

	public function setFormatter(MessageFormatterInterface $formatter): void
	{
		$this->formatter = $formatter;
	}

	public function setLogger(LoggerInterface $logger): void
	{
		$this->logger = $logger;
	}

	/**
	 * @param HandlerStack<callable(RequestInterface, array<array-key, mixed>): PromiseInterface<ResponseInterface, mixed>> $handlerStack
	 */
	public function setHandlerStack(HandlerStack $handlerStack): void
	{
		$this->handlerStack = $handlerStack;
	}

	public function withDefaults(): self
	{
		return $this->withTracy()->withLog();
	}

	public function withTracy(): self
	{
		if ($this->debug) {
			$this->with(new GuzzleHandler($this->snapshotStack), 'tracy');
		}

		return $this;
	}

	/**
	 * @phpstan-param LogLevel::* $level
	 */
	public function withLog(?LoggerInterface $logger = null, ?MessageFormatterInterface $formatter = null, string $level = LogLevel::DEBUG): self
	{
		if ($this->logger !== null || $logger !== null) {
			$resolvedLogger = $logger ?? $this->logger;
			assert($resolvedLogger !== null);

			return $this->with(
				Middleware::log(
					$resolvedLogger,
					$formatter ?? $this->formatter ?? new MessageFormatter(),
					$level,
				),
				'logger',
			);
		}

		return $this;
	}

	public function with(callable $middleware, string $name): self
	{
		$this->stackFns[$name] = $middleware;

		return $this;
	}

	public function withConfigOption(string $name, mixed $value): self
	{
		$this->options[$name] = $value;

		return $this;
	}

	/**
	 * @param array<string, string> $headers
	 */
	public function withHeaders(array $headers): self
	{
		$this->options['headers'] = array_merge(
			$this->options['headers'] ?? [], // @phpstan-ignore-line
			$headers,
		);

		return $this;
	}

	public function withUserAgent(string $agent): self
	{
		return $this->withHeaders(['User-Agent' => $agent]);
	}

	public function withBaseUri(string $url): self
	{
		return $this->withConfigOption('base_uri', $url);
	}

	public function withHttpAuth(string $user, string $password): self
	{
		return $this->withConfigOption('auth', [$user, $password]);
	}

	/**
	 * @param HandlerStack<callable(RequestInterface, array<array-key, mixed>): PromiseInterface<ResponseInterface, mixed>>|null $handlerStack
	 */
	public function create(?HandlerStack $handlerStack = null): GuzzleBuilder
	{
		$stack = $handlerStack ?? $this->handlerStack ?? HandlerStack::create();

		foreach ($this->stackFns as $name => $fn) {
			$stack->push($fn, $name);
		}

		if ($this->debug && !isset($this->stackFns['tracy'])) {
			$stack->push(new GuzzleHandler($this->snapshotStack), 'tracy');
		}

		$builder = new GuzzleBuilder($stack);

		foreach ($this->options as $name => $value) {
			$builder->withConfigOption($name, $value);
		}

		return $builder;
	}

	/**
	 * @param mixed[] $config
	 */
	public function createClient(array $config = []): Client
	{
		$handlerStack = null;

		if (isset($config['handler']) && $config['handler'] instanceof HandlerStack) {
			$handlerStack = $config['handler'];
			unset($config['handler']);
		}

		return $this->create($handlerStack)->build($config);
	}

}
