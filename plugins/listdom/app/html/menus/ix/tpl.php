<?php
// no direct access
defined('ABSPATH') || die();

/** @var LSD_Menus_IX $this */
?>
<div class="wrap about-wrap lsd-wrap">
    <h1><?php esc_html_e('Import / Export', 'listdom'); ?></h1>
    <div class="about-text">
		<?php echo esc_html__("Simply import / export listings in your desired format!", 'listdom'); ?>
    </div>

    <?php LSD_Ads::display('ix-top'); ?>

    <?php
        // Upgrade Message
        if($this->isLite())
        {
            echo LSD_Base::alert($this->missFeatureMessage(esc_html__('Import / Export', 'listdom')), 'warning');
        }
        else
        {
            // IX Tabs
            $this->include_html_file('menus/ix/tabs.php');

            // IX Content
            $this->include_html_file('menus/ix/content.php');
        }
    ?>

    <?php LSD_Ads::display('ix-bottom'); ?>
    
</div>