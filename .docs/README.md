# Contributte Guzzlette

[Guzzle](https://github.com/guzzle/guzzle) integration into Nette Framework

## Content

- [Setup](#usage)
- [Configuration](#configuration)
- [Implementation](#implementation)

## Setup

```bash
composer require contributte/guzzlette
```

```neon
extensions:
	guzzle: Contributte\Guzzlette\DI\GuzzleExtension
```

## Configuration

```neon
guzzle:
	debug: %debugMode%
	tracy: %debugMode%
	preset: default
	app: MyApp/1.0
	logger:
		level: info
		formatter: "[{method}] {uri} {code}"
		# logger: @Psr\Log\LoggerInterface
	client: # config for GuzzleHttp\Client
		timeout: 30
```

## Implementation

Get guzzle from DIC instead of creating a new one.
Everything else is in Guzzle documentation.

```php

use Contributte\Guzzlette\ClientFactory;
use Contributte\Guzzlette\GuzzleBuilder;
use GuzzleHttp\Client;
use Nette\Application\UI\Presenter;

class ExamplePresenter extends Presenter {

	/** @var Client */
	private $guzzle;

	public function injectGuzzle(Client $guzzle): void
	{
		$this->guzzle = $guzzle
	}

	// Alternatively you could create new instance through ClientFactory
	public function injectGuzzle(ClientFactory $factory): void
	{
		$this->guzzle = $factory->createClient([
			'timeout' => 30
		]);
	}

	public function injectGuzzleBuilder(ClientFactory $factory): void
	{
		$this->guzzle = $factory
			->create()
			->withBaseUri('https://api.example.com')
			->withHttpAuth('john', 'doe')
			->build();
	}

}
```
