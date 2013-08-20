<?php
/**
Plugin Name: Default Skin
**/
?>

<div class="flexslider ssp_slider_default" id="slider_<?php echo esc_attr( $slider_id ) ?>" data-slider_id="<?php echo esc_attr( $slider_id ) ?>">

<ul class="slides ssp_slider wsp_default_skin">

<?php foreach( $slides as $slide ):

      if ( isset( $shortcode_atts['link_target'] ) )
        $target = $shortcode_atts['link_target'];
      else
        $target = "_self";
?>
  <li class="slide" data-thumb="<?php echo $slide['image']['sizes']['thumbnail'] ?>">

    <?php do_action( 'ssp_skin_slide_start' ) ?>
    
    <?php if ( $slider_settings['linkable'] ): ?>
      <?php if ( isset( $shortcode_atts['size'] ) ): ?>
        <a href="<?php echo $slide['image']['link'] ?>" target="<?php echo $target ?>"><img class="slide_image" src="<?php echo $slide['image']['sizes'][$shortcode_atts['size']] ?>" /></a>
      <?php else: ?>
        <a href="<?php echo $slide['image']['link'] ?>" target="<?php echo $target ?>"><img alt="<?php echo $slide['image']['alt'] ?>" class="slide_image" src="<?php echo $slide['image']['url']; ?>" /></a>
      <?php endif; ?>
    <?php else: ?>
      <?php if ( isset( $shortcode_atts['size'] ) ): ?>
        <img alt="<?php echo $slide['image']['alt'] ?>" class="slide_image" src="<?php echo $slide['image']['sizes'][$shortcode_atts['size']] ?>" />
      <?php else: ?>
        <img alt="<?php echo $slide['image']['alt'] ?>" class="slide_image" src="<?php echo $slide['image']['url']; ?>" />
      <?php endif; ?>
    <?php endif; ?>

    <?php if ( $slider_settings['caption_box'] ): ?>
      <p class="flex-caption">
        <?php if ( $slider_settings['linkable'] ): ?>
          <a href="<?php echo $slide['image']['link'] ?>">
            <strong><?php echo $slide['image']['caption'] ?></strong>
          </a>
        <?php else: ?>
          <strong>
            <?php echo $slide['image']['caption'] ?>
          </strong>
        <?php endif; ?>
      </p>
    <?php endif; ?>

    <?php do_action( 'ssp_skin_slide_end' ) ?>

  </li>
<?php endforeach; ?>

</ul>

</div>

<script type="text/javascript">
  
  jQuery(function ($) {
      
      $(window).load( function() {

        id = "<?php echo esc_js( $slider_id ) ?>";
        
        options = <?php echo json_encode( $slider_settings ) ?>;

        selector = $('div[data-slider_id=' + id + ' ]');

       // options.default_skin_theme = 'theme-wsp-default-skin-1';

        //if ( options.default_skin_theme )
          //selector.addClass( 'theme-wsp-default-skin-1' );

        height = options.height.replace(/[^\d.]/g, "");

        width = options.width.replace(/[^\d.]/g, "");
        
        if ( options.h_responsive == false || options.h_responsive == '' ) {
          
          $('.slides .slide', selector).each( function() {

            if ( ! Number( height ) <= 0 )
             $(this).css( 'height', height + 'px' );

          });
          
        }

        if ( options.w_responsive == false || options.w_responsive == '' ) {
          
          if ( ! Number( width ) <= 0 )
            $( selector ).css( 'width', width + 'px' );
        
        }

        if ( options.thumbnail_navigation )
          options.control_nav = 'thumbnails';
        
        $(selector).flexslider( {

          smoothHeight: options.h_responsive,

          animation: options.animation,
    
          direction: options.direction,

          slideshow: options.slideshow,

          slideshowSpeed: Number( options.cycle_speed ) * 1000,

          animationSpeed: Number( options.animation_speed ) * 1000,

          pauseOnHover: options.pause_on_hover,

          controlNav: options.control_nav,

          directionNav: options.direction_nav,

          keyboard: options.keyboard_nav,

          touch: options.touch_nav,

          before: function( slider ) {

            slider.removeClass('loading');
            
          }

        });

      });

  });

</script>