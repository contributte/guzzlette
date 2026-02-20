<?php declare(strict_types = 1);

namespace Contributte\Guzzlette;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;

class GuzzleBuilder
{

	private HandlerStack $stack;

	/** @var array<string, mixed> */
	private array $options = [];

	public function __construct(HandlerStack $stack)
	{
		$this->stack = $stack;
	}

	public function with(callable $middleware, string $name): self
	{
		$this->stack->push($middleware, $name);

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
	 * @param array<string, mixed> $options
	 */
	public function build(array $options = []): Client
	{
		return new Client(array_merge(
			$this->options,
			$options,
			['handler' => $this->stack],
		));
	}

}
