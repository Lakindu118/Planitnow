<?php
// no direct access
defined('ABSPATH') || die();

/** @var LSD_Skins_Slider $this */

$ids = $this->listings;
?>
<div class="lsd-skin-<?php echo esc_attr($this->id); ?>-slider lsd-owl-carousel">
    <?php foreach($ids as $id): $listing = new LSD_Entity_Listing($id); ?>
    <div class="lsd-listing" <?php echo lsd_schema()->scope()->type(null, $listing->get_data_category()); ?>>
		<div class="lsd-listing-image">
            <?php echo LSD_Kses::element($listing->get_cover_image([1100, 550])); ?>

			<div class="lsd-listing-detail">
                <div class="lsd-listing-labels">
                    <?php echo LSD_Kses::element($listing->get_labels()); ?>
                </div>
                
				<h3 class="lsd-listing-title" <?php echo lsd_schema()->name(); ?>>
                    <?php echo LSD_Kses::element($this->get_title_tag($listing)); ?>
				</h3>
				
				<div class="lsd-listing-locations">
					<?php echo LSD_Kses::element($listing->get_locations()); ?>
				</div>
				
				<?php echo LSD_Kses::element($listing->get_rate_stars()); ?>
			</div>
        </div>
    </div>
    <?php endforeach; ?>
</div>