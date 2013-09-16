<?php
include("functions/integration_update_widget.php");

add_shortcode('fast_facts', 'fast_facts_func');

/* Some code taken from the following:

Plugin Name: Peter's Redirect On First Login (user-meta based)
Description: Standalone functionality to redirect users to a special page on their first login(s)
Author: Peter
Version: 1.0
Author URI: http://www.theblog.ca/wordpress-redirect-first-login

*/

// Send new users to a special page
function redirectOnFirstLogin( $redirect_to, $requested_redirect_to, $user ) {
    // URL to redirect to
    $redirect_url = '/first-time/';
    // How many times to redirect the user
    $num_redirects = 1;

    // If they're on the login page, don't do anything
    if( !isset( $user->user_login ) ) {
        return $redirect_to;
    }

    $key_name = 'redirect_on_first_login';
    // Third parameter ensures that the result is a string
    $current_redirect_value = get_user_meta( $user->ID, $key_name, true );
    if( '' == $current_redirect_value || intval( $current_redirect_value ) < $num_redirects )
    {
        if( '' != $current_redirect_value )
        {
            $num_redirects = intval( $current_redirect_value ) + 1;
        }
        update_user_meta( $user->ID, $key_name, $num_redirects );
        return $redirect_url;
    }
    else
    {
        return $redirect_to;
    }
}

add_filter( 'login_redirect', 'redirectOnFirstLogin', 10, 3 );

// Hide the admin bar for users who cannot edit posts
add_action('set_current_user', 'csstricks_hide_admin_bar');
function csstricks_hide_admin_bar() {
  if (!current_user_can('edit_posts')) {
    show_admin_bar(false);
  }
}

function logged_in_logo_redirect( $prev_logo) {
	// Displays H1 or DIV based on whether we are on the home page or not (SEO)
	$heading_tag = ( is_home() || is_front_page() ) ? 'h1' : 'div';
	if (of_get_option('use_logo_image')) {
		$class="graphic";
	} else {
		$class="text"; 		
	}
	$home_url = esc_url( home_url( '/' ) );
	// if ( is_user_logged_in() ) { 
	//     $home_url = esc_url( home_url( '/employees/' ) );
	//   }
	// echo of_get_option('header_logo')
	$child_logo  = '<'.$heading_tag.' id="site-title" class="'.$class.'"><a href="'.$home_url.'" title="'.esc_attr( get_bloginfo('name','display')).'">'.get_bloginfo('name').'</a></'.$heading_tag.'>'. "\n";
	$child_logo .= '<span class="site-desc '.$class.'">'.get_bloginfo('description').'</span>'. "\n";
	return $child_logo;
}

add_filter('child_logo','logged_in_logo_redirect');


function st_footer() {
  	//loads sidebar-footer.php
  	get_sidebar( 'footer' );
  	// prints site credits
  	echo '<div id="credits">';
  	echo of_get_option('footer_text');
  	//echo '<br /><a class="themeauthor" href="http://www.simplethemes.com" title="Simple WordPress Themes">WordPress Themes</a></div>';
}
  
//remove_action('wp_footer', 'st_footer');
add_action('wp_footer', 'st_footer');


/**
 * Authenticate the user using the username and password.
 */
add_filter('authenticate', 'wp_authenticate_username_password_custom', 20, 3);
function wp_authenticate_username_password_custom($user, $username, $password) {
	
	/* Funky login method. Username is actually given username concated with given pass. Password is hardcoded. 
	   This is to allow people to use userID = last 4 of social, pass = DOB in MMDDYYYY format, while underlying username is really concat of those 2 things, to provide more uniqueness. 
	*/ 
	$username .= $password;
	$password = "Tenet1!";
	
	if ( is_a($user, 'WP_User') ) { return $user; }

	if ( empty($username) || empty($password) ) {
		$error = new WP_Error();

		if ( empty($username) )
			$error->add('empty_username', __('<strong>ERROR</strong>: The username field is empty.'));

		if ( empty($password) )
			$error->add('empty_password', __('<strong>ERROR</strong>: The password field is empty.'));

		return $error;
	}

	$user = get_user_by('login', $username);

	if ( !$user )
		return new WP_Error( 'invalid_username', sprintf( __( '<strong>ERROR</strong>: Invalid username. <a href="%s" title="Password Lost and Found">Lost your password</a>?' ), wp_lostpassword_url() ) );

	if ( is_multisite() ) {
		// Is user marked as spam?
		if ( 1 == $user->spam )
			return new WP_Error( 'spammer_account', __( '<strong>ERROR</strong>: Your account has been marked as a spammer.' ) );

		// Is a user's blog marked as spam?
		if ( !is_super_admin( $user->ID ) && isset( $user->primary_blog ) ) {
			$details = get_blog_details( $user->primary_blog );
			if ( is_object( $details ) && $details->spam == 1 )
				return new WP_Error( 'blog_suspended', __( 'Site Suspended.' ) );
		}
	}
  
	$user = apply_filters('wp_authenticate_user', $user, $password);
	if ( is_wp_error($user) )
		return $user;

	if ( !wp_check_password($password, $user->user_pass, $user->ID) )
		return new WP_Error( 'incorrect_password', sprintf( __( '<strong>ERROR</strong>: The password you entered for the username <strong>%1$s</strong> is incorrect. <a href="%2$s" title="Password Lost and Found">Lost your password</a>?' ),
		$username, wp_lostpassword_url() ) );

	return $user;
}

