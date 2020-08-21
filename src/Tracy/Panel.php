<?php declare(strict_types = 1);

namespace Contributte\Guzzlette\Tracy;

use Contributte\Guzzlette\SnapshotStack;
use Namshi\Cuzzle\Formatter\CurlFormatter;
use Tracy;

class Panel implements Tracy\IBarPanel
{

	/** @var  SnapshotStack */
	protected $snapshotStack;

	public function __construct(SnapshotStack $snapshotStack)
	{
		$this->snapshotStack = $snapshotStack;
	}

	public function getTab(): string
	{
		if ($this->snapshotStack->getSnapshots() === []) {
			return '';
		}

		$snapshotStack = $this->snapshotStack;

		ob_start();
		require __DIR__ . '/templates/tab.phtml';
		return (string) ob_get_clean();
	}

	public function getPanel(): string
	{
		$snapshots = $this->snapshotStack->getSnapshots();

		ob_start();
		require __DIR__ . '/templates/panel.phtml';
		return (string) ob_get_clean();
	}

}
