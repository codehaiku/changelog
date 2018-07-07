<!DOCTYPE html>
<html>
<head>
	<title>Thrive Issues Log</title>
</head>
<body>
<?php
require_once(__DIR__ . '/vendor/autoload.php');
require_once(__DIR__ . '/config.php');
require_once(__DIR__ . '/cache.php');

$expires = 60; //12 Hours
$cache = new Dunhakdis\Changelog\Cache();
$cacheKey = 'changelog';
$issues = array();

if ( ! $cache->hasCache( $cacheKey ) ) {
	// Cache is not set, let's fetch some data.
	$client = new GuzzleHttp\Client();
	$params = '?state=closed';
	$request = $client->request('GET', 'https://api.github.com/repos/codehaiku/thrive-issue-tracker/issues'. $params,[
		'auth' => [USER, PASSWORD],
	]);
	$issues = json_decode( $request->getBody() );
	$cache->setCache( $cacheKey, $issues, $expires );
} else {
	$issues = $cache->getCache( $cacheKey );
}

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
?>
</body>
</html>