if (!is_admin()) add_action("wp_enqueue_scripts", "my_jquery_enqueue", 11);
function my_jquery_enqueue() {
   wp_deregister_script('jquery');
   wp_register_script('jquery', "http" . ($_SERVER['SERVER_PORT'] == 443 ? "s" : "") . "://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js", false, null);
   wp_enqueue_script('jquery');
}

/* failed experiment adding menus per template - might be useful later.

* Register nav menus

function register_my_menus() {

    register_nav_menus( 
		array(
		'side_nav' => 'Side Navigation',
		'top_nav' => 'Top Navigation'
		) 
	);
}

add_action( 'init', 'register_my_menus' );
*/

if (!function_exists('st_before_footer'))  {
    function st_before_footer() {
			$footerwidgets = is_active_sidebar('first-footer-widget-area') + is_active_sidebar('second-footer-widget-area') + is_active_sidebar('third-footer-widget-area') + is_active_sidebar('fourth-footer-widget-area');
			$class = ($footerwidgets == '0' ? 'noborder' : 'normal');
			echo "</div><!--/#wrap.container-->"."\n";
			echo '<div class="clear"></div><div id="footer" class="'.$class.' sixteen columns">';
    }
}

if (!function_exists('st_after_footer'))  {
	
    function st_after_footer() {
			echo "</div><!--/#footer-->"."\n";
			// Google Analytics
			if (of_get_option('footer_scripts') <> "" ) {
				echo '<script type="text/javascript">'.stripslashes(of_get_option('footer_scripts')).'</script>';
			}
    }
}



function create_integration_update_init()
{
    $args = array(
        'public' => true,
        'label' => 'Integration Updates',
        'singular_label' => 'Integration Update',
        'supports' => array('title','editor')
     );
    register_post_type('integration_update', $args);
}

// Disabling the technology_item post type
add_action('init', 'create_integration_update_init');

function create_fast_fact_init()
{
    $args = array(
        'public' => true,
        'label' => 'Fast Facts',
        'singular_label' => 'Fast Fact',
        'supports' => array('title','editor')
     );
    register_post_type('fast_fact', $args);
}

// Disabling the technology_item post type
add_action('init', 'create_fast_fact_init');


function fast_facts_func($atts, $content = null) {
  $fast_fact_text = '';
  
  //colors: blue, gold, green, blue
  $background_colors = array("blue", "gold", "green", "blue");
  $rand_keys = array_rand($background_colors, 2);
  
  query_posts('post_type="fast_fact"&posts_per_page=1&orderby=rand');
  if (have_posts()) : 
  	while (have_posts()) : the_post(); 
  		$fast_fact_text = '<div class="fast-fact" style="background-image: url(/wp-content/themes/our-new-tenet/images/bkgd-fastfact-' .  $background_colors[$rand_keys[0]] . '.png)"><span class="fast-fact-content">' . get_the_content() . '</span></div>';
  	endwhile;
  endif; 
  wp_reset_query();
  
  $html = '<div class="slider ' . $has_excerpt_class . ' ' . $class . '" style="background-image: url(' . $image_url . '); background-repeat: no-repeat;">
	          <div class="slider-content" >
    			    <div class="inner-content">
    			      <h1 class="entry-title">' . $title . '</h1>
        			  <p>' . $excerpt . '</p>
      			  </div>
            </div>
            <div class="clear"></div>
          </div>';

	return $fast_fact_text; 
}
?>