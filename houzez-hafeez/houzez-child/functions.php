<?php
// code will goes here


function save_multi_unit_data($post_id) {
    // Check if this is an autosave or if the user has permission to save
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Check user permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Check if the 'fave_multi_units' field is present
    if (isset($_POST['fave_multi_units'])) {
        $multi_units = $_POST['fave_multi_units'];

        // Save multi units data
        foreach ($multi_units as $key => $sub_listing) {
            // Check if the price exists and save it to a separate meta key
            if (isset($sub_listing['fave_mu_price'])) {
                $price_key = 'sub_listing_price_' . $key;
                update_post_meta($post_id, $price_key, $sub_listing['fave_mu_price']);
            }
        }

        // Optionally, you can save the multi units data itself if needed
        // update_post_meta($post_id, 'fave_multi_units', $multi_units);
    }
}
add_action('save_post', 'save_multi_unit_data');

// houzez_search_min_max_price

if (!function_exists('houzez_search_min_max_price')) {
    function houzez_search_min_max_price($meta_query) {
        // Initialize an array for price queries
        $price_queries = array('relation' => 'OR');

        if (isset($_GET['min-price']) && !empty($_GET['min-price']) && $_GET['min-price'] != 'any' && isset($_GET['max-price']) && !empty($_GET['max-price']) && $_GET['max-price'] != 'any') {
            $min_price = doubleval(houzez_clean($_GET['min-price']));
            $max_price = doubleval(houzez_clean($_GET['max-price']));

            if ($min_price > 0 && $max_price >= $min_price) {
                // Search in parent property price
                $price_queries[] = array(
                    'key' => 'fave_property_price',
                    'value' => array($min_price, $max_price),
                    'type' => 'NUMERIC',
                    'compare' => 'BETWEEN',
                );

                // Prepare to search in sub-listing prices
                for ($i = 0; $i < 10; $i++) {
                    $sub_listing_key = 'sub_listing_price_' . $i;
                    $price_queries[] = array(
                        'key' => $sub_listing_key,
                        'value' => array($min_price, $max_price),
                        'type' => 'NUMERIC',
                        'compare' => 'BETWEEN',
                    );
                }
            }
        } else if (isset($_GET['min-price']) && !empty($_GET['min-price']) && $_GET['min-price'] != 'any') {
            $min_price = doubleval(houzez_clean($_GET['min-price']));
            if ($min_price > 0) {
                // Search in parent property price
                $price_queries[] = array(
                    'key' => 'fave_property_price',
                    'value' => $min_price,
                    'type' => 'NUMERIC',
                    'compare' => '>=',
                );

                // Prepare to search in sub-listing prices
                for ($i = 0; $i < 10; $i++) {
                    $sub_listing_key = 'sub_listing_price_' . $i;
                    $price_queries[] = array(
                        'key' => $sub_listing_key,
                        'value' => $min_price,
                        'type' => 'NUMERIC',
                        'compare' => '>=',
                    );
                }
            }
        } else if (isset($_GET['max-price']) && !empty($_GET['max-price']) && $_GET['max-price'] != 'any') {
            $max_price = doubleval(houzez_clean($_GET['max-price']));
            if ($max_price > 0) {
                // Search in parent property price
                $price_queries[] = array(
                    'key' => 'fave_property_price',
                    'value' => $max_price,
                    'type' => 'NUMERIC',
                    'compare' => '<=',
                );

                // Prepare to search in sub-listing prices
                for ($i = 0; $i < 10; $i++) {
                    $sub_listing_key = 'sub_listing_price_' . $i;
                    $price_queries[] = array(
                        'key' => $sub_listing_key,
                        'value' => $max_price,
                        'type' => 'NUMERIC',
                        'compare' => '<=',
                    );
                }
            }
        }

        // Add the price queries to the meta query
        if (!empty($price_queries)) {
            $meta_query[] = $price_queries;
        }

        return $meta_query;
    }

    add_filter('houzez_meta_search_filter', 'houzez_search_min_max_price');
}


// houzez_search_bedrooms

if(!function_exists('houzez_search_bedrooms')) {
    function houzez_search_bedrooms($meta_query) {
        $beds_baths_search = houzez_option('beds_baths_search', 'equal');
        $search_criteria = '=';
        $type = 'NUMERIC';

        if ($beds_baths_search == 'greater') {
            $search_criteria = '>=';
        } else if ($beds_baths_search == 'like') {
            $search_criteria = 'LIKE';
            $type = 'CHAR';
        }

        if (isset($_GET['bedrooms']) && $_GET['bedrooms'] != "" && $_GET['bedrooms'] != 'any') {
            $bedrooms = $_GET['bedrooms'];

            // Search in parent listing bedrooms
            $meta_query[] = array(
                'relation' => 'OR',
                array(
                    'key' => 'fave_property_bedrooms',
                    'value' => $bedrooms,
                    'type' => $type,
                    'compare' => $search_criteria,
                ),
                // Search in sub-listings bedrooms
                array(
                    'key' => 'fave_multi_units',
                    'value' => sprintf('s:12:"fave_mu_beds";s:%d:"%s"', strlen($bedrooms), $bedrooms),
                    'compare' => 'LIKE'
                )
            );
        }

        return $meta_query;
    }

    add_filter('houzez_meta_search_filter', 'houzez_search_bedrooms');
}

// houzez_search_bathrooms

if(!function_exists('houzez_search_bathrooms')) {
	function houzez_search_bathrooms($meta_query) {
        $beds_baths_search = houzez_option('beds_baths_search', 'equal');
        $search_criteria = '=';
        $type = 'NUMERIC';

        if ($beds_baths_search == 'greater') {
            $search_criteria = '>=';
        } else if ($beds_baths_search == 'like') {
            $search_criteria = 'LIKE';
            $type = 'CHAR';
        }

        if (isset($_GET['bathrooms']) && $_GET['bathrooms'] != "" && $_GET['bathrooms'] != 'any') {
            $bathrooms = $_GET['bathrooms'];

            // Search in parent listing bathrooms
            $meta_query[] = array(
                'relation' => 'OR',
                array(
                    'key' => 'fave_property_bathrooms',
                    'value' => $bathrooms,
                    'type' => $type,
                    'compare' => $search_criteria,
                ),
                // Search in sub-listings bathrooms
                array(
                    'key' => 'fave_multi_units',
                    'value' => sprintf('s:13:"fave_mu_baths";s:%d:"%s"', strlen($bathrooms), $bathrooms),
                    'compare' => 'LIKE'
                )
            );
        }

        return $meta_query;
    }

	add_filter('houzez_meta_search_filter', 'houzez_search_bathrooms');
}




/*-----------------------------------------------------------------------------------*/
// Submit Property filter
/*-----------------------------------------------------------------------------------*/


?>