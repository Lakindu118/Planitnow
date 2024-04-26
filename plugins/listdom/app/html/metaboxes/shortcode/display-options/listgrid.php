<?php
// no direct access
defined('ABSPATH') || die();

/** @var LSD_PTypes_Shortcode $this */
/** @var array $options */

$listgrid = $options['listgrid'] ?? [];
?>
<div class="lsd-form-row lsd-form-row-separator"></div>
<div class="lsd-form-row">
    <div class="lsd-col-2"></div>
    <div class="lsd-col-10">
        <p class="description"><?php echo sprintf(esc_html__('Using %s skin, you can show a list and grid view of the listings. You can include Google Maps to the view as well.', 'listdom'), '<strong>'.esc_html__('List+Grid', 'listdom').'</strong>'); ?></p>
    </div>
</div>
<div class="lsd-form-row">
    <div class="lsd-col-2"><?php echo LSD_Form::label([
        'title' => esc_html__('Style', 'listdom'),
        'for' => 'lsd_display_options_skin_listgrid_style',
    ]); ?></div>
    <div class="lsd-col-6">
        <?php echo LSD_Form::select([
            'id' => 'lsd_display_options_skin_listgrid_style',
            'name' => 'lsd[display][listgrid][style]',
            'options' => LSD_Styles::listgrid(),
            'value' => $listgrid['style'] ?? 'style1'
        ]); ?>
    </div>
</div>
<div class="lsd-form-row">
    <div class="lsd-col-2"><?php echo LSD_Form::label([
        'title' => esc_html__('Map Provider', 'listdom'),
        'for' => 'lsd_display_options_skin_listgrid_map_provider',
    ]); ?></div>
    <div class="lsd-col-6">
        <?php echo LSD_Form::providers([
            'id' => 'lsd_display_options_skin_listgrid_map_provider',
            'name' => 'lsd[display][listgrid][map_provider]',
            'value' => $listgrid['map_provider'] ?? LSD_Map_Provider::def(),
            'disabled' => true,
            'class' => 'lsd-map-provider-toggle',
            'attributes' => [
                'data-parent' => '#lsd_skin_display_options_listgrid'
            ]
        ]); ?>
    </div>
