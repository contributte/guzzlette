<?php

namespace Matyx\Guzzlette;

use GuzzleHttp;
use Tracy;

class Guzzlette {
	const FORCE_REQUEST_COLLECTION = true;

	/** @var  \Matyx\Guzzlette\RequestStack */
	private $requestStack;

	public function __construct() {
		$this->requestStack = new RequestStack();
		$this->registerTracyPanel();
	}

	/**
	 * @param array $guzzleConfig
	 * @return \GuzzleHttp\Client
	 */
	public function createGuzzleClient($guzzleConfig = []) {
		$handler = $this->createHandlerStack((isset($guzzleConfig['handler']) ? $guzzleConfig['handler'] : NULL));
		$guzzleConfig['handler'] = $handler;

		return new GuzzleHttp\Client($guzzleConfig);
	}

	/**
	 * @param \GuzzleHttp\HandlerStack|NULL $handlerStack
	 * @return \GuzzleHttp\HandlerStack
	 */
	private function createHandlerStack(GuzzleHttp\HandlerStack $handlerStack = NULL) {
		if($handlerStack === NULL) {
			$handlerStack = GuzzleHttp\HandlerStack::create();
		}

		$guzzletteHandler = new GuzzletteHandler($this->requestStack);
		$handlerStack->push($guzzletteHandler);

		return $handlerStack;
	}

	private function registerTracyPanel() {
		Tracy\Debugger::getBar()->addPanel(new TracyPanel($this->requestStack));
	}

	/**
	 * @return \Matyx\Guzzlette\RequestStack
	 */
	public function getRequestStack() {
		return $this->requestStack;
	}
}