<?php declare(strict_types = 1);

namespace Contributte\Guzzlette;

class SnapshotStack
{

	/** @var Snapshot[] */
	private array $snapshots = [];

	private float $totalTime = 0;

	public function getTotalTime(): float
	{
		return $this->totalTime;
	}

	public function addSnapshot(Snapshot $r): void
	{
		$this->snapshots[] = $r;
		$this->totalTime += $r->getTime();
	}

	/**
	 * @return Snapshot[]
	 */
	public function getSnapshots(): array
	{
		return $this->snapshots;
	}

}
