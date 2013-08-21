<?php
/**
Plugin Name: Simple Skin for WP slider
**/
?>
<div id="slider_<?php echo $slider_id ?>">
<ul>
<?php foreach( $slides as $slide ): ?>
<li>
<?php
if ($slide['type'] == 'image'){
// var_dump($slide);?>
<img src="<?php echo $slide['image']['url'] ?>" />
<?php
} else {
echo $slide['html'];
}
?>
</li>
<?php endforeach; ?>
</ul>
</div>

<script>

jQuery(function ($) {
id = "<?php echo esc_js( $slider_id ) ?>";
    options = <?php echo json_encode( $slider_settings ) ?>;
    slider_cont = $( '#slider_' + id );
    slider_cont.flexslider();
});

</script>