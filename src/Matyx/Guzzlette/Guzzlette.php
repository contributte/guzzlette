<?php

namespace Matyx\Guzzlette;

use GuzzleHttp;
use Tracy;

class Guzzlette {
	const FORCE_REQUEST_COLLECTION = true;

	/** @var GuzzleHttp\Client */
	private static $client;

	/** @var  \Matyx\Guzzlette\RequestStack */
	private $requestStack;

	private $forceRequestCollection = false;

	private $panelRegistered = false;

	/**
	 * Guzzlette constructor.
	 *
	 * @param bool $forceRequestCollection
	 * @param bool $registerTracyPanel
	 */
	public function __construct($forceRequestCollection = false, $registerTracyPanel = true) {
		$this->requestStack = new RequestStack();
		if($registerTracyPanel) {
			$this->registerTracyPanel();
		}
		$this->forceRequestCollection = $forceRequestCollection;
	}

	/**
	 * @param array $guzzleConfig
	 * @return \GuzzleHttp\Client
	 */
	public function createGuzzleClient($guzzleConfig = []) {
		if(Tracy\Debugger::isEnabled() || $this->forceRequestCollection) {
			$handler = $this->createHandlerStack((isset($guzzleConfig['handler']) ? $guzzleConfig['handler'] : NULL));

			$guzzleConfig['handler'] = $handler;
		}

		return new GuzzleHttp\Client($guzzleConfig);
	}

	public function createHandlerStack(GuzzleHttp\HandlerStack $handlerStack = NULL) {
		if($handlerStack === NULL) {
			$handlerStack = GuzzleHttp\HandlerStack::create();
		}

		$guzzletteHandler = new GuzzletteHandler($this->requestStack);
		$handlerStack->push($guzzletteHandler);

		return $handlerStack;
	}

	public function registerTracyPanel() {
		if(!$this->panelRegistered) {
			Tracy\Debugger::getBar()->addPanel(new TracyPanel($this->requestStack));
			$this->panelRegistered = true;
		}
	}

	/**
	 * @return \Matyx\Guzzlette\RequestStack
	 */
	public function getRequestStack() {
		return $this->requestStack;
	}
}