</div>
<div class="lsd-form-group lsd-form-row-map-needed <?php echo ((isset($listgrid['map_provider']) and $listgrid['map_provider']) ? '' : 'lsd-util-hide'); ?>" id="lsd_display_options_skin_listgrid_map_options">
    <div class="lsd-form-row">
        <div class="lsd-col-2"><?php echo LSD_Form::label([
            'title' => esc_html__('Position', 'listdom'),
            'for' => 'lsd_display_options_skin_listgrid_map_position',
        ]); ?></div>
        <div class="lsd-col-6">
            <?php echo LSD_Form::select([
                'id' => 'lsd_display_options_skin_listgrid_map_position',
                'name' => 'lsd[display][listgrid][map_position]',
                'options' => [
                    'top' => esc_html__('Show before list + grid view', 'listdom'),
                    'bottom' => esc_html__('Show after list + grid view', 'listdom')
                ],
                'value' => $listgrid['map_position'] ?? 'top'
            ]); ?>
        </div>
    </div>
    <div class="lsd-form-row lsd-map-provider-dependency lsd-map-provider-dependency-googlemap">
        <div class="lsd-col-2"><?php echo LSD_Form::label([
            'title' => esc_html__('Map Style', 'listdom'),
            'for' => 'lsd_display_options_skin_listgrid_mapstyle',
        ]); ?></div>
        <div class="lsd-col-6">
            <?php echo LSD_Form::mapstyle([
                'id' => 'lsd_display_options_skin_listgrid_mapstyle',
                'name' => 'lsd[display][listgrid][mapstyle]',
                'value' => $listgrid['mapstyle'] ?? ''
            ]); ?>
        </div>
    </div>
    <div class="lsd-form-row">
        <div class="lsd-col-2"><?php echo LSD_Form::label([
            'title' => esc_html__('Clustering', 'listdom'),
            'for' => 'lsd_display_options_skin_listgrid_clustering',
        ]); ?></div>
        <div class="lsd-col-6">
            <?php echo LSD_Form::switcher([
                'id' => 'lsd_display_options_skin_listgrid_clustering',
                'toggle' => '#lsd_display_options_skin_listgrid_clustering_options',
                'name' => 'lsd[display][listgrid][clustering]',
                'value' => $listgrid['clustering'] ?? '1'
            ]); ?>
        </div>
    </div>
    <div class="lsd-map-provider-dependency lsd-map-provider-dependency-googlemap">
        <div id="lsd_display_options_skin_listgrid_clustering_options" <?php echo ((!isset($listgrid['clustering']) || $listgrid['clustering']) ? '' : 'style="display: none;"'); ?>>
            <div class="lsd-form-row">
                <div class="lsd-col-2"><?php echo LSD_Form::label([
                    'title' => esc_html__('Bubbles', 'listdom'),
                    'for' => 'lsd_display_options_skin_listgrid_clustering_images',
                ]); ?></div>
                <div class="lsd-col-6">
                    <?php echo LSD_Form::select([
                        'id' => 'lsd_display_options_skin_listgrid_clustering_images',
                        'name' => 'lsd[display][listgrid][clustering_images]',
                        'options' => LSD_Base::get_clustering_icons(),
                        'value' => $listgrid['clustering_images'] ?? 'img/cluster1/m'
                    ]); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="lsd-form-row">
        <div class="lsd-col-2"><?php echo LSD_Form::label([
            'title' => esc_html__('Marker/Shape On Click', 'listdom'),
            'for' => 'lsd_display_options_skin_listgrid_mapobject_onclick',
        ]); ?></div>
        <div class="lsd-col-6">
            <?php echo LSD_Form::select([
                'id' => 'lsd_display_options_skin_listgrid_mapobject_onclick',
                'name' => 'lsd[display][listgrid][mapobject_onclick]',
                'options' => [
                    'infowindow' => esc_html__('Open Infowindow', 'listdom'),
                    'redirect' => esc_html__('Redirect to Listing Details Page', 'listdom'),
                    'lightbox' => esc_html__('Open Listing Details in Lightbox', 'listdom'),
                    'none' => esc_html__('None', 'listdom')
                ],
                'value' => $listgrid['mapobject_onclick'] ?? 'infowindow'
            ]); ?>
            <p class="description"><?php esc_html_e("You can select to show an infowindow when someone clicks on Marker or Shape on the map or open the listing details page directly.", 'listdom'); ?></p>
        </div>
    </div>
    <div class="lsd-form-row lsd-map-provider-dependency lsd-map-provider-dependency-googlemap">
        <div class="lsd-col-2"><?php echo LSD_Form::label([
            'title' => esc_html__('Map Search', 'listdom'),
            'for' => 'lsd_display_options_skin_listgrid_mapsearch',
        ]); ?></div>
        <div class="lsd-col-6">
            <?php if($this->isPro()): ?>
                <?php echo LSD_Form::switcher([
                    'id' => 'lsd_display_options_skin_listgrid_mapsearch',
                    'name' => 'lsd[display][listgrid][mapsearch]',
                    'value' => $listgrid['mapsearch'] ?? '1',
                ]); ?>
                <p class="description"><?php esc_html_e("Provide ability to filter listings based on current map position.", 'listdom'); ?></p>
            <?php else: ?>
                <p class="lsd-alert lsd-warning"><?php echo LSD_Base::missFeatureMessage(esc_html__('Map Search', 'listdom')); ?></p>
            <?php endif; ?>
        </div>
    </div>
    <div class="lsd-form-row">
        <div class="lsd-col-2"><?php echo LSD_Form::label([
            'title' => esc_html__('Map Limit', 'listdom'),
            'for' => 'lsd_display_options_skin_listgrid_maplimit',
        ]); ?></div>
        <div class="lsd-col-6">
            <?php echo LSD_Form::text([
                'id' => 'lsd_display_options_skin_listgrid_maplimit',
                'name' => 'lsd[display][listgrid][maplimit]',
                'value' => $listgrid['maplimit'] ?? '300'
            ]); ?>
            <p class="description"><?php esc_html_e("It's for Map. If you increase the limit to more than 300, then the page may loads pretty slow. We suggest you to use filter options to filter only the listings that you want to show.", 'listdom'); ?></p>
        </div>
    </div>

    <?php
        // Action for Third Party Plugins
        do_action('lsd_shortcode_map_options', 'listgrid', $options);
    ?>
