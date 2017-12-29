<?php

namespace Matyx\Guzzlette\Bridges\Nette\DI;

use GuzzleHttp\Client;
use Matyx\Guzzlette\ClientFactory;
use Matyx\Guzzlette\RequestStack;
use Nette\DI\CompilerExtension;

class GuzzletteExtension extends CompilerExtension {

	private $debugMode;

	/**
	 * GuzzletteExtension constructor.
	 *
	 * @param $debugMode
	 */
	public function __construct($debugMode) { $this->debugMode = $debugMode; }


	public function loadConfiguration() {
		/** @var \Nette\DI\ContainerBuilder $builder */
		$builder = $this->getContainerBuilder();

		if($this->debugMode !== true) {
			$builder->addDefinition($this->prefix('client'))
				->setClass(Client::class);

			return;
		}

		$builder->addDefinition($this->prefix('requestStack'))
			->setClass(RequestStack::class)
			->setAutowired(false);

		$builder->addDefinition($this->prefix('clientFactory'))
			->setClass(ClientFactory::class, [$builder->getDefinition($this->prefix('requestStack'))]);


		$builder->addDefinition($this->prefix('client'))
			->setClass(Client::class)
			->setFactory('@' . $this->prefix('clientFactory') . '::createClient');
	}

}