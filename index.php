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

$expires = 60; //12 Hours
$cache = new Dunhakdis\Changelog\Cache();
$cacheKey = 'changelog';
$issues = array();
$count = 0;

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
?>
<div id="wrap">
<h1 class="title">Thrive Intranet & Community WP Theme</h1>
<h1 class="subtitle">Revision History, Bug Fixes, Changelog</h1>

<?php
if ( ! empty ( $issues ) ) {
	echo '<ul>';
	foreach($issues as $issue){ ?>
		<li class="list">
			<?php if ( $count === 0 ) { ?>
				<hr/>
				<div class="updated-at">
					Last Updated: 
						<?php echo date_format( date_create($issue->milestone->updated_at), "M d, Y"); ?>
				</div>
			<?php } ?>
			<?php if ( $count>=1) { ?>
				<?php if ( $issues[$count-1]->milestone->title !== $issue->milestone->title ): ?>
					<hr /><!-- separate issues by milestone-->
					<div class="updated-at">
						Last Updated: 
						<?php echo date_format( date_create($issue->milestone->updated_at), "M d, Y"); ?>
						
					</div>
				<?php endif; ?>
			<?php } ?>

			<div class="columns">
				<div class="column">
				   	<a href="<?php echo $issue->milestone->html_url; ?>">
						<span class="milestone tag is-info">
							Version 
							<?php if( isset( $issue->milestone->title ) ) { ?>
								<?php echo $issue->milestone->title; ?>
							<?php } else { ?>
								~
							<?php } ?>
						</span>
					</a>
				</div>
				<div class="column is-four-fifths">
				    <a href="<?php echo $issue->html_url;?>" title="<?php echo $issue->title; ?>">
				    	<?php echo sprintf('#%d %s', $issue->number, $issue->title); ?>
				    	<span class="icon has-text-success">
  <i class="fas fa-check-square"></i>
</span>
						<?php if ( isset( $issue->labels ) && ! empty( $issue->labels ) ): ?>
							<p>
							<?php foreach ( $issue->labels as $label ): ?>
								<span class="is-size-7" style="color: #<?php echo $label->color;?>">
									<?php echo $label->name; ?>
								</span>
							<?php endforeach; ?>
						</p>
						<?php endif; ?>
					</a>
				</div>
			</div>

			<?php $count++ ;?>
		</li>
	<?php 
	} 
	echo '</ul>';
}
?>
</div><!--#wrap-->
</body>
</html>