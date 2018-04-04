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

	private $debugMode;


	/**
	 * @param bool $debugMode
	 * @deprecated please configure using debugger extension parameter
	 */
	public function __construct($debugMode = null)
	{
		$this->debugMode = $debugMode;
	}


	public function loadConfiguration()
	{
		/** @var \Nette\DI\ContainerBuilder $builder */
		$builder = $this->getContainerBuilder();

		$config = Config\Helpers::merge($this->config, $builder->expand($this->defaults));

		// Backwards compatibility, $config['debugger'] overrides constructor parameter
		if ($this->debugMode !== null && !isset($config['debugger'])) {
			$config['debugger'] = $this->debugMode;
		}

		$enableDebugger = (isset($config['debugger']) ? $config['debugger'] : false);
		unset($config['debugger']);

		$builder->addDefinition($this->prefix('requestStack'))
			->setClass(RequestStack::class)
			->setAutowired(false);

		$builder->addDefinition($this->prefix('clientFactory'))
			->setClass(ClientFactory::class, [$builder->getDefinition($this->prefix('requestStack')), $enableDebugger]);


		$builder->addDefinition($this->prefix('client'))
			->setClass(Client::class)
			->setFactory('@' . $this->prefix('clientFactory') . '::createClient', ['config' => $config]);
	}
}
