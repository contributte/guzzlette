<?php

namespace Matyx\Guzzlette\Bridges\Nette\DI;

use GuzzleHttp\Client;
use Matyx\Guzzlette\ClientFactory;
use Matyx\Guzzlette\RequestStack;
use Nette\DI\CompilerExtension;
use Nette\DI\Config;

class GuzzletteExtension extends CompilerExtension
{
	/** @var array */
	private $defaults = [
		'debugger' => '%debugMode%',
		'timeout' => 30,
	];

	private $debugMode = false;


	/**
	 * @param bool $debugMode
	 * @deprecated please configure using debugger extension parameter
	 */
	public function __construct($debugMode = false)
	{
		$this->debugMode = $debugMode;
	}


	public function loadConfiguration()
	{
		/** @var \Nette\DI\ContainerBuilder $builder */
		$builder = $this->getContainerBuilder();

		$config = Config\Helpers::merge($this->config, $this->getContainerBuilder()->expand($this->defaults));
		$debugMode = $this->debugMode || $config['debugger'];
		unset($config['debugMode']);

		if ($debugMode === false) { // production mode, registers client directly without factory
			$builder->addDefinition($this->prefix('client'))
				->setClass(Client::class, ['config' => $config]);

			return;
		}

		$builder->addDefinition($this->prefix('requestStack'))
			->setClass(RequestStack::class)
			->setAutowired(false);

		$builder->addDefinition($this->prefix('clientFactory'))
			->setClass(ClientFactory::class, [$builder->getDefinition($this->prefix('requestStack'))]);


		$builder->addDefinition($this->prefix('client'))
			->setClass(Client::class)
			->setFactory('@' . $this->prefix('clientFactory') . '::createClient', ['guzzleConfig' => $config]);
	}
}
