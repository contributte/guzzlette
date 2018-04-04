<?php

namespace Matyx\Guzzlette;

class RequestStack
{
	private $requests = [];
	private $totalTime = 0;


	public function getTotalTime()
	{
		return $this->totalTime;
	}


	public function addRequest(Request $r)
	{
		$this->requests[] = $r;
		$this->totalTime += $r->getTime();
	}


	public function getRequests()
	{
		return $this->requests;
	}
}
