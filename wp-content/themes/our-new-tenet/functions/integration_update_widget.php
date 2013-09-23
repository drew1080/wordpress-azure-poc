<?php
/**
 * Plugin Name: A simple Widget
 * Description: A widget that displays authors name.
 * Version: 0.1
 * Author: Bilal Shaheen
 * Author URI: http://gearaffiti.com/about
 */


add_action( 'widgets_init', 'integration_update_widget_init' );


function integration_update_widget_init() {
	register_widget( 'integration_update_widget' );
}

class Integration_Update_Widget extends WP_Widget {

	function Integration_Update_Widget() {
		$widget_ops = array( 'classname' => 'integration-update', 'description' => __('A widget that displays Integration Updates', 'integration-update') );
		
		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'integration-update-widget' );
		
		$this->WP_Widget( 'integration-update-widget', __('Integration Update Widget', 'example'), $widget_ops, $control_ops );
	}
	
	function widget( $args, $instance ) {
		extract( $args );

		//Our variables from the widget settings.
		$title = apply_filters('widget_title', $instance['title'] );
		$integration_update_count = $instance['integration_update_count'];

		echo $before_widget;

		// Display the widget title 
		if ( $title )
			echo $before_title . $title . $after_title;
			
		query_posts('post_type="integration_update"&posts_per_page=' . $integration_update_count . '&orderby=date&order=DESC');
    if (have_posts()) : 
    	while (have_posts()) : the_post(); 
    		the_title('<h3>', '</h3>');	
    		echo '<h4>' . get_the_date('m-d-Y') . '</h4>';
    		the_content();
    	endwhile;
    endif; 
    wp_reset_query();
		
		echo $after_widget;
	}

	//Update the widget 
	 
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		//Strip tags from title and name to remove HTML 
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['integration_update_count'] = strip_tags( $new_instance['integration_update_count'] );

		return $instance;
	}

	
	function form( $instance ) {

		//Set up some default widget settings.
		$defaults = array( 'title' => __('', 'integration-update-widget'), 'integration_update_count' => __('5', 'integration-update-widget') );
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'example'); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'integration_update_count' ); ?>"><?php _e('Integration Updates to show:', 'example'); ?></label>
			<input id="<?php echo $this->get_field_id( 'integration_update_count' ); ?>" name="<?php echo $this->get_field_name( 'integration_update_count' ); ?>" value="<?php echo $instance['integration_update_count']; ?>" style="width:100%;" />
		</p>

	<?php
	}
}

?>