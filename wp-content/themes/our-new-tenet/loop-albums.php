<?php
/**
 * The loop that displays a page.
 *
 * The loop displays the posts and the post content.  See
 * http://codex.wordpress.org/The_Loop to understand it and
 * http://codex.wordpress.org/Template_Tags to understand
 * the tags used in it.
 *
 * This can be overridden in child themes with loop-page.php.
 *
 * @package Skeleton WordPress Theme Framework
 * @subpackage skeleton
 * @author Simple Themes - www.simplethemes.com
 */
?>

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

				<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
								
				<?php if (!is_page_template('onecolumn-page.php')) { ?>
					<?php if (is_front_page() && !get_post_meta($post->ID, 'hidetitle', true)) { ?>
						
						<h2 class="entry-title"><?php the_title(); ?></h2>
						
					<?php } elseif (!get_post_meta($post->ID, 'hidetitle', true)) { ?>
						
						<h1 class="entry-title"><?php the_title(); ?></h1><?php if (is_page('faq') ) {echo do_shortcode('[submit_a_question]'); } ?> 
						
					<?php } else {
						echo '<br />';
					} ?>
				<?php } ?>
				
					<div class="entry-content">
						<?php the_content(); ?>
            
            <?php $pages = get_pages(array('child_of' => $post->ID, 'sort_order' => 'ASC', 'sort_column' => 'menu_order')); ?> 
            <?php foreach ($pages as $page): ?>
              <div class="wp-caption alignleft custom-thumbnails">
                <a href="<?php echo get_permalink($page->ID) ?>"><?php echo get_the_post_thumbnail($page->ID, 'thumbnail'); ?>
                <p class="wp-caption-text"><?php echo $page->post_title; ?></p></a>
              </div>
            <?php endforeach; ?>
            
            
						<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'skeleton' ), 'after' => '</div>' ) ); ?>
						<?php edit_post_link( __( 'Edit', 'skeleton' ), '<span class="edit-link">', '</span>' ); ?>
					</div><!-- .entry-content -->
				</div><!-- #post-## -->

				<?php comments_template( '', true ); ?>

<?php endwhile; // end of the loop. ?>