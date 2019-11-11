<?php declare(strict_types = 1);

namespace Tests\Contributte\Guzzlette\DI;

use Contributte\Guzzlette\ClientFactory;
use Contributte\Guzzlette\DI\GuzzleExtension;
use GuzzleHttp\Client;
use Nette\DI\Compiler;
use Nette\DI\Container;
use Nette\DI\ContainerLoader;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../bootstrap.php';

/**
 * @TestCase
 */
class GuzzleExtensionTest extends TestCase
{

	public function testExtensionDebug(): void
	{
		$loader = new ContainerLoader(TEMP_DIR, true);
		$class = $loader->load(function (Compiler $compiler): void {
			$compiler->addConfig(['guzzle' => [
				'debug' => true,
			]]);
			$compiler->addExtension('guzzle', new GuzzleExtension());
		}, [getmypid(), 1]);

		/** @var Container $container */
		$container = new $class();

		Assert::count(1, $container->findByType(Client::class));
		Assert::count(1, $container->findByType(ClientFactory::class));
	}

	public function testExtensionProduction(): void
	{
		$loader = new ContainerLoader(TEMP_DIR, true);
		$class = $loader->load(function (Compiler $compiler): void {
			$compiler->addConfig(['guzzle' => [
				'debug' => false,
			]]);
			$compiler->addExtension('guzzle', new GuzzleExtension());
		}, [getmypid(), 2]);

		/** @var Container $container */
		$container = new $class();

		Assert::count(1, $container->findByType(Client::class));
		Assert::count(1, $container->findByType(ClientFactory::class));
	}

}

(new GuzzleExtensionTest())->run();
