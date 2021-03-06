<?php
/**
 * Template Name: Side Nav for Executive Pages
 * Description: Template for pages with side navigation
 * Copied from page.php from the skeleton theme
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


    $roles      = array('executive');
    $in_role    = check_user_role($roles);
    
    if ($in_role) {
        get_sidebar('page');
        st_before_content($columns='');
        echo do_shortcode('[fast_facts]');
        get_template_part( 'loop', 'page' );
        st_after_content();
    }
    
    else {
        echo "Nice try! You do not have the necessary priveleges";
    }

get_footer();

/* Failed experiment adding menus - might be useful later
wp_nav_menu(  array( 'theme_location' => 'side_nav' ) );
*/
?>