<?php

get_header();
get_sidebar('page');
st_before_content($columns='');
echo do_shortcode('[fast_facts]');
get_template_part( 'loop', 'attachment' );
st_after_content();
get_footer();

/* Failed experiment adding menus - might be useful later
wp_nav_menu(  array( 'theme_location' => 'side_nav' ) );
*/
?>