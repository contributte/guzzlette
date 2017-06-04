<?php

namespace Matyx\Guzzlette;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../../bootstrap.php';

/**
 * @testCase
 */
class TracyPanelTest extends TestCase {

	public function testTracyPanel() {
		$guzzlette = new \Matyx\Guzzlette\Guzzlette(\Matyx\Guzzlette\Guzzlette::FORCE_REQUEST_COLLECTION);

		$client = $guzzlette->createGuzzleClient();
		$stack = $guzzlette->getRequestStack();

		$panel = new TracyPanel($stack);
		Assert::same(false, $panel->getTab());

		$client->request('GET', 'https://api.github.com/repos/stedolan/jq/commits?per_page=5');

		Assert::same(1,count($stack->getRequests()));
		Assert::notSame(false, $panel->getTab());

		// Is panel rendering OK?
		$panel->getPanel();
	}
}


(new TracyPanelTest())->run();
