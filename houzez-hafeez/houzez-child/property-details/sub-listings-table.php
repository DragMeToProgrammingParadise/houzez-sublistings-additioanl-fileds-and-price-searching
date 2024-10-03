<?php
// Get multi-unit data associated with the listing
$multi_units = houzez_get_listing_data('multi_units');

if (isset($multi_units[0]['fave_mu_title']) && !empty($multi_units[0]['fave_mu_title'])) {
?>
<div class="property-sub-listings-table-wrap property-section-wrap" id="property-sub-listings-wrap">
    <div class="block-wrap">
        <?php 
        // Main listing data
        $property_id = get_the_ID();
        $property_title = get_the_title($property_id);
        $property_price = get_post_meta($property_id, 'fave_property_price', true);

        // Display main listing details
        echo '<div class="main-listing">';
        echo '<h1>' . esc_html($property_id) . '</h1>';
        echo '<h1>' . esc_html($property_title) . '</h1>';
        echo '<p>Price: ' . esc_html($property_price) . '</p>';
        echo '</div>';

        // Sub-listing data
        $multi_units = get_post_meta($property_id, 'fave_multi_units', true);

        if (!empty($multi_units) && is_array($multi_units)) {
            echo '<h2>Sub Listings</h2>';
        ?>
            <div class="block-title-wrap">
                <h2><?php echo houzez_option('sps_sub_listings', 'Sub Listings'); ?></h2>
            </div><!-- block-title-wrap -->
            <div class="block-content-wrap">
                <table class="sub-listings-table table-lined responsive-table">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('Title', 'houzez'); ?></th>
                            <th><?php esc_html_e('Property Type', 'houzez'); ?></th>
                            <th><?php esc_html_e('Price', 'houzez'); ?></th>
                            <th><?php esc_html_e('Beds', 'houzez'); ?></th>
                            <th><?php esc_html_e('Baths', 'houzez'); ?></th>
                            <th><?php esc_html_e('Property Size', 'houzez'); ?></th>
                            <th><?php esc_html_e('Availability Date', 'houzez'); ?></th>
                            <th><?php esc_html_e('Payment Plan', 'houzez'); ?></th> <!-- Payment Plan Column -->
                        </tr>
                    </thead>
                    <tbody>

                    <?php 
                    foreach ($multi_units as $mu) {
                        $mu_price_postfix = !empty($mu['fave_mu_price_postfix']) ? ' / ' . esc_html($mu['fave_mu_price_postfix']) : '';
                        $fave_mu_size = !empty($mu['fave_mu_size']) ? esc_html($mu['fave_mu_size']) : '';
                        $postfix = !empty($mu['fave_mu_size_postfix']) ? houzez_get_size_unit($mu['fave_mu_size_postfix']) : '';
                        $fave_mu_availability_date = !empty($mu['fave_mu_availability_date']) ? esc_html($mu['fave_mu_availability_date']) : '';
                        $fave_mu_beds = !empty($mu['fave_mu_beds']) ? esc_html($mu['fave_mu_beds']) : '';
                        $fave_mu_baths = !empty($mu['fave_mu_baths']) ? esc_html($mu['fave_mu_baths']) : '';

                        // Extract payment plan details
                        $payment_plans = ''; // Initialize to store all payment plans
                        if (!empty($mu['fave_mu_payment_plans']) && is_array($mu['fave_mu_payment_plans'])) {
                            foreach ($mu['fave_mu_payment_plans'] as $payment_plan) {
                                $payment_title = isset($payment_plan['payment_title']) ? esc_html($payment_plan['payment_title']) : '';
                                $payment_value = isset($payment_plan['payment_value']) ? esc_html($payment_plan['payment_value']) : '';
                                
                                if (!empty($payment_title) && !empty($payment_value)) {
                                    $payment_plans .= '<strong>' . $payment_title . '</strong>: ' . $payment_value . '<br>';
                                }
                            }
                        }

                        ?>
                        <tr>
                            <td data-label="<?php esc_html_e('Title', 'houzez'); ?>">
                                <strong><?php echo esc_attr($mu['fave_mu_title']); ?></strong>
                            </td>
                            <td data-label="<?php esc_html_e('Property Type', 'houzez'); ?>"><?php echo esc_attr($mu['fave_mu_type']); ?></td>
                            <td data-label="<?php esc_html_e('Price', 'houzez'); ?>">
                                <strong><?php echo houzez_get_property_price($mu['fave_mu_price']) . $mu_price_postfix; ?></strong>
                            </td>
                            <td data-label="<?php esc_html_e('Beds', 'houzez'); ?>">
                                <i class="houzez-icon icon-hotel-double-bed-1 mr-1"></i>
                                <?php echo esc_attr($fave_mu_beds); ?> 
                            </td>
                            <td data-label="<?php esc_html_e('Baths', 'houzez'); ?>">
                                <i class="houzez-icon icon-bathroom-shower-1 mr-1"></i>
                                <?php echo esc_attr($fave_mu_baths); ?> 
                            </td>
                            <td data-label="<?php esc_html_e('Property Size', 'houzez'); ?>"><?php echo $fave_mu_size . ' ' . $postfix; ?></td>
                            <td data-label="<?php esc_html_e('Availability Date', 'houzez'); ?>"><?php echo esc_attr($fave_mu_availability_date); ?></td>
                            <td data-label="<?php esc_html_e('Payment Plan', 'houzez'); ?>">
                                <?php 
                                if (!empty($payment_plans)) {
                                    echo $payment_plans; // Display all payment plans
                                } else {
                                    echo 'No payment plan available';
                                }
                                ?>
                            </td>
                        </tr>
                    <?php 
                    } // End of foreach loop
                    ?>
                    
                    </tbody>
                </table>
            </div><!-- block-content-wrap -->
        <?php 
        } else {
            echo '<p>No sub-listings available.</p>';
        }
        ?>
    </div><!-- block-wrap -->
</div><!-- property-sub-listings-wrap -->
<?php 
}
?>