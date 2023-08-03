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

$news_feed_array = [];
$promotion_feed_array = [];

//News Feed
if(get_option('ss_rss_feed_link') == 1){
    $key = 0;
    foreach ($feed_items1 as $item) { 
        if( $key <=2 ){
            $news_feed_array[$key]= array(
                'featured_image' => $item->get_item_tags('','featured_image')[0]['data'],
                'title' => $item->get_title(),
                'description'=>substr($item->get_description(), 0, 100),
                'link'=> $item->get_permalink(),
            );
         }
        $key++;
    }
}

//Promotions Feed
if(get_option('ss_rss_feed_link_promotion') == 1){
    $key = 0;
    foreach ($feed_items2 as $item) { 
        if( $key <=2 ){
            $promotion_feed_array[$key]= array(
                'featured_image' => $item->get_item_tags('','featured_image')[0]['data'],
                'title' => $item->get_title(),
                'description'=>substr($item->get_description(), 0, 100),
                'link'=> $item->get_permalink(),
            );
        }
        $key++;
    }
}

if(!empty($news_feed_array) && !empty($promotion_feed_array)){
    $a3 = merge($news_feed_array, $promotion_feed_array);
}else if(!empty($news_feed_array) && empty($promotion_feed_array)){
    $a3 = $news_feed_array;
}else if(empty($news_feed_array) && !empty($promotion_feed_array)){
    $a3 = $promotion_feed_array;
}

function merge($a1, $a2)
{
    $a3 = [];
    $len = count($a1);
    for($i=0;$i<$len;$i++)
    {
        $a3 []= $a1[$i];
        $a3 []= $a2[$i];
    }
    return $a3;
}

foreach ($a3 as $item) { 
   ?>
    <li>
        <div class="uk-card uk-card-default">
            <div class="uk-card-media-top">
                <?php if($item['featured_image'] != ''){ ?>
                    <img src="<?php echo $item['featured_image'];?>" width="1800" height="1200" alt="">
                <?php }?>
            </div>
            <div class="uk-card-body">
                <h3 class="uk-card-title"><?php echo $item['title'];?></h3>
                <p><?php echo $item['description']?></p> 
                <a href="<?php echo $item['link'] ?>" target="_blank">Learn More</a>
            </div>
        </div>
    </li> 
<?php }
?>