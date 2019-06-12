<?php declare(strict_types = 1);

namespace Contributte\Guzzlette\DI;

use Contributte\Guzzlette\ClientFactory;
use Contributte\Guzzlette\SnapshotStack;
use GuzzleHttp\Client;
use Nette\DI\CompilerExtension;
use Nette\PhpGenerator\ClassType;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use stdClass;

/**
 * @property-read stdClass $config
 */
class GuzzleExtension extends CompilerExtension
{

	public function getConfigSchema(): Schema
	{
		return Expect::structure([
			'debug' => Expect::bool(false),
			'client' => Expect::array()->default([
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
			->setArguments([$builder->getDefinition($this->prefix('snapshotStack')), $config->debug]);

		$builder->addDefinition($this->prefix('client'))
			->setType(Client::class)
			->setFactory('@' . $this->prefix('clientFactory') . '::createClient', ['config' => $config->client]);
	}

	public function afterCompile(ClassType $class): void
	{
		$config = $this->config;

		if ($config->debug) {
			$initialize = $class->getMethod('initialize');
			$initialize->addBody(
				'$this->getService(?)->addPanel(new \Contributte\Guzzlette\Tracy\Panel($this->getService(?)));',
				['tracy.bar', $this->prefix('snapshotStack')]
			);
		}
	}

}
