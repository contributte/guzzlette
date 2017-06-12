<?php

namespace Matyx\Guzzlette\DI;

use GuzzleHttp\Client;
use Matyx\Guzzlette\Guzzlette;
use Nette\DI\Compiler;
use Nette\DI\Container;
use Nette\DI\ContainerLoader;
use Tester\Assert;
use Tester\TestCase;
use Tracy\Debugger;

require_once __DIR__ . '/../../bootstrap.php';

/**
 * @testCase
 */
class GuzzletteExtensionTest extends TestCase {

	public function testExtensionDebug() {
		$loader = new ContainerLoader(TEMP_DIR, true);
		$class = $loader->load(function (Compiler $compiler) {
			$compiler->addExtension('guzzlette', new GuzzletteExtension());
		}, [microtime(), 1]);

		/** @var Container $container */
		$container = new $class;

		Assert::count(1, $container->findByType(Client::class));
		Assert::count(1, $container->findByType(Guzzlette::class));
	}

	public function testExtensionProduction() {
		Debugger::$productionMode = true;
		$loader = new ContainerLoader(TEMP_DIR, true);
		$class = $loader->load(function (Compiler $compiler) {
			$compiler->addExtension('guzzlette', new GuzzletteExtension());
		}, [microtime(), 1]);

		/** @var Container $container */
		$container = new $class;

		Assert::count(1, $container->findByType(Client::class));
		Assert::count(0, $container->findByType(Guzzlette::class));
	}
}


(new GuzzletteExtensionTest())->run();
