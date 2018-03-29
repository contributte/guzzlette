<?php

namespace Matyx\Guzzlette\DI;

use GuzzleHttp\Client;
use Matyx\Guzzlette\Bridges\Nette\DI\GuzzletteExtension;
use Matyx\Guzzlette\ClientFactory;
use Nette\DI\Compiler;
use Nette\DI\Container;
use Nette\DI\ContainerLoader;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../../bootstrap.php';

/**
 * @testCase
 */
class GuzzletteExtensionTest extends TestCase
{
	public function testExtensionDebug()
	{
		$loader = new ContainerLoader(TEMP_DIR, true);
		$class = $loader->load(function (Compiler $compiler) {
			$compiler->addConfig(['parameters' => [
				'debugMode' => true,
			]]);
			$compiler->addExtension('guzzlette', new GuzzletteExtension(true));
		}, [microtime(), 1]);

		/** @var Container $container */
		$container = new $class;

		Assert::count(1, $container->findByType(Client::class));
		Assert::count(1, $container->findByType(ClientFactory::class));
	}


	public function testExtensionProduction()
	{
		$loader = new ContainerLoader(TEMP_DIR, true);
		$class = $loader->load(function (Compiler $compiler) {
			$compiler->addConfig(['parameters' => [
				'debugMode' => false,
			]]);

			$compiler->addExtension('guzzlette', new GuzzletteExtension(false));
		}, [microtime(), 1]);

		/** @var Container $container */
		$container = new $class;

		Assert::count(1, $container->findByType(Client::class));
		Assert::count(0, $container->findByType(ClientFactory::class));
	}
}


(new GuzzletteExtensionTest())->run();
