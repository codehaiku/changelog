<?php
require_once(__DIR__ . '/config.php');
require_once(__DIR__ . '/vendor/autoload.php');

$client = new GuzzleHttp\Client();

$params = '?state=closed';

$request = $client->request('GET', 'https://api.github.com/repos/codehaiku/thrive-issue-tracker/issues'. $params,[
	'auth' => [USER, PASSWORD],
]);

$issues = json_decode( $request->getBody() );

if ( ! empty ( $issues ) ) {
	echo '<ul>';
	foreach($issues as $issue){ ?>
		<li>
			<a href="<?php echo $issue->url;?>" title="<?php echo $issue->title; ?>">
				<?php echo sprintf('#%d %s', $issue->number, $issue->title); ?>
			</a>
		</li>
	<?php 
	} 
	echo '</ul>';
}