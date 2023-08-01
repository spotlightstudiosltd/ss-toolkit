<?php
/**
* Template Name: RSS Feed
*/
// Include the necessary files
require_once ABSPATH . WPINC . '/feed.php';

$feed_url = 'https://spotlightstudios.co.uk/feed/';
if(get_option('ss_rss_feed_link') != null || get_option('ss_rss_feed_link') != ''){
    $feed_url = get_option('ss_rss_feed_link');
}

// Fetch the feed
$rss = fetch_feed($feed_url);
// Check if there is an error while fetching the feed
do {
    if (is_wp_error($rss)) {
        // The feed is not valid
        // echo "The feed is not valid: " . $rss->get_error_message();
        $feed_url = 'https://spotlightstudios.co.uk/feed/';
        $rss = fetch_feed($feed_url);
        $feed_items = $rss->get_items();
        break;
    } else {
        // The feed is valid
        // Get feed items to ensure the feed data is valid
        $feed_items = $rss->get_items();
        if (!$feed_items) {
            // The feed is not valid
            echo "The feed is not valid or empty.";
            $feed_url = 'https://spotlightstudios.co.uk/feed/';
            break;
        } else {
            // The feed is valid
            echo "The feed is valid.";
        }
    }
}while(0);

$key = 0;
foreach ($feed_items as $item) { 
    if( $key <=2 ){?>
        <li>
            <div class="uk-card uk-card-default">
                <div class="uk-card-media-top">
                    <?php if(isset($item->get_item_tags('','featured_image')[0]['data'])){ ?>
                        <img src="<?php echo $item->get_item_tags('','featured_image')[0]['data']?>" width="1800" height="1200" alt="">
                    <?php }?>
                </div>
                <div class="uk-card-body">
                    <h3 class="uk-card-title"><?php echo $item->get_title();?></h3>
                    <p><?php echo substr($item->get_description(), 0, 100);?></p> 
                    <a href="<?php echo $item->get_permalink(); ?>" target="_blank">Learn More</a>
                </div>
            </div>
        </li>
    <?php }
    $key++;
}

?>