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

$fast_fact_text = '';

//colors: blue, gold, green, blue
$background_colors = array("blue", "gold", "green", "orange");
$rand_keys = array_rand($background_colors, 1);

query_posts('post_type="fast_fact"&posts_per_page=1&orderby=rand');
if (have_posts()) : 
	while (have_posts()) : the_post(); 
		$fast_fact_text = '<div id="fast-fact" class="fast-fact" style="background-image: url(/wp-content/themes/our-new-tenet/images/bkgd-fastfact-' .  $background_colors[$rand_keys] . '.png)"><span class="fast-fact-content">' . get_the_content() . '</span></div>';
	endwhile;
endif; 
wp_reset_query();

echo $fast_fact_text;
?>