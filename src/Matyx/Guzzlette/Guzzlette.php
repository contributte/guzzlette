<?php

namespace Matyx\Guzzlette;

use GuzzleHttp;
use Tracy;

class Guzzlette {
	/** @var  GuzzleHttp\Client */
	protected static $client = NULL;

	/**
	 * @param $tempDir
	 * @param array $guzzleConfig
	 * @return \GuzzleHttp\Client
	 * @throws \Matyx\Guzzlette\GuzzletteException
	 */
	public static function createGuzzleClient($tempDir, $guzzleConfig = []) {
		if(isset(static::$client)) return static::$client;

		$handler = NULL;
		if(isset($guzzleConfig['handler'])) {
			$handler = $guzzleConfig['handler'];
			if(!($handler instanceof GuzzleHttp\HandlerStack)) {
				throw new GuzzletteException("Handler must be instance of " . GuzzleHttp\HandlerStack::class);
			}
		}
		else {
			$handler = GuzzleHttp\HandlerStack::create();
		}

		$requestStack = new RequestStack();
		Tracy\Debugger::getBar()->addPanel(new TracyPanel($tempDir, $requestStack));

		$handler->push(function (callable $handler) use ($requestStack) {
			return function ($request, array $options) use ($handler, $requestStack) {
				Tracy\Debugger::timer();

				$guzzletteRequest = new Request();
				$guzzletteRequest->request = $request;

				return $handler($request, $options)->then(function ($response) use ($requestStack, $guzzletteRequest) {
					$guzzletteRequest->time = Tracy\Debugger::timer();
					$guzzletteRequest->response = $response;

					$requestStack->addRequest($guzzletteRequest);

					return $response;
				});
			};
		});

		$guzzleConfig['handler'] = $handler;

		static::$client = new GuzzleHttp\Client($guzzleConfig);

		return static::$client;
	}
}