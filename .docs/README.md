# Guzzlette

[Guzzle](https://github.com/guzzle/guzzle) integration into Nette Framework

## Content

- [Usage - how to register](#usage)
- [Configuration - how to configure](#configuration)
- [Implementation - how to work with guzzle](/.docs/README.md#implementation)

## Usage

```yaml
extensions:
    guzzle: Contributte\Guzzlette\DI\GuzzleExtension
```

## Configuration

```yaml
guzzle:
    debug: %debugMode%
    client: # config for GuzzleHttp\Client
        timeout: 30
```

## Implementation

Get guzzle from DIC instead of creating a new one.
Everything else is in Guzzle documentation.

```php

use Contributte\Guzzlette\ClientFactory;
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

}
```
