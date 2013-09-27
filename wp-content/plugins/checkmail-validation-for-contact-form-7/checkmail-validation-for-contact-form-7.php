<?php
/* 
Plugin Name: Checkmail Validation for Contact Form 7
Description: This plugin add checkmail field validation in Contact Form 7
Author: Ipalo
Version: 0.2
Author URI: http://www.ipalo.it
Plugin URI: http://wordpress.org/extend/plugins/checkmail-validation-for-contact-form-7/
*/

/*  Copyright 2012 Ipalo (email: paolo@ipalo.it)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
*/


add_action('plugins_loaded', 'contact_form_7_checkmail', 10);

function contact_form_7_checkmail() {
	global $pagenow;
	if(function_exists('wpcf7_add_shortcode')) {
		wpcf7_add_shortcode( 'checkmail', 'wpcf7_checkmail_shortcode_handler', true );
		add_filter( 'wpcf7_validate_checkmail', 'wpcf7_checkmail_validation_filter', 10, 2 );
		add_action( 'admin_init', 'wpcf7_add_tag_generator_checkmail', 30 );
	} else {
		if($pagenow != 'plugins.php') { return; }
		add_action('admin_notices', 'cfcheckmailfieldserror');
		wp_enqueue_script('thickbox');
		function cfcheckmailfieldserror() {
			$out = '<div class="error" id="messages"><p>';
			$out .= 'The Contact Form 7 plugin must be installed and activated for the Checkmail Validation for Contact Form 7 to work. <a href="'.admin_url('plugin-install.php?tab=plugin-information&plugin=contact-form-7&from=plugins&TB_iframe=true&width=600&height=550').'" class="thickbox" title="Contact Form 7">Install Now.</a>';
			$out .= '</p></div>';
			echo $out;
		}
	}
}



function wpcf7_checkmail_shortcode_handler( $tag ) {
	if ( ! is_array( $tag ) )
		return '';

	$type = $tag['type'];
	$name = $tag['name'];
	$options = (array) $tag['options'];
	$values = (array) $tag['values'];

	if ( empty( $name ) )
		return '';

	$validation_error = wpcf7_get_validation_error( $name );

	$atts = $id_att = $size_att = $maxlength_att = '';
	$tabindex_att = $title_att = '';

	$class_att = wpcf7_form_controls_class( $type, 'wpcf7-checkmail' );

	if ( 'checkmail' == $type )
		$class_att .= ' wpcf7-validates-as-email wpcf7-validates-as-required';

	if ( $validation_error )
		$class_att .= ' wpcf7-not-valid';
	

	foreach ( $options as $option ) {
		if ( preg_match( '%^id:([-0-9a-zA-Z_]+)$%', $option, $matches ) ) {
			$id_att = $matches[1];

		} elseif ( preg_match( '%^class:([-0-9a-zA-Z_]+)$%', $option, $matches ) ) {
			$class_att .= ' ' . $matches[1];
		}
	}

	$value = (string) reset( $values );

	if ( wpcf7_is_posted() )
		$value = stripslashes_deep( $_POST[$name] );

	if ( $id_att ) {
		$id_att = trim( $id_att );
		$atts .= ' id="' . trim( $id_att ) . '"';
	}
	if ( $class_att )
		$atts .= ' class="' . trim( $class_att ) . '"';
		
	if ( $size_att )
		$atts .= ' size="' . $size_att . '"';
	else
		$atts .= ' size="40"'; // default size

	global $post;
	if(is_object($post)) {
		if (strtolower($value) == 'post_title' || strtolower($value) == 'post-title') { $value = $post->post_title; }
		if (strtolower($value) == 'post_url') { $value = $post->guid; }
		if (strtolower($value) == 'post_category') {
			$categories = get_the_category();$catnames = array();
			foreach($categories as $cat) { $catnames[] = $cat->cat_name; }
			if(is_array($catnames)) { $value = implode(', ', $catnames); }
		}
		if (strtolower($value) == 'post_author') { $value = $post->post_author; }
		if (strtolower($value) == 'post_date') { $value = $post->post_date; }
		if (preg_match('/^custom_field\-(.*?)$/ism', $value)) {
			$custom_field = preg_replace('/custom_field\-(.*?)/ism', '$1', $value);
			$value = get_post_meta($post->ID, $custom_field, true) ? get_post_meta($post->ID, $custom_field, true) : '';
		}
	}

	$value = apply_filters('wpcf7_checkmail_field_value', apply_filters('wpcf7_checkmail_field_value_'.$id_att, $value));

	$html = '<input type="text" name="checkmail_' . $name . '" value=""' . $atts . ' />';
	$html .= '<input type="hidden" name="' . $name . '" value="' . esc_attr( $value ) . '"' . $atts . ' />';
	$html = '<span class="wpcf7-form-control-wrap ' . $name . '">' . $html . $validation_error . '</span>';

	return $html;
}



function wpcf7_checkmail_validation_filter( $result, $tag ) {
	$type = $tag['type'];
	$name = $tag['name'];
	$values = $tag['values'];

	$_POST[$name] = trim( strtr( (string) $_POST[$name], "\n", " " ) );

	if ( 'checkmail' == $type ) {
		if ( '' == $_POST['checkmail_'.$name] ) {
			$result['valid'] = false;
			$result['reason'][$name] = wpcf7_get_message( 'invalid_required' );
		} elseif ( $_POST['checkmail_'.$name] != $_POST[$values[0]] ) {
			$result['valid'] = false;
			$result['reason'][$name] = wpcf7_get_message( 'invalid_email' );
		}
	}
	
	return $result;
}


function wpcf7_add_tag_generator_checkmail() {
	if(function_exists('wpcf7_add_tag_generator')) {
		wpcf7_add_tag_generator( 'checkmail', __( 'Checkmail', 'wpcf7' ), 'wpcf7-tg-pane-checkmail', 'wpcf7_tg_pane_checkmail' );
	}
}


function wpcf7_tg_pane_checkmail( &$contact_form ) {
?>
<div id="wpcf7-tg-pane-checkmail" class="hidden">
<form action="">

<table>
<tr><td><?php echo esc_html( __( 'Name', 'wpcf7' ) ); ?><br /><input type="text" name="name" class="tg-name oneline" /></td><td></td></tr>

<tr>
<td><code>id</code> (<?php echo esc_html( __( 'optional', 'wpcf7' ) ); ?>)<br />
<input type="text" name="id" class="idvalue oneline option" /></td>
</tr>

<tr>
<td><?php echo esc_html( __( 'Email field name to check', 'wpcf7' ) ); ?><br /><input type="text" name="values" class="oneline" /></td>

</tr>
</table>

<div class="tg-tag"><?php echo esc_html( __( "Copy this code and paste it into the form left.", 'wpcf7' ) ); ?><br /><input type="text" name="checkmail" class="tag" readonly="readonly" onfocus="this.select()" /></div>

<div class="tg-mail-tag"><?php echo esc_html( __( "And, put this code into the Mail fields below.", 'wpcf7' ) ); ?><br /><span class="arrow">&#11015;</span>&nbsp;<input type="text" class="mail-tag" readonly="readonly" onfocus="this.select()" /></div>
</form>
</div>
<?php
}

?>