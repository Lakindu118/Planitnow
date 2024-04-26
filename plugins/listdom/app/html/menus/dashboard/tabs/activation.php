<?php
// no direct access
defined('ABSPATH') || die();

/** @var array $products */
?>
<div class="lsd-activation-wrap">
    <?php foreach($products as $key => $product): ?>
    <?php
        $licensing = $product['licensing'] ?? null;
        if(!$licensing) continue;

        $valid = $licensing->isLicenseValid();
    ?>
    <div class="lsd-accordion-title <?php echo $valid ? 'lsd-activation-valid' : ''; ?>">
        <div class="lsd-form-row">
            <div class="lsd-col-11">
                <h3><?php echo esc_html($product['name']); ?><?php echo $valid ? LSD_Kses::element('<i class="lsd-icon fa fa-check lsd-ml-3"></i>') : ''; ?></h3>
            </div>
            <div class="lsd-col-1 lsd-accordion-icons">
                <i class="lsd-icon fa fa-plus"></i>
                <i class="lsd-icon fa fa-minus"></i>
            </div>
        </div>
    </div>
    <div class="lsd-activation-form-group lsd-accordion-panel <?php echo $valid ? 'lsd-activation-valid' : ''; ?>">
        <?php if(!$valid): ?>
        <form class="lsd-activation-form" data-key="<?php echo esc_attr($key); ?>">
            <div class="lsd-form-row lsd-mt-0">
                <div class="lsd-col-2"><?php echo LSD_Form::label([
                    'title' => esc_html__('License Key', 'listdom'),
                    'for' => $key.'_license_key',
                ]); ?></div>
                <div class="lsd-col-4">
                    <?php echo LSD_Form::text([
                        'id' => $key.'_license_key',
                        'name' => 'license_key',
                        'value' => $licensing->getLicenseKey()
                    ]); ?>
                    <p class="description lsd-mb-0"><?php esc_html_e("License Key / Purchase Code is required for functionality, auto update, and customer service!", 'listdom'); ?></p>
                </div>
                <div class="lsd-col-1 lsd-text-right">
                    <?php echo LSD_Form::hidden([
                        'name' => 'key',
                        'value' => $key
                    ]); ?>
                    <?php echo LSD_Form::hidden([
                        'name' => 'basename',
                        'value' => $licensing->getBasename()
                    ]); ?>
                    <?php LSD_Form::nonce($key.'_activation_form'); ?>
                    <?php echo LSD_Form::submit([
                        'label' => esc_html__('Activate', 'listdom'),
                        'id' => $key.'_activation_button'
                    ]); ?>
                </div>
            </div>
            <div class="lsd-form-row lsd-mb-0">
                <div class="lsd-col-12" id="<?php echo esc_attr($key); ?>_activation_alert"></div>
            </div>
        </form>
        <?php else: ?>
        <div>
            <h3 class="lsd-mt-0 lsd-mb-2"><?php echo sprintf(esc_html__('License Key: %s', 'listdom'), '<code>'.$licensing->getLicenseKey().'</code>'); ?></h3>
            <p class="description lsd-mb-0"><?php esc_html_e("This installation is activated so you will receive automatic updates on your website!", 'listdom'); ?></p>
        </div>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>
</div>
<script>
jQuery('.lsd-activation-form').on('submit', function(event)
{
    event.preventDefault();

    // Form
    const $form = jQuery(this);

    const key = $form.data('key');

    // DOM Elements
    const $alert = jQuery(`#${key}_activation_alert`);
    const $button = jQuery(`#${key}_activation_button`);

    // Remove Existing Alert
    $alert.removeClass('lsd-error lsd-success lsd-alert').html('');

    // Add loading Class to the button
    $button.addClass('loading').html('<i class="lsd-icon fa fa-spinner fa-pulse fa-fw"></i>');

    const activation = $form.serialize();
    jQuery.ajax(
    {
        type: "POST",
        url: ajaxurl,
        data: "action=lsd_activation&" + activation,
        dataType: "json",
        success: function(response)
        {
            if(response.success)
            {
                $alert.removeClass('lsd-error lsd-success lsd-alert').addClass('lsd-alert lsd-success').html(response.message);
                $button.hide();
            }
            else
            {
                $alert.removeClass('lsd-error lsd-success lsd-alert').addClass('lsd-alert lsd-error').html(response.message);
            }

            // Remove loading Class from the button
            $button.removeClass('loading').html("<?php echo esc_js(esc_attr__('Activate', 'listdom')); ?>");
        },
        error: function()
        {
            // Remove loading Class from the button
            $button.removeClass('loading').html("<?php echo esc_js(esc_attr__('Activate', 'listdom')); ?>");
        }
    });
});
</script>