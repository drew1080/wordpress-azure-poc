<?php
// Our include
define('WP_USE_THEMES', false);
require_once('../../../wp-load.php');

// Our variables
$numPosts = (isset($_GET['numPosts'])) ? $_GET['numPosts'] : 0;

echo $numPosts;
// echo $page;

query_posts(array(
       'posts_per_page' => $numPosts,
       'post_type'      => 'fast_fact'
));

$fast_fact_text = array();

//colors: blue, gold, green, blue
$background_colors = array("blue", "gold", "green", "orange");
//$rand_keys = array_rand($background_colors, 1);

$background_color_count = 0;
$fast_fact_counter = 0;

query_posts('post_type="fast_fact"&posts_per_page=100&orderby=date&order=ASC');
if (have_posts()) : 
	while (have_posts()) : the_post(); 
		$fast_fact_text[$fast_fact_counter] = '<div id="fast-fact-' . $fast_fact_counter . '" class="fast-fact" style="display:none;background-image: url(/wp-content/themes/our-new-tenet/images/bkgd-fastfact-' .  $background_colors[$background_color_count] . '.png)"><span class="fast-fact-content">' . get_the_content() . '</span></div>';
		
		if ($background_color_count >= 3) {
		  $background_color_count = 0;
		} else {
		  $background_color_count++;
		}
		$fast_fact_counter++;
	endwhile;
endif; 
wp_reset_query();

echo print_r($fast_fact_text);
?>