</div>
<div class="lsd-form-row">
    <div class="lsd-col-2"><?php echo LSD_Form::label([
        'title' => esc_html__('Default View', 'listdom'),
        'for' => 'lsd_display_options_skin_listgrid_default_view',
    ]); ?></div>
    <div class="lsd-col-6">
        <?php echo LSD_Form::select([
            'id' => 'lsd_display_options_skin_listgrid_default_view',
            'name' => 'lsd[display][listgrid][default_view]',
            'options' => [
                'grid' => esc_html__('Grid View', 'listdom'),
                'list' => esc_html__('List View', 'listdom')
            ],
            'value' => $listgrid['default_view'] ?? 'grid'
        ]); ?>
        <p class="description"><?php esc_html_e("You can change the default view that will show on the page.", 'listdom'); ?></p>
    </div>
</div>
<div class="lsd-form-row">
    <div class="lsd-col-2"><?php echo LSD_Form::label([
        'title' => esc_html__('Listings Per Row', 'listdom'),
        'for' => 'lsd_display_options_skin_listgrid_columns',
    ]); ?></div>
    <div class="lsd-col-6">
        <?php echo LSD_Form::select([
            'id' => 'lsd_display_options_skin_listgrid_columns',
            'name' => 'lsd[display][listgrid][columns]',
            'options' => ['2' => 2, '3' => 3, '4' => 4, '6' => 6],
            'value' => $listgrid['columns'] ?? '3'
        ]); ?>
        <p class="description"><?php esc_html_e("It used for grid view.", 'listdom'); ?></p>
    </div>
</div>
<div class="lsd-form-row">
    <div class="lsd-col-2"><?php echo LSD_Form::label([
        'title' => esc_html__('Limit', 'listdom'),
        'for' => 'lsd_display_options_skin_listgrid_limit',
    ]); ?></div>
    <div class="lsd-col-6">
        <?php echo LSD_Form::text([
            'id' => 'lsd_display_options_skin_listgrid_limit',
            'name' => 'lsd[display][listgrid][limit]',
            'value' => $listgrid['limit'] ?? '12'
        ]); ?>
        <p class="description"><?php echo sprintf(esc_html__("Number of Listings Per Page. It should be multiply of %s option. For example if %s is set to 3, you should set the limit to 3, 6, 9, 12, 30, etc.", 'listdom'), '<strong>'.esc_html__('Listings Per Row', 'listdom').'</strong>', '<strong>'.esc_html__('Listings Per Row', 'listdom').'</strong>'); ?></p>
    </div>
</div>
<div class="lsd-form-row">
    <div class="lsd-col-2"><?php echo LSD_Form::label([
        'title' => esc_html__('Pagination Method', 'listdom'),
        'for' => 'lsd_display_options_skin_listgrid_pagination',
    ]); ?></div>
    <div class="lsd-col-6">
        <?php echo LSD_Form::select([
            'id' => 'lsd_display_options_skin_listgrid_pagination',
            'name' => 'lsd[display][listgrid][pagination]',
            'value' => $listgrid['pagination'] ?? (isset($listgrid['load_more']) && $listgrid['load_more'] == 0 ? 'disabled' : 'loadmore'),
            'options' => [
                'loadmore' => esc_html__('Load More Button', 'listdom'),
                'scroll' => esc_html__('Infinite Scroll', 'listdom'),
                'disabled' => esc_html__('Disabled', 'listdom'),
            ],
        ]); ?>
        <p class="description"><?php esc_html_e('Navigation method', 'listdom'); ?></p>
    </div>
</div>

