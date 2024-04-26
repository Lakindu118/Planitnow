<?php
// no direct access
defined('ABSPATH') || die();

/** @var LSD_PTypes_Shortcode $this */
/** @var WP_Post $post */
?>
<div class="lsd-metabox lsd-metabox-shortcode">
    <div class="lsd-shortcode"><?php echo '[listdom id="'.esc_html($post->ID).'"]'; ?></div>
    <p class="description"><?php esc_html_e("Insert this shortcode inside of your desired pages for showing filtered listings with your selected style into the page.", 'listdom'); ?></p>
    <?php /* Security Nonce */ LSD_Form::nonce('lsd_shortcode_cpt', '_lsdnonce'); ?>
</div>