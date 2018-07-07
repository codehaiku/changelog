<!DOCTYPE html>
<html>
<head>
	<title>Thrive Issues Log</title>
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.0/css/all.css" integrity="sha384-lKuwvrZot6UHsBSfcMvOkWwlCMgc0TaWr+30HWe3a4ltaBwTZhyTEggF5tJv8tbt" crossorigin="anonymous">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.7.1/css/bulma.min.css" />
	<link rel="stylesheet" href="style.css" />
</head>
<body>
<?php
require_once(__DIR__ . '/vendor/autoload.php');
require_once(__DIR__ . '/config.php');
require_once(__DIR__ . '/cache.php');

$expires = 60; 
$cache = new Dunhakdis\Changelog\Cache();
$cacheKey = 'changelog';
$issues = array();
$counter = 0;

if ( ! $cache->hasCache( $cacheKey ) ) {
	// Cache is not set, let's fetch some data.
	$client = new GuzzleHttp\Client();
	$params = '?state=closed&sort=created';
	$request = $client->request('GET', 'https://api.github.com/repos/codehaiku/thrive-issue-tracker/issues'. $params,[
		'auth' => [USER, PASSWORD],
	]);
	$issues = json_decode( $request->getBody() );
	$cache->setCache( $cacheKey, $issues, $expires );
} else {
	$issues = $cache->getCache( $cacheKey );
}

$list = array();
?>
<div id="wrap">
<h1 class="title">Thrive Intranet & Community WP Theme</h1>
<h1 class="subtitle">Revision History, Bug Fixes, Changelog</h1>

<?php
if ( ! empty ( $issues ) ) {
	foreach($issues as $issue){ ?>
		<?php
			$list[$issue->milestone->title][] = array(
					'number' => $issue->number,
					'title' => $issue->title,
					'url' => $issue->html_url,
					'updated_at' => date_format( date_create($issue->milestone->updated_at), "M d, Y"),
					'milestone_url' => $issue->milestone->html_url
				);
		?>
	<?php 
	} 
}
include __DIR__ . '/template.phtml';
?>

</div><!--#wrap-->
</body>
</html>