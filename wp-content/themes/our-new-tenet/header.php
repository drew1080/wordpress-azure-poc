<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package Skeleton WordPress Theme Framework
 * @subpackage skeleton
 * @author Simple Themes - www.simplethemes.com
 */
?>
<!doctype html>
<!--[if lt IE 7 ]><html class="ie ie6" <?php language_attributes();?>> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" <?php language_attributes();?>> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" <?php language_attributes();?>> <![endif]-->
<!--[if IE 9 ]><html class="ie ie9" <?php language_attributes();?>> <![endif]-->
<!--[if (gte IE 10)|!(IE)]><!--><html <?php language_attributes();?>> <!--<![endif]-->

<head>
<!--TEST BRANCH-->
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

<title><?php
	// Detect Yoast SEO Plugin
	if (defined('WPSEO_VERSION')) {
		wp_title('');
	} else {
	/*
	 * Print the <title> tag based on what is being viewed.
	 */
	global $page, $paged;

	wp_title( '|', true, 'right' );

	// Add the blog name.
	bloginfo( 'name' );

	// Add the blog description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		echo " | $site_description";

	// Add a page number if necessary:
	if ( $paged >= 2 || $page >= 2 )
		echo ' | ' . sprintf( __( 'Page %s', 'skeleton' ), max( $paged, $page ) );
	}
	?>
</title>

<link rel="profile" href="http://gmpg.org/xfn/11" />

<!--[if lt IE 9]>
	<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->

<!-- Add CSS3 Rules here for IE 7-9
================================================== -->

<!--[if IE]>
<style type="text/css">
html.ie #navigation,
html.ie a.button,
html.ie .cta,
html.ie .wp-caption,
html.ie #breadcrumbs,
html.ie a.more-link,
html.ie .gallery .gallery-item img,
html.ie .gallery .gallery-item img.thumbnail,
html.ie .widget-container
</style>
<![endif]-->


<!-- Mobile Specific Metas
================================================== -->

<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" /> 

<!-- Favicons
================================================== -->

<link rel="apple-touch-icon" href="<?php echo get_stylesheet_directory_uri();?>/images/apple-touch-icon.png">

<link rel="apple-touch-icon" sizes="72x72" href="<?php echo get_stylesheet_directory_uri();?>/images/apple-touch-icon-72x72.png" />

<link rel="apple-touch-icon" sizes="114x114" href="<?php echo get_stylesheet_directory_uri();?>/images/apple-touch-icon-114x114.png" />

<link rel="pingback" href="<?php echo get_option('siteurl') .'/xmlrpc.php';?>" />

<link rel="stylesheet" id="bootstrap" href="<?php echo home_url() .'/wp-content/themes/our-new-tenet/util/bootstrap/css/bootstrap.min.css';?>" type="text/css" media="all" />

<link rel="stylesheet" id="bootstrap-responsive" href="<?php echo home_url() .'/wp-content/themes/our-new-tenet/util/bootstrap/css/bootstrap-responsive.min.css';?>" type="text/css" media="all" />

<link rel="stylesheet" id="custom" href="<?php echo home_url() .'/?get_styles=css';?>" type="text/css" media="all" />

<?php
	/* 
	 * enqueue threaded comments support.
	 */
	if ( is_singular() && get_option( 'thread_comments' ) )
		wp_enqueue_script( 'comment-reply' );
	// Load head elements
	wp_head();
?>

<script type="text/javascript" src="<?php echo home_url(); ?>/wp-content/themes/our-new-tenet/js/custom.js"></script>
<script type="text/javascript" src="<?php echo home_url(); ?>/wp-content/themes/our-new-tenet/util/bootstrap/js/bootstrap.min.js"></script>


</head>
<body <?php body_class(); ?>>
	<div id="wrap" class="container">
	<div class="resize"></div>
	<div class="header-wrap">
	  <?php
    st_above_header();
    st_header();
    st_below_header();
  	?>
  	<?php 
  	st_navbar();
  	?>
  	
  	<?php if ( is_user_logged_in () ) { ?>
  	<div class="search-form">
  		<?php get_search_form(); ?>
  	</div>
	  <?php } ?>  
	</div>
	
	<div class="navbar navbar-inverse">
    <div class="navbar-inner">
      <div class="container-fluid">
        <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
         <span class="icon-bar"></span>
         <span class="icon-bar"></span>
         <span class="icon-bar"></span>
        </a>
        <div class="nav-collapse collapse">
          <?php
          
            if ( is_front_page() ){
              $defaults = array(
              	'menu'            => 'top-nav-responsive',
              	'menu_class'      => 'nav',
              	'echo'            => true,
              	'items_wrap'      => '<ul id="%1$s" class="%2$s">%3$s</ul>',
              	'depth'           => 0
              );
              wp_nav_menu( $defaults );
            } else if ( is_user_logged_in() ) {
              $defaults = array(
              	'menu'            => 'side-nav-responsive',
              	'menu_class'      => 'nav',
              	'echo'            => true,
              	'items_wrap'      => '<ul id="%1$s" class="%2$s">%3$s</ul>',
              	'depth'           => 0
              );
              wp_nav_menu( $defaults );
            } else {
              $defaults = array(
              	'menu'            => 'top-nav-responsive',
              	'menu_class'      => 'nav',
              	'echo'            => true,
              	'items_wrap'      => '<ul id="%1$s" class="%2$s">%3$s</ul>',
              	'depth'           => 0
              );
              wp_nav_menu( $defaults );
            }
            
            
          ?>  
        </div><!-- /.nav-collapse -->
      </div><!-- /.container -->
    </div><!-- /.navbar-inner -->
  </div><!-- /.navbar -->
	
	<?php
	// Check if this is a post or page, if it has a thumbnail, and if it exceeds defined HEADER_IMAGE_WIDTH
	if ( is_singular() && current_theme_supports( 'post-thumbnails' ) && has_post_thumbnail( $post->ID ) 
	&& ( /* $src, $width, $height */
	$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'post-thumbnail' ))
	&&
	$image[1] >= HEADER_IMAGE_WIDTH ) :
	// Houston, we have a new header image!
	$image_attr = array(
				'class'	=> "scale-with-grid",
				'alt'	=> trim(strip_tags( $attachment->post_excerpt )),
				'title'	=> trim(strip_tags( $attachment->post_title ))
				);
	echo '<div id="header_image" class="row sixteen columns">'.get_the_post_thumbnail( $post->ID, array("HEADER_IMAGE_WIDTH","HEADER_IMAGE_HEIGHT"), $image_attr ).'</div>';
	elseif ( get_header_image() ) : ?>
		<div id="header_image" class="row sixteen columns"><img class="scale-with-grid round" src="<?php header_image(); ?>" alt="" /></div>
	<?php endif; ?>