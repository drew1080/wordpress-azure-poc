<?php
// Custom Functions for Our New Tenet

include("functions/integration_update_widget.php");

add_shortcode('fast_facts', 'fast_facts_func');
add_shortcode('submit_a_question', 'submit_a_question_func');

// Hide the admin bar
add_filter('show_admin_bar', '__return_false');

/* Redirect all non-logged-in users to the login page (private site). Add to functions.php. */
 
function admin_redirect() {
  if ( !is_front_page() && !is_page('contact-us') && !is_user_logged_in() 
    && !is_page('login-page') && !is_page('coming-soon') && !is_page('terms-of-use')) {
    wp_redirect( home_url('/wp-admin/') );
    exit;
  }
}
 
add_action('get_header', 'admin_redirect');


/* Some code taken from the following:

Plugin Name: Peter's Redirect On First Login (user-meta based)
Description: Standalone functionality to redirect users to a special page on their first login(s)
Author: Peter
Version: 1.0
Author URI: http://www.theblog.ca/wordpress-redirect-first-login

*/

//Login redirecting
function redirectOnFirstLogin( $redirect_to, $requested_redirect_to, $user ) {
    // URL to redirect to
    $redirect_url = '/employee-portal/'; 
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
	$home_url = esc_url( home_url() );
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
  $fast_fact_text = '<div id="fast-facts-wrapper">';
  
  //colors: blue, gold, green, blue
  $background_colors = array("gold", "green", "orange", "blue");
  //$rand_keys = array_rand($background_colors, 1);
  
  $selected_class = "selected";
  $display_none = "";
  $background_color_count = 0;
  $fast_fact_counter = 0;

  query_posts('post_type="fast_fact"&posts_per_page=100&orderby=date&order=DESC');
  if (have_posts()) : 
  	while (have_posts()) : the_post(); 
  		$fast_fact_text .= '<div id="fast-fact-' . $fast_fact_counter . '" class="fast-fact ' . $selected_class . '" style="' . $display_none . 'background-image: url(/wp-content/themes/our-new-tenet/images/fastfact-' .  $background_colors[$background_color_count] . '.png)"><span class="fast-fact-content">' . get_the_content() . '</span></div>';

  		if ($background_color_count >= 3) {
  		  $background_color_count = 0;
  		} else {
  		  $background_color_count++;
  		}
  		$fast_fact_counter++;
  		$display_none = "display:none;";
  	endwhile;
  endif; 
  wp_reset_query();
  
  $fast_fact_text .=  '</div>';

	return $fast_fact_text; 
}

function register_ajaxLoop_script() {
    wp_register_script(
       'ajaxLoop',
        get_stylesheet_directory_uri() . '/js/ajaxLoop.js',
        array('jquery'),
        NULL
    );
    wp_enqueue_script('ajaxLoop');
}
add_action('wp_enqueue_scripts', 'register_ajaxLoop_script');

function submit_a_question_func() {
  $html = '';
  
  $html = '<div class="submit-a-question"><a href="/submit-a-question "/submit-a-question">SUBMIT A QUESTION</a></div><div class="clear"></div>';
  
  return $html;
}

// Toggle

function toggle_content_func( $atts, $content = null ) {
	extract(shortcode_atts(array(
		 'title' => '',
		 'style' => 'list',
		 'new_image' => ''
    ), $atts));
	output;
	
	if ( $new_image != '' ) {
	  $output .= '<div class="'.$style.'"><p class="trigger new-faq"><span class="new-faq-wrap"><span class="new-toggle"></span></span><a href="#">' .$title. '</a></p>';
	} else {
	  $output .= '<div class="'.$style.'"><p class="trigger"><a href="#">' .$title. '</a></p>';
	}
	
	$output .= '<div class="toggle_container"><div class="block">';
	$output .= do_shortcode($content);
	$output .= '</div></div></div>';

	return $output;
}
add_shortcode('toggle_content', 'toggle_content_func');


// Login styling
function custom_login_head() { 
  //echo '<script type="text/javascript" src="' . home_url() . '/wp-content/themes/our-new-tenet/js/lib/jquery.html5-placeholder-shim.js"></script>';
  echo '<script type="text/javascript" src="' . home_url() . '/wp-content/themes/our-new-tenet/js/custom.js"></script>';
  echo '<link rel="stylesheet" type="text/css" href="' . get_stylesheet_directory_uri() . '/stylized-login.css" />';
  remove_action('login_head', 'wp_shake_js', 12);
}
add_action('login_head', 'custom_login_head');

