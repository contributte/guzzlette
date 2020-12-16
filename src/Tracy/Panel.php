<?php declare(strict_types = 1);

namespace Contributte\Guzzlette\Tracy;

use Contributte\Guzzlette\SnapshotStack;
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

		// phpcs:disable
		$snapshotStack = $this->snapshotStack;

		ob_start();
		require __DIR__ . '/templates/tab.phtml';
		return (string) ob_get_clean();
	}

	public function getPanel(): string
	{
		// phpcs:disable
		$snapshots = $this->snapshotStack->getSnapshots();

		ob_start();
		require __DIR__ . '/templates/panel.phtml';
		return (string) ob_get_clean();
	}

}