<?php if($this->isPro()): ?>
<div class="lsd-form-row">
    <div class="lsd-col-2"><?php echo LSD_Form::label([
        'title' => esc_html__('Listing Link', 'listdom'),
        'for' => 'lsd_display_options_skin_listgrid_listing_link',
    ]); ?></div>
    <div class="lsd-col-6">
        <?php echo LSD_Form::select([
            'id' => 'lsd_display_options_skin_listgrid_listing_link',
            'name' => 'lsd[display][listgrid][listing_link]',
            'value' => $listgrid['listing_link'] ?? 'normal',
            'options' => [
                'normal' => esc_html__('Same Window', 'listdom'),
                'blank' => esc_html__('New Window', 'listdom'),
                'disabled' => esc_html__('Disabled', 'listdom'),
            ],
        ]); ?>
        <p class="description"><?php esc_html_e("Link to listing detail page.", 'listdom'); ?></p>
    </div>
</div>
<div class="lsd-form-row">
    <div class="lsd-col-2"><?php echo LSD_Form::label([
        'title' => esc_html__('Display Image', 'listdom'),
        'for' => 'lsd_display_options_skin_listgrid_display_image',
    ]); ?></div>
    <div class="lsd-col-6">
        <?php echo LSD_Form::switcher([
            'id' => 'lsd_display_options_skin_listgrid_display_image',
            'name' => 'lsd[display][listgrid][display_image]',
            'value' => $listgrid['display_image'] ?? '1',
            'toggle' => '#lsd_display_options_skin_listgrid_image_method'
        ]); ?>
        <p class="description"><?php esc_html_e("Display listing image.", 'listdom'); ?></p>
    </div>
</div>
<div class="lsd-form-row <?php echo !isset($listgrid['display_image']) || $listgrid['display_image'] ? '' : 'lsd-util-hide'; ?>" id="lsd_display_options_skin_listgrid_image_method">
    <div class="lsd-col-2"><?php echo LSD_Form::label([
        'title' => esc_html__('Image Method', 'listdom'),
        'for' => 'lsd_display_options_skin_listgrid_image_method',
    ]); ?></div>
    <div class="lsd-col-6">
        <?php echo LSD_Form::select([
            'id' => 'lsd_display_options_skin_listgrid_image_method',
            'name' => 'lsd[display][listgrid][image_method]',
            'options' => [
                'cover' => esc_html__('Cover', 'listdom'),
                'slider' => esc_html__('Slider', 'listdom'),
            ],
            'value' => $listgrid['image_method'] ?? 'cover'
        ]); ?>
        <p class="description"><?php esc_html_e("Cover shows only featured image but slider shows all gallery images.", 'listdom'); ?></p>
    </div>
</div>
<?php else: ?>
<div class="lsd-form-row">
    <div class="lsd-col-2"></div>
    <div class="lsd-col-6">
        <p class="lsd-alert lsd-warning lsd-mt-0"><?php echo LSD_Base::missFeatureMessage(esc_html__('Listing Link & Display Image', 'listdom'), true); ?></p>
    </div>
</div>
<?php endif; ?>

<div class="lsd-form-row">
    <div class="lsd-col-2"><?php echo LSD_Form::label([
        'title' => esc_html__('Display Labels', 'listdom'),
        'for' => 'lsd_display_options_skin_listgrid_display_labels',
    ]); ?></div>
    <div class="lsd-col-6">
        <?php echo LSD_Form::switcher([
            'id' => 'lsd_display_options_skin_listgrid_display_labels',
            'name' => 'lsd[display][listgrid][display_labels]',
            'value' => $listgrid['display_labels'] ?? '0'
        ]); ?>
        <p class="description"><?php esc_html_e("Display listing labels on the image or not.", 'listdom'); ?></p>
    </div>
</div>
<div class="lsd-form-row">
    <div class="lsd-col-2"><?php echo LSD_Form::label([
        'title' => esc_html__('Display Share Buttons', 'listdom'),
        'for' => 'lsd_display_options_skin_listgrid_display_share_buttons',
    ]); ?></div>
    <div class="lsd-col-6">
        <?php echo LSD_Form::switcher([
            'id' => 'lsd_display_options_skin_listgrid_display_share_buttons',
            'name' => 'lsd[display][listgrid][display_share_buttons]',
            'value' => $listgrid['display_share_buttons'] ?? '0'
        ]); ?>
        <p class="description"><?php esc_html_e("Display share buttons.", 'listdom'); ?></p>
    </div>
</div>