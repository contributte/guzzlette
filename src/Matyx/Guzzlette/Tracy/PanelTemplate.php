<?php
require_once __DIR__ . '/DummyFormatter.php';

if (!isset($requests)) {
	$requests = [];
}
if (!isset($formatter)) {
	$formatter = new \Matyx\Guzzlette\Tracy\DummyFormatter();
}
?>

<h1>Guzzlette</h1>
<div class="tracy-inner">
	<table style="width: 100%;">
		<tr>
			<th>Request</th>
			<th>Reponse</th>
			<th>Time</th>
		</tr>
		<?php $counter = 0; ?>
		<?php foreach ($requests as $r) {
	?>
		<?php $response = $r->getResponse(); ?>
		<?php $response = $r->getResponse(); ?>
		<?php $request = $r->getRequest(); ?>

		<tr>
			<td>
				<?= htmlspecialchars($request->getMethod(), ENT_QUOTES); ?>
				<?= htmlspecialchars($request->getUri(), ENT_QUOTES); ?>
				<br>

				<strong>
					<a class="tracy-toggle tracy-collapsed" href="#" data-tracy-ref="#nette-addons-Guzzlette-<?= $counter; ?>">CURL</a>
				</strong>
				<div class="tracy-collapsed" id="nette-addons-Guzzlette-<?= $counter; ?>">
					<textarea readonly onclick="this.focus();this.select();" rows="5" style="background: #F4F3F1; padding: 4px; width: 600px; resize: none;"><?= htmlspecialchars($formatter->format($request), ENT_QUOTES); ?></textarea>
				</div>

				<br>

				<strong>Headers:</strong>
				<?= \Tracy\Dumper::toHtml($request->getHeaders(), [
					Tracy\Dumper::COLLAPSE_COUNT => 1,
					Tracy\Dumper::COLLAPSE => 1,
				]); ?>
				<br>

				<strong>Body:</strong>
				<?php if (isset($request->getHeader('Content-Type')[0]) && strpos($request->getHeader('Content-Type')[0], 'application/json') !== false) {
					?>
					<?= \Tracy\Dumper::toHtml(json_decode($request->getBody(), true), [Tracy\Dumper::COLLAPSE_COUNT => 1, Tracy\Dumper::COLLAPSE => 1]); ?>
				<?php } else {
						?>
					<?= \Tracy\Dumper::toHtml((string) $request->getBody(), [Tracy\Dumper::COLLAPSE_COUNT => 1, Tracy\Dumper::COLLAPSE => 1]); ?>
				<?php
					} ?>
			</td>
			<td>
				<?= htmlspecialchars($response->getStatusCode(), ENT_QUOTES); ?>
				<?= htmlspecialchars($response->getReasonPhrase(), ENT_QUOTES); ?>
				<br>

				<strong>Headers:</strong>
				<?= \Tracy\Dumper::toHtml($response->getHeaders(), [
					Tracy\Dumper::COLLAPSE_COUNT => 1,
					Tracy\Dumper::COLLAPSE => 1,
				]); ?>
				<br>

				<strong>Body:</strong>
				<?php if ($response->getHeader('Content-Type') && strpos($response->getHeader('Content-Type')[0], 'application/json') !== false) {
					?>
					<?= \Tracy\Dumper::toHtml(json_decode($response->getBody(), true), [
						Tracy\Dumper::COLLAPSE_COUNT => 1,
						Tracy\Dumper::COLLAPSE => 1,
					]); ?>
				<?php } else {
						?>
					<?= \Tracy\Dumper::toHtml((string) $response->getBody(), [
						Tracy\Dumper::COLLAPSE_COUNT => 1,
						Tracy\Dumper::COLLAPSE => 1,
					]); ?>
				<?php
					} ?>
			</td>
			<td><?= ($r->getTime() ? sprintf('%0.1f ms', $r->getTime() * 1000) : ''); ?></td>
		</tr>
		<?php
} ?>
	</table>
</div>
