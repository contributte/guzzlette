<?php

namespace Matyx\Guzzlette\DI;

use GuzzleHttp\Client;
use Matyx\Guzzlette\Guzzlette;
use Nette\DI\CompilerExtension;
use Tracy\Debugger;

class GuzzletteExtension extends CompilerExtension {

	public function loadConfiguration() {
		/** @var \Nette\DI\ContainerBuilder $builder */
		$builder = $this->getContainerBuilder();

		if(!class_exists('Tracy\Debugger') || Debugger::$productionMode === true) {
			$builder->addDefinition($this->prefix('client'))
				->setClass(Client::class);

			return;
		}

		$builder->addDefinition('guzzlette')
			->setClass(Guzzlette::class);

		$builder->addDefinition($this->prefix('client'))
			->setClass(Client::class)
			->setFactory('@guzzlette::createGuzzleClient');
	}

}