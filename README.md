# Guzzlette
Guzzle wrapper integration to nette framework

[![Build Status](https://travis-ci.org/matyx/Guzzlette.svg?branch=master)](https://travis-ci.org/matyx/Guzzlette)
[![Latest stable](https://img.shields.io/packagist/v/matyx/guzzlette.svg)](https://packagist.org/packages/matyx/guzzlette)
[![Build Status](https://travis-ci.org/matyx/Guzzlette.svg?branch=master)](https://travis-ci.org/matyx/Guzzlette)
[![Coverage Status](https://coveralls.io/repos/github/matyx/Guzzlette/badge.svg?branch=travis-ci)](https://coveralls.io/github/matyx/Guzzlette?branch=travis-ci)


Usage:

1, Install via composer
```yaml
composer require matyx/guzlette
```


2, Register service in `config.neon`:
```yaml
services:
	guzzleClient: Matyx\Guzzlette\Guzzlette::createGuzzleClient(%tempDir%)
```

3, Add Optional settings eq. Base API URI
```yaml
services:
	guzzleClient: Matyx\Guzzlette\Guzzlette::createGuzzleClient(%tempDir%, ['base_uri' = %apiBaseUri%])
```


Demo:

![Tracy tab](https://raw.githubusercontent.com/matyx/Guzzlette/master/docs/guzzleta-tab.png?token=AHlnAZmc1MSg4bMnZ8u2bpr4Aawt3sfKks5XK5JrwA%3D%3D)
![Tracy panel](https://raw.githubusercontent.com/matyx/Guzzlette/master/docs/guzzlete-panel.png?token=AHlnAUE7Eh0ZHL9uHyQ-d9hmE-fFK7zbks5XK5KQwA%3D%3D)

