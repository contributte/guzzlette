<?php

namespace Matyx\Guzzlette;

use GuzzleHttp;
use Matyx\Guzzlette\Tracy\Panel;
use Tracy;

class ClientFactory
{
	const FORCE_REQUEST_COLLECTION = true;

	/** @var  \Matyx\Guzzlette\RequestStack */
	private $requestStack;

	/** @var bool  */
	private $enableDebugger;


	public function __construct(RequestStack $requestStack, $enableDebugger = false)
	{
		$this->requestStack = $requestStack;
		$this->enableDebugger = $enableDebugger;
		$this->registerTracyPanel();
	}


	/**
	 * @param array $guzzleConfig
	 * @return \GuzzleHttp\Client
	 */
	public function createClient($config = [])
	{
		if ($this->enableDebugger) {
			$handler = $this->createHandlerStack((isset($config['handler']) ? $config['handler'] : null));
			$config['handler'] = $handler;
		}

		return new GuzzleHttp\Client($config);
	}


	/**
	 * @param \GuzzleHttp\HandlerStack|NULL $handlerStack
	 * @return \GuzzleHttp\HandlerStack
	 */
	private function createHandlerStack(GuzzleHttp\HandlerStack $handlerStack = null)
	{
		if ($handlerStack === null) {
			$handlerStack = GuzzleHttp\HandlerStack::create();
		}

		$guzzletteHandler = new GuzzletteHandler($this->requestStack);
		$handlerStack->push($guzzletteHandler);

		return $handlerStack;
	}


	private function registerTracyPanel()
	{
		Tracy\Debugger::getBar()->addPanel(new Panel($this->requestStack));
	}
}
