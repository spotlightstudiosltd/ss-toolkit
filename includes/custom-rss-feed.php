<?php
/**
* Template Name: RSS Feed
*/
// Include the necessary files
require_once ABSPATH . WPINC . '/feed.php';

// News Feed URL
$feed_url = 'https://spotlightstudios.co.uk/feed/';
$rss_feed1 = fetch_feed($feed_url);
$feed_items1 = $rss_feed1->get_items();

// Promotions Feed URL
$feed_url_promotions = 'https://spotlightstudios.co.uk/promotions/feed/';
$rss_feed2 = fetch_feed($feed_url_promotions);
$feed_items2 = $rss_feed2->get_items();

if(get_option('ss_rss_feed_link') == 1){
    $key = 0;
    foreach ($feed_items1 as $item) { 
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
}

if(get_option('ss_rss_feed_link_promotion') == 1){
    $key = 0;
    foreach ($feed_items2 as $item) { 
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
}
?>