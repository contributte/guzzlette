<?php declare(strict_types = 1);

namespace Contributte\Guzzlette\DI;

use Contributte\Guzzlette\ClientFactory;
use Contributte\Guzzlette\SnapshotStack;
use GuzzleHttp\Client;
use GuzzleHttp\MessageFormatter;
use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\ServiceDefinition;
use Nette\DI\Definitions\Statement;
use Nette\PhpGenerator\ClassType;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Psr\Log\NullLogger;
use stdClass;

/**
 * @property-read stdClass $config
 */
class GuzzleExtension extends CompilerExtension
{

	public function getConfigSchema(): Schema
	{
		$expectService = Expect::anyOf(
			Expect::string()->required()->assert(static fn (mixed $input): bool => is_string($input) && (str_starts_with($input, '@') || class_exists($input) || interface_exists($input))),
			Expect::type(Statement::class)->required(),
		);

		return Expect::structure([
			'debug' => Expect::bool(false),
			'preset' => Expect::anyOf(null, 'default')->default('default'),
			'app' => Expect::string()->nullable()->default(null),
			'logger' => Expect::structure([
				'level' => Expect::string(LogLevel::INFO),
				'formatter' => Expect::anyOf(
					clone $expectService,
					Expect::string()->required(),
				)->nullable()->default(null),
				'logger' => clone $expectService,
			]),
			'tracy' => Expect::bool(false),
			'client' => Expect::array()->dynamic()->default([
				'timeout' => 30,
			]),
		]);
	}

	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();
		$config = $this->config;

		$builder->addDefinition($this->prefix('snapshotStack'))
			->setType(SnapshotStack::class)
			->setAutowired(false);

		$builder->addDefinition($this->prefix('clientFactory'))
			->setType(ClientFactory::class)
			->setArguments([$builder->getDefinition($this->prefix('snapshotStack')), $config->debug || $config->tracy]);

		$factoryDef = $builder->getDefinition($this->prefix('clientFactory'));
		assert($factoryDef instanceof ServiceDefinition);

		if ($config->app !== null) {
			$factoryDef->addSetup('withUserAgent', [$config->app]);
		}

		if ($config->logger->formatter !== null) {
			if (is_string($config->logger->formatter)) {
				$factoryDef->addSetup('setFormatter', [new Statement(MessageFormatter::class, [$config->logger->formatter])]);
			} else {
				$factoryDef->addSetup('setFormatter', [$config->logger->formatter]);
			}
		}

		if ($config->logger->logger !== null) {
			$factoryDef->addSetup('setLogger', [is_string($config->logger->logger) ? new Statement($config->logger->logger) : $config->logger->logger]);
		}

		$builder->addDefinition($this->prefix('client'))
			->setType(Client::class)
			->setFactory('@' . $this->prefix('clientFactory') . '::createClient', ['config' => $config->client]);
	}

	public function beforeCompile(): void
	{
		$config = $this->config;
		$builder = $this->getContainerBuilder();

		$factoryDef = $builder->getDefinition($this->prefix('clientFactory'));
		assert($factoryDef instanceof ServiceDefinition);

		if ($config->logger->logger === null) {
			$loggerDef = $builder->getByType(LoggerInterface::class);

			if ($loggerDef !== null) {
				$factoryDef->addSetup('setLogger', [$builder->getDefinition($loggerDef)]);
			} elseif ($config->preset === 'default') {
				$factoryDef->addSetup('setLogger', [new Statement(NullLogger::class)]);
			}
		}

		if ($config->preset === 'default') {
			$factoryDef->addSetup('withDefaults');
		}
	}

	public function afterCompile(ClassType $class): void
	{
		$config = $this->config;

		if ($config->debug || $config->tracy) {
			$initialize = $class->getMethod('initialize');
			$initialize->addBody(
				'$this->getService(?)->addPanel(new \Contributte\Guzzlette\Tracy\Panel($this->getService(?)));',
				['tracy.bar', $this->prefix('snapshotStack')]
			);
		}
	}

}
