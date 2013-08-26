<?php



/*
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
	if ( is_user_logged_in() ) { 
		$home_url = esc_url( home_url( '/employees/' ) );
	}
	// echo of_get_option('header_logo')
	$child_logo  = '<'.$heading_tag.' id="site-title" class="'.$class.'"><a href="'.$home_url.'" title="'.esc_attr( get_bloginfo('name','display')).'">'.get_bloginfo('name').'</a></'.$heading_tag.'>'. "\n";
	$child_logo .= '<span class="site-desc '.$class.'">'.get_bloginfo('description').'</span>'. "\n";
	return $child_logo;
}

add_filter('child_logo','logged_in_logo_redirect');

if ( !function_exists( 'st_footer' ) ) {	
  function st_footer() {
  	//loads sidebar-footer.php
  	get_sidebar( 'footer' );
  	// prints site credits
  	echo '<div id="credits">';
  	echo '<span>Copyright </span>';
    echo date("Y");
    echo ' | ';
  	echo of_get_option('footer_text');
  	echo '<br /><a class="themeauthor" href="http://www.simplethemes.com" title="Simple WordPress Themes">WordPress Themes</a></div>';
  }
  
  //remove_action('wp_footer', 'st_footer');
  add_action('wp_footer', 'st_footer');
}

?>