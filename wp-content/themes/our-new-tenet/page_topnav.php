<?php
/**
 * Template Name: Top Nav
 * Description: Template for pages using top navigation
 * Originally copied from page.php from skeleton theme
 *
 * This is the template that displays all pages by default.
 * Please note that this is the wordpress construct of pages
 * and that other 'pages' on your wordpress site will use a
 * different template.
 *
 * @package Skeleton WordPress Theme Framework
 * @subpackage skeleton
 * @author Simple Themes - www.simplethemes.com
 */
// You can override via functions.php conditionals or define:
// $columns = 'four';

get_header();
st_before_content($columns='');
get_template_part( 'loop', 'page' );
st_after_content();
get_sidebar('page');
get_footer();

/* Failed experiment adding menus - might be useful later
wp_nav_menu(  array( 'theme_location' => 'top_nav' ) );
*/

?>