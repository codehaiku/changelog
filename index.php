<!DOCTYPE html>
<html>
<head>
	<title>Revision History, Bug Fixes, Changelog | Thrive Intranet & Community WordPress Theme</title>
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.0/css/all.css" integrity="sha384-lKuwvrZot6UHsBSfcMvOkWwlCMgc0TaWr+30HWe3a4ltaBwTZhyTEggF5tJv8tbt" crossorigin="anonymous">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.7.1/css/bulma.min.css" />
	<link rel="stylesheet" href="style.css" />
</head>
<body>
<?php
require_once(__DIR__ . '/vendor/autoload.php');
require_once(__DIR__ . '/config.php');
require_once(__DIR__ . '/cache.php');

$expires = 43200; //12 hours
$cache = new Dunhakdis\Changelog\Cache();

$issues = array();
$counter = 0;

$per_page = 10;
$page = 1;

if ( isset( $_GET['page']) && is_numeric($_GET['page'])) {
	$page = $_GET['page'];
}

$params = '?state=closed&sort=created&page='.$page.'&per_page='.$per_page;
$url = 'https://api.github.com/repos/codehaiku/thrive-issue-tracker/issues'. $params;

$cacheKey = md5($url);

if ( isset($_GET['__deleteCache'])) {
	$cache->clearCache();
}

if ( ! $cache->hasCache( $cacheKey ) ) {
	// Cache is not set, let's fetch some data.
	$client = new GuzzleHttp\Client();
	
	$request = $client->request('GET', $url,[
		'auth' => [USER, PASSWORD],
	]);

	$links = $request->getHeader('link');
	
	$pages_links = explode(',',$links[0]);
	$last = $pages_links[1];
	
	$paging = array();

	foreach( $pages_links as $link ) {
		// Last Page
		if ( strpos($link, 'rel="last"') ) {
			preg_match('/page=(\d+).*$/', $link, $matches);
			$paging['last'] = $matches[1];
		}
		// First Page
		if ( strpos($link, 'rel="first"')) {
			preg_match('/page=(\d+).*$/', $link, $matches);
			$paging['first'] = $matches[1];
		}
		// Previous Page.
		if ( strpos($link, 'rel="prev"')) {
			preg_match('/page=(\d+).*$/', $link, $matches);
			$paging['prev'] = $matches[1];
		}
		// Previous Page.
		if ( strpos($link, 'rel="next"')) {
			preg_match('/page=(\d+).*$/', $link, $matches);
			$paging['next'] = $matches[1];
		}
	}
	$issues = json_decode( $request->getBody() );
	// Cache issues
	$cache->setCache( $cacheKey, $issues, $expires );
	// Cache paging
	$cache->setCache( 'paging'. $cacheKey, $paging, $expires );

} else {
	$issues = $cache->getCache( $cacheKey );
	$paging = $cache->getCache( 'paging'. $cacheKey );
}

$list = array();
?>
<div id="wrap">
<h1 class="title">Thrive Intranet & Community WP Theme</h1>
<h1 class="subtitle">Revision History, Bug Fixes, Changelog</h1>

<?php
if ( ! empty ( $issues ) ) {
	//echo count($issues);
	foreach($issues as $issue){ ?>
		
		<?php
			if( isset( $issue->milestone->title ) ) {
				$list[$issue->milestone->title][] = array(
					'number' => $issue->number,
					'title' => $issue->title,
					'url' => $issue->html_url,
					'updated_at' => date_format( date_create($issue->milestone->updated_at), "M d, Y"),
					'milestone_url' => $issue->milestone->html_url
				);
			}
		?>
	<?php 
	} 
}
include __DIR__ . '/template.phtml';
?>

</div><!--#wrap-->
</body>
</html>