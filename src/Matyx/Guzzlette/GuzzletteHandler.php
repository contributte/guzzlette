<?php

namespace Matyx\Guzzlette;

class GuzzletteHandler
{
	/** @var \Matyx\Guzzlette\RequestStack */
	private $requestStack;


	/**
	 * GuzzletteHandler constructor.
	 *
	 * @param \Matyx\Guzzlette\RequestStack $requestStack
	 */
	public function __construct(\Matyx\Guzzlette\RequestStack $requestStack)
	{
		$this->requestStack = $requestStack;
	}


	public function __invoke(callable $nextHandler)
	{
		$requestStack = $this->requestStack;

		return function ($request, array $options) use ($nextHandler, $requestStack) {
			$startTime = microtime(true);

			return $nextHandler($request, $options)->then(function ($response) use ($startTime, $requestStack, $request) {
				$requestStack->addRequest(new Request(
					$request,
					$response,
					(microtime(true) - $startTime)
				));

				return $response;
			});
		};
	}
}
