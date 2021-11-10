<?php declare(strict_types = 1);

namespace Contributte\Guzzlette\Tracy;

use Contributte\Guzzlette\SnapshotStack;
use Nette\Utils\Helpers;
use Tracy;

class Panel implements Tracy\IBarPanel
{

	/** @var SnapshotStack */
	protected $snapshotStack;

	public function __construct(SnapshotStack $snapshotStack)
	{
		$this->snapshotStack = $snapshotStack;
	}

	public function getTab(): string
	{
		return Helpers::capture(function (): void {
			// phpcs:disable
			$totalTime = $this->snapshotStack->getTotalTime();
			$count = $this->snapshotStack->getNumberOfSnapshots();
			require __DIR__ . '/templates/tab.phtml';
		});
	}

	/**
	 * @return string|null
	 */
	public function getPanel(): ?string
	{
		if ($this->snapshotStack->getNumberOfSnapshots() === 0) {
			return null;
		}

		return Helpers::capture(function (): void {
			// phpcs:disable
			$snapshots = $this->snapshotStack->getSnapshots();
			require __DIR__ . '/templates/panel.phtml';
		});
	}

}