//Use this for a custom login form...but it only appends to the current form
// function login_form_func() { 
//   $args = array(
//       'echo' => false,
//       'redirect' => admin_url(), 
//       'form_id' => 'loginform-custom',
//       'label_username' => __( 'Username custom text' ),
//       'label_password' => __( 'Password custom text' ),
//       'label_remember' => __( 'Remember Me custom text' ),
//       'label_log_in' => __( 'Log In custom text' ),
//       'remember' => true
//   );
//   //'<input type="text" name="log" id="user_login" class="input" value="" size="20">'
//   $login_form =  wp_login_form( $args );
//   $login_form =  str_replace('<input type="text" name="log" id="user_login" class="input" value="" size="20">', 
//                             '<input type="text" name="log" id="user_login" class="input" value="" size="20" placeholder="TEST">',
//                             $login_form);
//                             
//   $login_form =  str_replace('id="user_login"', 
//                             'id="user_login" placeholder="Last 4 digits of your SSN"',
//                             $login_form);
//                             
//                             
//   $login_form =  str_replace('id="user_pass"', 
//                             'id="user_pass" placeholder="Date of birth (mmddyyy)"',
//                             $login_form);
//   echo $login_form;
// }
// remove_action('login_form', 'wp_login_form');
// 
// add_action('login_form', 'login_form_func');

/*
function remove_subscribers() {
	global $wpdb;
	$args = array( 'role' => 'Subscriber' );
	$subscribers = get_users( $args );
	if( !empty($subscribers) ) {
		require_once( ABSPATH.'wp-admin/includes/user.php' );
		$i = 0;
		foreach( $subscribers as $subscriber ) {
			if( wp_delete_user( $subscriber->ID ) ) {
				$i++;
			}
		}
		echo $i.' Subscribers deleted';
	} else {
		echo 'No Subscribers deleted';
	}
}

remove_subscribers();
*/

function is_already_submitted($formName, $fieldName, $fieldValue){
    require_once(ABSPATH . 'wp-content/plugins/contact-form-7-to-database-extension/CFDBFormIterator.php');
    $exp = new CFDBFormIterator();
    $atts = array();
    $atts['show'] = $fieldName;
    $atts['filter'] = "$fieldName=$fieldValue";
    $exp->export($formName, $atts);
    $found = false;
    while ($row = $exp->nextRow()) {
        $found = true;
    }
    return $found;
}
 
function is_survey_taken_by_user($result) {
    // $formName = 'email_form'; // Name of the form containing this field
    // $fieldName = 'email_123'; // Set to your form's unique field name
    // $name = $tag['name'];
    // if($name == $fieldName){
    //     $valueToValidate = $_POST[$name];
    //     if (is_already_submitted($formName, $fieldName, $valueToValidate)) {
    //         $result['valid'] = false;
    //         $result['reason'][$name] = 'Email has already been submitted'; // error message
    //     }
    // }
    
    $formName = 'Survey'; // Name of the form containing this field
    $fieldName = 'Submitted Login'; // Set to your form's unique field name
    $valueToValidate = 'connorposke';
    
    if (is_already_submitted($formName, $fieldName, $valueToValidate)) {
        $result['valid'] = false;
        $result['reason'][$name] = 'Thanks for your response!'; // error message
    }
    
    return $result;
}

function my_tweaked_admin_bar() {
	
  // if ( is_user_logged_in() ) {
  //    global $user_login;
  //     get_currentuserinfo();
  // 
  //    $formName = 'Survey_copy'; // Name of the form containing this field
  //     $fieldName = 'Submitted Login'; // Set to your form's unique field name
  //     $valueToValidate = $user_login;
  // 
  //     if (is_already_submitted($formName, $fieldName, $valueToValidate)) {
  // 
  //    }
  // }
	
	echo "<script type='text/javascript'>
    window.onload=function(){
      if( jQuery('#credits .wpcf7').length == 0 ) {
        jQuery('.visiblebox').hide();
      }
    };
    </script>";
}

add_action( 'wp_loaded', 'my_tweaked_admin_bar' );

function user_last_login($login) {
    global $user_ID;
    $user = get_userdatabylogin($login);
    update_usermeta($user->ID, 'last_login', time());
}

add_action('wp_login','user_last_login');
 
//add_filter('wpcf7_validate_email*', 'my_validate_email', 10, 2);
//add_filter('wpcf7_validate_select*', 'is_survey_taken_by_user', 10, 2);
// add_action( 'wpcf7_contact_form', 'is_survey_taken_by_user' );


// global $wpcf7_contact_form;
// if ( ! ( $wpcf7_contact_form = wpcf7_contact_form( 1 ) ) )
// return 'Contact form not found!';
// $form = $wpcf7_contact_form->form_html();
// echo $form;

// add_action( 'plugins_loaded', 'wpcf7_add_shortcodes_custom', 1 );
// 
// function wpcf7_add_shortcodes_custom() {
//  remove_shortcode( 'contact-form-7', 'wpcf7_contact_form_tag_func' );
//  remove_shortcode( 'contact-form', 'wpcf7_contact_form_tag_func' );
//  
//   add_shortcode( 'contact-form-7', 'wpcf7_contact_form_tag_func_custom' );
//  add_shortcode( 'contact-form', 'wpcf7_contact_form_tag_func_custom' );
// }

add_filter('site_transient_update_plugins', 'dd_remove_update_nag');

function dd_remove_update_nag($value) {
  unset($value->response['contact-form-7/wp-contact-form-7.php']);

  return $value;
}

?>