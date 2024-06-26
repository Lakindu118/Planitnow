<?php
// no direct access
defined('ABSPATH') || die();
?>
<div class="lsd-dummy-data-wrap">
    <form id="lsd_dummy_data_form">
        <h3><?php esc_html_e('Dummy Data', 'listdom'); ?></h3>
        <p class="description"><?php esc_html_e("Do you need dummy data? We can import sample categories, tags, labels, locations, shortcodes etc for you. You're able to remove them if you don't need them!", 'listdom'); ?></p>
        <div class="lsd-form-row lsd-mt-4">
            <div class="lsd-col-12">
                <?php LSD_Form::nonce('lsd_dummy_data_form'); ?>
                <?php echo LSD_Form::submit([
                    'label' => esc_html__('Import Dummy Data', 'listdom'),
                    'id' => 'lsd_dummy_data_save_button',
                    'class' => 'button button-primary button-hero'
                ]); ?>
            </div>
        </div>
        <div class="lsd-util-hide" id="lsd_success_message">
            <div class="lsd-form-row lsd-mt-3">
                <div class="lsd-col-12">
                    <?php echo LSD_Base::alert(esc_html__('Dummy Data imported completely.', 'listdom'), 'success'); ?>
                </div>
            </div>
        </div>
    </form>
</div>
<script>
jQuery('#lsd_dummy_data_form').on('submit', function(event)
{
    event.preventDefault();

    // Hide Message
    jQuery('#lsd_success_message').addClass('lsd-util-hide');

    // Add loading Class to the button
    jQuery("#lsd_dummy_data_save_button").addClass('loading').html('<i class="lsd-icon fa fa-spinner fa-pulse fa-fw"></i>');

    var dummy = jQuery("#lsd_dummy_data_form").serialize();
    jQuery.ajax(
    {
        type: "POST",
        url: ajaxurl,
        data: "action=lsd_dummy&" + dummy,
        success: function()
        {
            // Show Message
            jQuery('#lsd_success_message').removeClass('lsd-util-hide');

            // Remove loading Class from the button
            jQuery("#lsd_dummy_data_save_button").removeClass('loading').html("<?php echo esc_js(esc_attr__('Import Dummy Data', 'listdom')); ?>");
        },
        error: function()
        {
            // Remove loading Class from the button
            jQuery("#lsd_dummy_data_save_button").removeClass('loading').html("<?php echo esc_js(esc_attr__('Import Dummy Data', 'listdom')); ?>");
        }
    });
});
</script>