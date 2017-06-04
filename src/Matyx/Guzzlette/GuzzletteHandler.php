<?php

namespace Matyx\Guzzlette;

use Tracy;

class GuzzletteHandler {
	/** @var \Matyx\Guzzlette\RequestStack */
	private $requestStack;

	/**
	 * GuzzletteHandler constructor.
	 *
	 * @param \Matyx\Guzzlette\RequestStack $requestStack
	 */
	public function __construct(\Matyx\Guzzlette\RequestStack $requestStack) { $this->requestStack = $requestStack; }

	public function __invoke(callable $nextHandler) {
		$requestStack = $this->requestStack;

		return function ($request, array $options) use ($nextHandler, $requestStack) {
			Tracy\Debugger::timer('Guzzlette'); //Start a timer

			return $nextHandler($request, $options)->then(function ($response) use ($requestStack, $request) {
				$requestStack->addRequest(new Request(
					$request,
					$response,
					Tracy\Debugger::timer('Guzzlette')
				));

				return $response;
			});
		};
	}
}