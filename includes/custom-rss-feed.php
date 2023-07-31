<?php
/**
* Template Name: RSS Feed
*/
$feed_url = 'https://spotlightstudios.co.uk/feed/';
if(get_option('ss_rss_feed_link') != null || get_option('ss_rss_feed_link') != ''){
    $feed_url = get_option('ss_rss_feed_link');
}

$sValidator = 'http://feedvalidator.org/check.cgi?url=';
if( stristr( $sValidationResponse , 'This is a valid RSS feed' ) === false )
{
    $feed_url = 'https://spotlightstudios.co.uk/feed/';
}
$xml = file_get_contents($feed_url);$feed = simplexml_load_string($xml);
if ($feed === false) {echo 'Failed to load the feed.';} else {$key = 0;foreach ($feed->channel->item as $item) { if( $key <=2 ){?><li><div class="uk-card uk-card-default"><div class="uk-card-media-top"><img src="<?php echo $item->featured_image?>" width="1800" height="1200" alt=""></div><div class="uk-card-body"><h3 class="uk-card-title"><?php echo $item->title?></h3><p><?php echo substr($item->description, 0, 100);?></p> <a href="<?php echo $item->link ?>" target="_blank">Learn More</a></div></div></li><?php  }$key++;}}
?>