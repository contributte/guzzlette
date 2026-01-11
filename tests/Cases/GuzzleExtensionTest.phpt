<?php declare(strict_types = 1);

namespace Tests\Cases;

use Contributte\Guzzlette\ClientFactory;
use Contributte\Guzzlette\DI\GuzzleExtension;
use Contributte\Tester\Environment;
use Contributte\Tester\Toolkit;
use GuzzleHttp\Client;
use Nette\DI\Compiler;
use Nette\DI\Container;
use Nette\DI\ContainerLoader;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

Toolkit::test(function (): void {
	$loader = new ContainerLoader(Environment::getTestDir(), true);
	$class = $loader->load(function (Compiler $compiler): void {
		$compiler->addConfig([
			'guzzle' => [
				'debug' => true,
			],
		]);
		$compiler->addExtension('guzzle', new GuzzleExtension());
	}, [getmypid(), 1]);

	/** @var Container $container */
	$container = new $class();

	Assert::count(1, $container->findByType(Client::class));
	Assert::count(1, $container->findByType(ClientFactory::class));
});

Toolkit::test(function (): void {
	$loader = new ContainerLoader(Environment::getTestDir(), true);
	$class = $loader->load(function (Compiler $compiler): void {
		$compiler->addConfig([
			'guzzle' => [
				'debug' => false,
			],
		]);
		$compiler->addExtension('guzzle', new GuzzleExtension());
	}, [getmypid(), 2]);

	/** @var Container $container */
	$container = new $class();

	Assert::count(1, $container->findByType(Client::class));
	Assert::count(1, $container->findByType(ClientFactory::class));
});

Toolkit::test(function (): void {
	$loader = new ContainerLoader(Environment::getTestDir(), true);
	$class = $loader->load(function (Compiler $compiler): void {
		$compiler->setDynamicParameterNames(['guzzle-client']);
		$compiler->addConfig([
			'parameters' => [
				'guzzle-client' => [],
			],
			'guzzle' => [
				'client' => '%guzzle-client%',
			],
		]);
		$compiler->addExtension('guzzle', new GuzzleExtension());
	}, [getmypid(), 3]);

	/** @var Container $container */
	$container = new $class();

	Assert::count(1, $container->findByType(Client::class));
	Assert::count(1, $container->findByType(ClientFactory::class));
});
