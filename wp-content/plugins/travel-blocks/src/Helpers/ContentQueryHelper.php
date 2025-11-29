<?php

namespace Travel\Blocks\Helpers;

class ContentQueryHelper {

    /**
     * Get content from CPT with all filters applied
     *
     * Uses transient caching to improve performance. Cache is automatically
     * invalidated when a post is saved/updated.
     *
     * @param string $prefix Field prefix to use (e.g., 'fgc', 'hc', 'pc')
     * @param string $post_type Post type to query ('package' or 'post')
     * @return array Array of items formatted as cards
     */
    public static function get_content($prefix = '', $post_type = 'package') {
        // Build field name with prefix
        $field_prefix = $prefix ? $prefix . '_' : '';

        // Get configuration from ACF fields
        $limit = get_field($field_prefix . 'dynamic_limit') ?: 6;
        $orderby = get_field($field_prefix . 'dynamic_orderby') ?: 'date';
        $order = get_field($field_prefix . 'dynamic_order') ?: 'DESC';

        // Build cache key from all filter parameters
        $cache_key = self::build_cache_key($prefix, $post_type);

        // Try to get cached results
        $cached = get_transient($cache_key);
        if ($cached !== false && is_array($cached)) {
            return $cached;
        }

        // Prepare base WP_Query arguments
        $args = [
            'post_type' => $post_type,
            'posts_per_page' => $limit,
            'post_status' => 'publish',
            'orderby' => $orderby,
            'order' => $order,
            'no_found_rows' => true, // Performance: skip counting total rows
            'update_post_meta_cache' => false, // We'll get ACF fields manually
            'update_post_term_cache' => true, // Keep this for taxonomy queries
        ];

        // Special ordering for ACF fields
        if ($orderby === 'rating') {
            $args['meta_key'] = 'rating';
            $args['orderby'] = 'meta_value_num';
        } elseif ($orderby === 'price') {
            $args['meta_key'] = 'price_from';
            $args['orderby'] = 'meta_value_num';
        }

        // ===== APPLY TAXONOMY FILTERS =====
        $tax_query = ['relation' => 'AND'];

        // FILTERS FOR BLOG POSTS
        if ($post_type === 'post') {
            // Category filter
            $filter_category = get_field($field_prefix . 'filter_category');
            if (!empty($filter_category)) {
                $tax_query[] = [
                    'taxonomy' => 'category',
                    'field' => 'term_id',
                    'terms' => $filter_category,
                    'operator' => 'IN',
                ];
            }

            // Tag filter
            $filter_post_tag = get_field($field_prefix . 'filter_post_tag');
            if (!empty($filter_post_tag)) {
                $tax_query[] = [
                    'taxonomy' => 'post_tag',
                    'field' => 'term_id',
                    'terms' => $filter_post_tag,
                    'operator' => 'IN',
                ];
            }
        }

        // FILTERS FOR PACKAGES
        // Package Type
        $filter_package_type = get_field($field_prefix . 'filter_package_type');
        if (!empty($filter_package_type)) {
            $tax_query[] = [
                'taxonomy' => 'package_type',
                'field' => 'term_id',
                'terms' => $filter_package_type,
                'operator' => 'IN',
            ];
        }

        // Interest
        $filter_interest = get_field($field_prefix . 'filter_interest');
        if (!empty($filter_interest)) {
            $tax_query[] = [
                'taxonomy' => 'interest',
                'field' => 'term_id',
                'terms' => $filter_interest,
                'operator' => 'IN',
            ];
        }

        // Locations
        $filter_locations = get_field($field_prefix . 'filter_locations');
        if (!empty($filter_locations)) {
            $tax_query[] = [
                'taxonomy' => 'locations',
                'field' => 'term_id',
                'terms' => $filter_locations,
                'operator' => 'IN',
            ];
        }

        // Optional Renting
        $filter_optional_renting = get_field($field_prefix . 'filter_optional_renting');
        if (!empty($filter_optional_renting)) {
            $tax_query[] = [
                'taxonomy' => 'optional_renting',
                'field' => 'term_id',
                'terms' => $filter_optional_renting,
                'operator' => 'IN',
            ];
        }

        // Included Services
        $filter_included_services = get_field($field_prefix . 'filter_included_services');
        if (!empty($filter_included_services)) {
            $tax_query[] = [
                'taxonomy' => 'included_services',
                'field' => 'term_id',
                'terms' => $filter_included_services,
                'operator' => 'IN',
            ];
        }

        // Additional Info
        $filter_additional_info = get_field($field_prefix . 'filter_additional_info');
        if (!empty($filter_additional_info)) {
            $tax_query[] = [
                'taxonomy' => 'additional_info',
                'field' => 'term_id',
                'terms' => $filter_additional_info,
                'operator' => 'IN',
            ];
        }

        // Tag Locations
        $filter_tag_locations = get_field($field_prefix . 'filter_tag_locations');
        if (!empty($filter_tag_locations)) {
            $tax_query[] = [
                'taxonomy' => 'tag_locations',
                'field' => 'term_id',
                'terms' => $filter_tag_locations,
                'operator' => 'IN',
            ];
        }

        // Add tax_query to args if filters exist
        if (count($tax_query) > 1) { // More than 1 because it always has 'relation'
            $args['tax_query'] = $tax_query;
        }

        // ===== APPLY ACF FIELD FILTERS =====
        $meta_query = ['relation' => 'AND'];

        // Service Type
        $filter_service_type = get_field($field_prefix . 'filter_service_type');
        if (!empty($filter_service_type)) {
            $meta_query[] = [
                'key' => 'service_type',
                'value' => $filter_service_type,
                'compare' => 'IN',
            ];
        }

        // Activity Level
        $filter_activity_level = get_field($field_prefix . 'filter_activity_level');
        if (!empty($filter_activity_level)) {
            $meta_query[] = [
                'key' => 'activity_level',
                'value' => $filter_activity_level,
                'compare' => 'IN',
            ];
        }

        // Physical Difficulty
        $filter_difficulty = get_field($field_prefix . 'filter_difficulty');
        if (!empty($filter_difficulty)) {
            $meta_query[] = [
                'key' => 'physical_difficulty',
                'value' => $filter_difficulty,
                'compare' => 'IN',
            ];
        }

        // Price Range
        $price_min = get_field($field_prefix . 'filter_price_min');
        $price_max = get_field($field_prefix . 'filter_price_max');
        if (!empty($price_min) || !empty($price_max)) {
            $price_query = ['relation' => 'OR'];

            if (!empty($price_min) && !empty($price_max)) {
                // Both min and max specified - BETWEEN
                $price_query[] = [
                    'key' => 'price_from',
                    'value' => [$price_min, $price_max],
                    'type' => 'NUMERIC',
                    'compare' => 'BETWEEN',
                ];
                $price_query[] = [
                    'key' => 'price_offer',
                    'value' => [$price_min, $price_max],
                    'type' => 'NUMERIC',
                    'compare' => 'BETWEEN',
                ];
            } elseif (!empty($price_min)) {
                // Only min specified - greater than or equal
                $price_query[] = [
                    'key' => 'price_from',
                    'value' => $price_min,
                    'type' => 'NUMERIC',
                    'compare' => '>=',
                ];
                $price_query[] = [
                    'key' => 'price_offer',
                    'value' => $price_min,
                    'type' => 'NUMERIC',
                    'compare' => '>=',
                ];
            } elseif (!empty($price_max)) {
                // Only max specified - less than or equal
                $price_query[] = [
                    'key' => 'price_from',
                    'value' => $price_max,
                    'type' => 'NUMERIC',
                    'compare' => '<=',
                ];
                $price_query[] = [
                    'key' => 'price_offer',
                    'value' => $price_max,
                    'type' => 'NUMERIC',
                    'compare' => '<=',
                ];
            }

            $meta_query[] = $price_query;
        }

        // Days Range
        $days_min = get_field($field_prefix . 'filter_days_min');
        $days_max = get_field($field_prefix . 'filter_days_max');
        if (!empty($days_min) && !empty($days_max)) {
            $meta_query[] = [
                'key' => 'days',
                'value' => [$days_min, $days_max],
                'type' => 'NUMERIC',
                'compare' => 'BETWEEN',
            ];
        } elseif (!empty($days_min)) {
            $meta_query[] = [
                'key' => 'days',
                'value' => $days_min,
                'type' => 'NUMERIC',
                'compare' => '>=',
            ];
        } elseif (!empty($days_max)) {
            $meta_query[] = [
                'key' => 'days',
                'value' => $days_max,
                'type' => 'NUMERIC',
                'compare' => '<=',
            ];
        }

        // Featured Only
        $filter_featured = get_field($field_prefix . 'filter_featured_only');
        if ($filter_featured) {
            $meta_query[] = [
                'key' => 'featured_package',
                'value' => '1',
                'compare' => '=',
            ];
        }

        // Has Promotion (active_promotion = 1)
        $filter_promo = get_field($field_prefix . 'filter_active_promo');
        if ($filter_promo) {
            $meta_query[] = [
                'key' => 'active_promotion',
                'value' => '1',
                'compare' => '=',
            ];
        }

        // Minimum Rating
        $filter_rating = get_field($field_prefix . 'filter_min_rating');
        if (!empty($filter_rating)) {
            $meta_query[] = [
                'key' => 'rating',
                'value' => floatval($filter_rating),
                'type' => 'NUMERIC',
                'compare' => '>=',
            ];
        }

        // Add meta_query to args if filters exist
        if (count($meta_query) > 1) { // More than 1 because it always has 'relation'
            $args['meta_query'] = $meta_query;
        }

        // Execute query
        $query = new \WP_Query($args);

        $items = [];
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $items[] = self::prepare_card_data(get_post(), $prefix, $post_type);
            }
            wp_reset_postdata();
        }

        // Cache results for 15 minutes
        set_transient($cache_key, $items, 15 * MINUTE_IN_SECONDS);

        return $items;
    }

    /**
     * Get packages (backward compatibility wrapper)
     *
     * @param string $prefix Field prefix to use
     * @return array Array of package items formatted as cards
     */
    public static function get_packages($prefix = '') {
        return self::get_content($prefix, 'package');
    }

    /**
     * Get all active deals for deal selector
     *
     * @return array Array of deals with ID and title
     */
    public static function get_all_deals() {
        $args = [
            'post_type' => 'deal',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'orderby' => 'title',
            'order' => 'ASC',
            'no_found_rows' => true,
            'meta_query' => [
                [
                    'key' => 'active',
                    'value' => '1',
                    'compare' => '=',
                ],
            ],
        ];

        $query = new \WP_Query($args);
        $deals = [];

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $post_id = get_the_ID();

                // Check if deal is within date range
                $start_date = get_post_meta($post_id, 'start_date', true);
                $end_date = get_post_meta($post_id, 'end_date', true);
                $now = current_time('timestamp');

                if ($start_date && $end_date) {
                    $start = strtotime($start_date);
                    $end = strtotime($end_date);

                    // Only include active or scheduled deals (not expired)
                    if ($now <= $end) {
                        $discount = get_post_meta($post_id, 'discount_percentage', true);
                        $label = get_the_title();

                        if ($discount) {
                            $label .= ' (' . $discount . '% OFF)';
                        }

                        $deals[$post_id] = $label;
                    }
                }
            }
            wp_reset_postdata();
        }

        return $deals;
    }

    /**
     * Get packages from a specific deal
     *
     * @param int $deal_id Deal post ID
     * @param string $prefix Field prefix to use
     * @return array Array of package items formatted as cards
     */
    public static function get_deal_packages($deal_id, $prefix = '') {
        if (empty($deal_id)) {
            return [];
        }

        // Get packages relationship from deal
        $package_ids = get_post_meta($deal_id, 'packages', true);

        if (empty($package_ids) || !is_array($package_ids)) {
            return [];
        }

        // Get deal discount
        $discount_percentage = get_post_meta($deal_id, 'discount_percentage', true);

        $items = [];
        foreach ($package_ids as $package_id) {
            $package_id = intval($package_id);

            if (!$package_id || get_post_status($package_id) !== 'publish') {
                continue;
            }

            $post = get_post($package_id);
            if (!$post) {
                continue;
            }

            // Prepare card data
            $card_data = self::prepare_package_card_data($post, $prefix);

            // Apply deal discount to price if available
            if ($discount_percentage && !empty($card_data['price'])) {
                // Extract numeric price from string like "$299 USD" or "Desde $299 USD"
                preg_match('/\$?(\d+(?:,\d{3})*(?:\.\d{2})?)/', $card_data['price'], $matches);

                if (!empty($matches[1])) {
                    $original_price = floatval(str_replace(',', '', $matches[1]));
                    $discounted_price = $original_price * (1 - ($discount_percentage / 100));

                    // Format with strikethrough original and discounted price
                    $card_data['price'] = '<span style="text-decoration: line-through; opacity: 0.6;">$' . number_format($original_price, 0) . '</span> <strong style="color: #ff6b6b;">$' . number_format($discounted_price, 0) . ' USD</strong>';
                    $card_data['has_deal_discount'] = true;
                    $card_data['deal_discount'] = $discount_percentage;

                    // Recalculate duration_price with discounted price
                    $duration_price = '';
                    if (!empty($card_data['duration']) || !empty($card_data['price'])) {
                        $parts = [];
                        if (!empty($card_data['duration'])) {
                            $parts[] = $card_data['duration'];
                        }
                        if (!empty($card_data['price'])) {
                            $parts[] = $card_data['price'];
                        }
                        if (!empty($parts)) {
                            $duration_price = implode(' | ', $parts);
                        }
                    }
                    $card_data['duration_price'] = $duration_price;
                }
            }

            // Add deal badge
            $card_data['category'] = get_post_meta($deal_id, 'discount_percentage', true) . '% OFF';
            $card_data['badge_color_variant'] = 'primary';

            $items[] = $card_data;
        }

        return $items;
    }

    /**
     * Build unique cache key based on all filter parameters
     *
     * Creates a hash of all ACF field values to generate a unique cache key
     * for each combination of filters.
     *
     * @param string $prefix Field prefix to use
     * @param string $post_type Post type being queried
     * @return string Cache key
     */
    private static function build_cache_key($prefix = '', $post_type = 'package') {
        $field_prefix = $prefix ? $prefix . '_' : '';
        $params = [
            'post_type' => $post_type,
            'prefix' => $prefix,
            'limit' => get_field($field_prefix . 'dynamic_limit'),
            'orderby' => get_field($field_prefix . 'dynamic_orderby'),
            'order' => get_field($field_prefix . 'dynamic_order'),
            'visible_fields' => get_field($field_prefix . 'dynamic_visible_fields'),
            'cta_text' => get_field($field_prefix . 'dynamic_cta_text'),
            'badge_taxonomy' => get_field($field_prefix . 'badge_taxonomy'),
            'badge_color_variant' => get_field('badge_color_variant'),
            // Taxonomy filters - PACKAGES
            'package_type' => get_field($field_prefix . 'filter_package_type'),
            'interest' => get_field($field_prefix . 'filter_interest'),
            'locations' => get_field($field_prefix . 'filter_locations'),
            'optional_renting' => get_field($field_prefix . 'filter_optional_renting'),
            'included_services' => get_field($field_prefix . 'filter_included_services'),
            'additional_info' => get_field($field_prefix . 'filter_additional_info'),
            'tag_locations' => get_field($field_prefix . 'filter_tag_locations'),
            // Taxonomy filters - BLOG POSTS
            'filter_category' => get_field($field_prefix . 'filter_category'),
            'filter_post_tag' => get_field($field_prefix . 'filter_post_tag'),
            // ACF field filters
            'service_type' => get_field($field_prefix . 'filter_service_type'),
            'activity_level' => get_field($field_prefix . 'filter_activity_level'),
            'difficulty' => get_field($field_prefix . 'filter_difficulty'),
            'price_min' => get_field($field_prefix . 'filter_price_min'),
            'price_max' => get_field($field_prefix . 'filter_price_max'),
            'days_min' => get_field($field_prefix . 'filter_days_min'),
            'days_max' => get_field($field_prefix . 'filter_days_max'),
            'featured' => get_field($field_prefix . 'filter_featured_only'),
            'active_promo' => get_field($field_prefix . 'filter_active_promo'),
            'rating' => get_field($field_prefix . 'filter_min_rating'),
            'deal_id' => get_field($field_prefix . 'deal_selector'),
        ];

        return 'travel_content_' . md5(serialize($params));
    }

    /**
     * Clear all content query caches
     *
     * Should be called when a post is saved/updated.
     * Hook this to 'save_post_package' and 'save_post_post' actions.
     *
     * @return void
     */
    public static function clear_cache() {
        global $wpdb;

        // Delete all transients starting with our prefix
        $wpdb->query(
            "DELETE FROM {$wpdb->options}
             WHERE option_name LIKE '_transient_travel_content_%'
             OR option_name LIKE '_transient_timeout_travel_content_%'"
        );
    }

    /**
     * Prepare card data based on post type
     *
     * @param \WP_Post $post Post object
     * @param string $prefix Field prefix to use
     * @param string $post_type Post type ('package' or 'post')
     * @return array Card data array
     */
    public static function prepare_card_data($post, $prefix = '', $post_type = 'package') {
        if ($post_type === 'post') {
            return self::prepare_post_card_data($post, $prefix);
        }

        return self::prepare_package_card_data($post, $prefix);
    }

    /**
     * Prepare package data to match card structure
     *
     * @param \WP_Post $post Package post object
     * @param string $prefix Field prefix to use
     * @return array Card data array
     */
    public static function prepare_package_card_data($post, $prefix = '') {
        $post_id = $post->ID;
        $field_prefix = $prefix ? $prefix . '_' : '';

        // Get visible fields configuration
        $visible_fields = get_field($field_prefix . 'dynamic_visible_fields') ?: ['image', 'category', 'title', 'description', 'location', 'price'];
        $cta_text = get_field($field_prefix . 'dynamic_cta_text') ?: 'Ver Paquete';

        // Initialize card data with required layout key
        $card_data = [
            'acf_fc_layout' => 'card', // For flexible content compatibility
        ];

        // Image (featured or first from gallery)
        if (in_array('image', $visible_fields)) {
            $image = null;
            if (has_post_thumbnail($post_id)) {
                $image_id = get_post_thumbnail_id($post_id);
                $image = [
                    'url' => get_the_post_thumbnail_url($post_id, 'full'),
                    'sizes' => [
                        'large' => get_the_post_thumbnail_url($post_id, 'large'),
                        'medium' => get_the_post_thumbnail_url($post_id, 'medium')
                    ],
                    'alt' => get_post_meta($image_id, '_wp_attachment_image_alt', true)
                ];
            } else {
                // Fallback: first image from gallery
                $gallery = get_field('gallery', $post_id);
                if (!empty($gallery) && is_array($gallery)) {
                    $image = $gallery[0];
                }
            }
            $card_data['image'] = $image;
        }

        // Category/Badge
        if (in_array('category', $visible_fields)) {
            $category = '';
            // Get badge color from block settings (default: secondary)
            $badge_color = get_field('badge_color_variant') ?: 'secondary';

            // Get selected badge taxonomy from ACF field
            $badge_taxonomy = get_field($field_prefix . 'badge_taxonomy');

            // Priority 1: Active promotion (always overrides badge color to primary)
            if (get_field('promo_enabled', $post_id)) {
                $promo_tag = get_field('promo_tag', $post_id) ?: 'OFERTA';
                $category = ucwords(strtolower($promo_tag)); // Capitalize properly
                $badge_color = 'primary'; // Promo always uses primary color
            }
            // Priority 2: Use selected taxonomy if specified
            elseif (!empty($badge_taxonomy)) {
                if ($badge_taxonomy === 'service_type') {
                    // Service Type ACF field
                    $service_type = get_field('service_type', $post_id);
                    if ($service_type) {
                        $category = ($service_type === 'shared') ? 'Compartido' : 'Privado';
                    }
                } else {
                    // Get first term from selected taxonomy
                    $terms = get_the_terms($post_id, $badge_taxonomy);
                    if (!empty($terms) && !is_wp_error($terms)) {
                        $category = ucwords(strtolower($terms[0]->name)); // Capitalize properly
                    }
                }
            }
            // Priority 3: Default behavior (Service Type → Package Type)
            else {
                // Service Type
                if (get_field('service_type', $post_id)) {
                    $service_type = get_field('service_type', $post_id);
                    $category = ($service_type === 'shared') ? 'Compartido' : 'Privado';
                }
                // Package Type taxonomy
                else {
                    $terms = get_the_terms($post_id, 'package_type');
                    if (!empty($terms) && !is_wp_error($terms)) {
                        $category = ucwords(strtolower($terms[0]->name)); // Capitalize properly
                    }
                }
            }

            $card_data['category'] = $category;
            $card_data['badge_color_variant'] = $badge_color;
        }

        // Title
        if (in_array('title', $visible_fields)) {
            $card_data['title'] = get_the_title($post);
        }

        // Description (use 'excerpt' key for HeroCarousel compatibility)
        if (in_array('description', $visible_fields)) {
            $description = get_field('summary', $post_id);
            if (empty($description)) {
                $description = get_the_excerpt($post);
            }
            $card_data['description'] = $description;
            $card_data['excerpt'] = $description; // Alias for HeroCarousel
        }

        // Location (priority: locations CPT → tag_locations CPT → departure text field → empty)
        if (in_array('location', $visible_fields)) {
            $location = '';

            // Priority 1: Get from 'locations' field (post_object array → location CPT)
            $location_ids = get_field('locations', $post_id);
            if (!empty($location_ids) && is_array($location_ids)) {
                $location = get_the_title($location_ids[0]); // Tomar el primero
            } elseif ($location_ids) {
                $location = get_the_title($location_ids); // Por si es un solo ID
            }

            // Priority 2: Fallback to 'tag_locations' field
            if (empty($location)) {
                $tag_location_ids = get_field('tag_locations', $post_id);
                if (!empty($tag_location_ids) && is_array($tag_location_ids)) {
                    $location = get_the_title($tag_location_ids[0]); // Tomar el primero
                } elseif ($tag_location_ids) {
                    $location = get_the_title($tag_location_ids);
                }
            }

            // Priority 3: Fallback to 'departure' text field
            if (empty($location)) {
                $location = get_field('departure', $post_id) ?: '';
            }

            $card_data['location'] = $location;
        }

        // Price
        if (in_array('price', $visible_fields)) {
            $price = '';
            $price_offer = get_field('price_offer', $post_id);
            $price_from = get_field('price_from', $post_id);

            if (!empty($price_offer)) {
                $price = '$' . number_format($price_offer, 0);
            } elseif (!empty($price_from)) {
                $price = 'From $' . number_format($price_from, 0);
            }
            $card_data['price'] = $price;
        }

        // Duration (new field)
        if (in_array('duration', $visible_fields)) {
            $days = get_field('days', $post_id);
            if (!empty($days)) {
                $card_data['duration'] = $days . ' ' . ($days == 1 ? 'day' : 'days');
            }
        }

        // Rating (new field)
        if (in_array('rating', $visible_fields)) {
            $rating = get_field('rating', $post_id);
            if (!empty($rating)) {
                $card_data['rating'] = floatval($rating);
            }
        }

        // Group Size (new field)
        if (in_array('group_size', $visible_fields)) {
            $group_size = get_field('group_size', $post_id);
            if (!empty($group_size)) {
                $card_data['group_size'] = $group_size;
            }
        }

        // Date (for HeroCarousel compatibility - use post date)
        $card_data['date'] = get_the_date('F j, Y', $post);

        // Combined duration + price for package cards (format: "7 days | From $70")
        $duration_price = '';
        if (!empty($card_data['duration']) || !empty($card_data['price'])) {
            $parts = [];
            if (!empty($card_data['duration'])) {
                $parts[] = $card_data['duration'];
            }
            if (!empty($card_data['price'])) {
                $parts[] = $card_data['price'];
            }
            if (!empty($parts)) {
                $duration_price = implode(' | ', $parts);
            }
        }
        $card_data['duration_price'] = $duration_price;
        $card_data['is_package'] = true; // Flag to identify package content

        // Link and CTA (always included for functionality)
        $card_data['link'] = [
            'url' => get_permalink($post),
            'title' => $cta_text,
            'target' => ''
        ];
        $card_data['cta_text'] = $cta_text;

        return $card_data;
    }

    /**
     * Get array of all dynamic content field definitions for ACF
     * This can be included in any block's register_fields() method
     *
     * @param string $prefix Field key prefix (e.g., 'fgc', 'hc', 'pc')
     * @return array Array of ACF field definitions
     */
    public static function get_dynamic_content_fields($prefix) {
        return [
            // ===== TAB: CONTENIDO DINÁMICO =====
            [
                'key' => "field_{$prefix}_tab_dynamic_content",
                'label' => '🔄 Contenido Dinámico',
                'type' => 'tab',
                'placement' => 'top',
            ],

            // Selector de fuente de contenido
            [
                'key' => "field_{$prefix}_dynamic_source",
                'label' => '📦 Fuente de Contenido',
                'name' => "{$prefix}_dynamic_source",
                'type' => 'select',
                'instructions' => 'Selecciona si el contenido es manual (items personalizados) o dinámico (desde CPT)',
                'required' => 0,
                'choices' => [
                    'none' => 'Ninguno (Manual)',
                    'package' => 'Packages (Dinámico)',
                    'post' => 'Blog Posts (Dinámico)',
                    'deal' => 'Deal - Paquetes de Oferta (Dinámico)',
                ],
                'default_value' => 'none',
                'ui' => 1,
                'return_format' => 'value',
            ],

            // Deal Selector (solo se muestra cuando dynamic_source = 'deal')
            [
                'key' => "field_{$prefix}_deal_selector",
                'label' => '🏷️ Seleccionar Deal',
                'name' => "{$prefix}_deal_selector",
                'type' => 'select',
                'instructions' => 'Selecciona el deal del cual mostrar los paquetes con precio promocional',
                'required' => 1,
                'conditional_logic' => [
                    [
                        [
                            'field' => "field_{$prefix}_dynamic_source",
                            'operator' => '==',
                            'value' => 'deal',
                        ],
                    ],
                ],
                'choices' => [], // Will be populated dynamically via AJAX
                'default_value' => '',
                'allow_null' => 0,
                'ui' => 1,
                'ajax' => 0,
                'return_format' => 'value',
            ],

            // Límite de Posts
            [
                'key' => "field_{$prefix}_dynamic_limit",
                'label' => '📊 Límite de Posts',
                'name' => "{$prefix}_dynamic_limit",
                'type' => 'number',
                'instructions' => 'Cantidad máxima de items a mostrar',
                'conditional_logic' => [
                    [
                        [
                            'field' => "field_{$prefix}_dynamic_source",
                            'operator' => '==',
                            'value' => 'package',
                        ],
                    ],
                    [
                        [
                            'field' => "field_{$prefix}_dynamic_source",
                            'operator' => '==',
                            'value' => 'post',
                        ],
                    ],
                ],
                'default_value' => 6,
                'min' => 1,
                'max' => 50,
                'step' => 1,
            ],

            // Ordenar Por
            [
                'key' => "field_{$prefix}_dynamic_orderby",
                'label' => '📑 Ordenar Por',
                'name' => "{$prefix}_dynamic_orderby",
                'type' => 'select',
                'instructions' => 'Campo por el cual ordenar los items',
                'conditional_logic' => [
                    [
                        [
                            'field' => "field_{$prefix}_dynamic_source",
                            'operator' => '==',
                            'value' => 'package',
                        ],
                    ],
                    [
                        [
                            'field' => "field_{$prefix}_dynamic_source",
                            'operator' => '==',
                            'value' => 'post',
                        ],
                    ],
                ],
                'choices' => [
                    'date' => 'Fecha de publicación (más reciente)',
                    'modified' => 'Última modificación',
                    'title' => 'Título (A-Z)',
                    'rand' => 'Aleatorio',
                    'menu_order' => 'Orden manual',
                    'rating' => 'Rating (campo ACF)',
                    'price' => 'Precio (menor a mayor)',
                ],
                'default_value' => 'date',
                'ui' => 1,
            ],

            // Dirección de Ordenamiento
            [
                'key' => "field_{$prefix}_dynamic_order",
                'label' => '↕️ Dirección',
                'name' => "{$prefix}_dynamic_order",
                'type' => 'select',
                'instructions' => 'Dirección del ordenamiento',
                'conditional_logic' => [
                    [
                        [
                            'field' => "field_{$prefix}_dynamic_source",
                            'operator' => '==',
                            'value' => 'package',
                        ],
                    ],
                    [
                        [
                            'field' => "field_{$prefix}_dynamic_source",
                            'operator' => '==',
                            'value' => 'post',
                        ],
                    ],
                ],
                'choices' => [
                    'DESC' => 'Descendente',
                    'ASC' => 'Ascendente',
                ],
                'default_value' => 'DESC',
                'ui' => 1,
            ],

            // Campos Visibles en Card - PACKAGES
            [
                'key' => "field_{$prefix}_dynamic_visible_fields_package",
                'label' => '👁️ Campos Visibles (Package)',
                'name' => "{$prefix}_dynamic_visible_fields",
                'type' => 'checkbox',
                'instructions' => 'Selecciona qué campos del package mostrar en cada card. Desmarca los que quieras ocultar.',
                'conditional_logic' => [
                    [
                        [
                            'field' => "field_{$prefix}_dynamic_source",
                            'operator' => '==',
                            'value' => 'package',
                        ],
                    ],
                ],
                'choices' => [
                    'image' => '🖼️ Imagen',
                    'category' => '🏷️ Badge/Categoría',
                    'title' => '📝 Título',
                    'description' => '📄 Descripción',
                    'location' => '📍 Ubicación',
                    'price' => '💰 Precio',
                    'duration' => '⏱️ Duración (días)',
                    'rating' => '⭐ Rating',
                    'group_size' => '👥 Tamaño de Grupo',
                ],
                'default_value' => ['image', 'category', 'title', 'description', 'location', 'price'],
                'layout' => 'vertical',
                'toggle' => 1,
            ],

            // Campos Visibles en Card - POSTS
            [
                'key' => "field_{$prefix}_dynamic_visible_fields_post",
                'label' => '👁️ Campos Visibles (Blog Post)',
                'name' => "{$prefix}_dynamic_visible_fields",
                'type' => 'checkbox',
                'instructions' => 'Selecciona qué campos del post mostrar en cada card.',
                'conditional_logic' => [
                    [
                        [
                            'field' => "field_{$prefix}_dynamic_source",
                            'operator' => '==',
                            'value' => 'post',
                        ],
                    ],
                ],
                'choices' => [
                    'image' => '🖼️ Imagen destacada',
                    'category' => '📁 Categoría',
                    'title' => '📝 Título',
                    'description' => '📄 Extracto',
                ],
                'default_value' => ['image', 'category', 'title', 'description'],
                'layout' => 'vertical',
                'toggle' => 1,
            ],

            // Texto CTA Personalizado
            [
                'key' => "field_{$prefix}_dynamic_cta_text",
                'label' => '🔘 Texto del Botón CTA',
                'name' => "{$prefix}_dynamic_cta_text",
                'type' => 'text',
                'instructions' => 'Texto personalizado para el botón de todas las cards dinámicas',
                'conditional_logic' => [
                    [
                        [
                            'field' => "field_{$prefix}_dynamic_source",
                            'operator' => '==',
                            'value' => 'package',
                        ],
                    ],
                    [
                        [
                            'field' => "field_{$prefix}_dynamic_source",
                            'operator' => '==',
                            'value' => 'post',
                        ],
                    ],
                ],
                'default_value' => 'Ver más',
                'maxlength' => 30,
                'placeholder' => 'ej: Ver Detalles, Leer más, Explorar',
            ],

            // Selector de Taxonomía para Badge - PACKAGES
            [
                'key' => "field_{$prefix}_badge_taxonomy_package",
                'label' => '🏷️ Taxonomía del Badge',
                'name' => "{$prefix}_badge_taxonomy",
                'type' => 'select',
                'instructions' => 'Selecciona qué taxonomía mostrar en el badge de las cards. Si no seleccionas nada, usa la lógica por defecto (Service Type → Package Type)',
                'conditional_logic' => [
                    [
                        [
                            'field' => "field_{$prefix}_dynamic_source",
                            'operator' => '==',
                            'value' => 'package',
                        ],
                    ],
                ],
                'choices' => [
                    '' => 'Por defecto (Service Type o Package Type)',
                    'service_type' => 'Tipo de Servicio (Compartido/Privado)',
                    'package_type' => 'Tipo de Paquete',
                    'interest' => 'Intereses',
                    'locations' => 'Ubicaciones',
                    'included_services' => 'Servicios Incluidos',
                    'additional_info' => 'Información Adicional',
                    'tag_locations' => 'Tags de Ubicación',
                    'optional_renting' => 'Alquileres Opcionales',
                ],
                'default_value' => '',
                'allow_null' => 1,
                'ui' => 1,
                'return_format' => 'value',
            ],

            // Selector de Taxonomía para Badge - BLOG POSTS
            [
                'key' => "field_{$prefix}_badge_taxonomy_post",
                'label' => '🏷️ Taxonomía del Badge',
                'name' => "{$prefix}_badge_taxonomy",
                'type' => 'select',
                'instructions' => 'Selecciona qué taxonomía mostrar en el badge de las cards. Si no seleccionas nada, muestra la primera categoría',
                'conditional_logic' => [
                    [
                        [
                            'field' => "field_{$prefix}_dynamic_source",
                            'operator' => '==',
                            'value' => 'post',
                        ],
                    ],
                ],
                'choices' => [
                    '' => 'Por defecto (Primera Categoría)',
                    'category' => 'Categoría',
                    'post_tag' => 'Etiqueta (Tag)',
                ],
                'default_value' => '',
                'allow_null' => 1,
                'ui' => 1,
                'return_format' => 'value',
            ],
        ];
    }

    /**
     * Get array of all filter field definitions for ACF
     * This can be included in any block's register_fields() method
     *
     * @param string $prefix Field key prefix (e.g., 'fgc', 'hc', 'pc')
     * @return array Array of ACF field definitions
     */
    public static function get_filter_fields($prefix) {
        return [
            // ===== TAB: FILTROS =====
            [
                'key' => "field_{$prefix}_tab_filters",
                'label' => '🔍 Filtros',
                'type' => 'tab',
                'placement' => 'top',
                'conditional_logic' => [
                    [
                        [
                            'field' => "field_{$prefix}_dynamic_source",
                            'operator' => '==',
                            'value' => 'package',
                        ],
                    ],
                ],
            ],

            // Package Type
            [
                'key' => "field_{$prefix}_filter_package_type",
                'label' => '🎯 Tipo de Paquete',
                'name' => "{$prefix}_filter_package_type",
                'type' => 'taxonomy',
                'instructions' => 'Filtrar por tipo de paquete (ej: Full Day Tour, Multi-Day Trek)',
                'taxonomy' => 'package_type',
                'field_type' => 'checkbox',
                'add_term' => 0,
                'save_terms' => 0,
                'load_terms' => 0,
                'return_format' => 'id',
                'multiple' => 1,
            ],

            // Interest
            [
                'key' => "field_{$prefix}_filter_interest",
                'label' => '🎨 Intereses',
                'name' => "{$prefix}_filter_interest",
                'type' => 'taxonomy',
                'instructions' => 'Filtrar por intereses (ej: Culture, History, Photography)',
                'taxonomy' => 'interest',
                'field_type' => 'checkbox',
                'add_term' => 0,
                'save_terms' => 0,
                'load_terms' => 0,
                'return_format' => 'id',
                'multiple' => 1,
            ],

            // Locations
            [
                'key' => "field_{$prefix}_filter_locations",
                'label' => '📍 Ubicaciones',
                'name' => "{$prefix}_filter_locations",
                'type' => 'taxonomy',
                'instructions' => 'Filtrar por ubicaciones (ej: Cusco, Machu Picchu)',
                'taxonomy' => 'locations',
                'field_type' => 'checkbox',
                'add_term' => 0,
                'save_terms' => 0,
                'load_terms' => 0,
                'return_format' => 'id',
                'multiple' => 1,
            ],

            // Optional Renting
            [
                'key' => "field_{$prefix}_filter_optional_renting",
                'label' => '🚗 Alquileres Opcionales',
                'name' => "{$prefix}_filter_optional_renting",
                'type' => 'taxonomy',
                'instructions' => 'Filtrar por servicios de alquiler disponibles',
                'taxonomy' => 'optional_renting',
                'field_type' => 'checkbox',
                'add_term' => 0,
                'save_terms' => 0,
                'load_terms' => 0,
                'return_format' => 'id',
                'multiple' => 1,
            ],

            // Included Services
            [
                'key' => "field_{$prefix}_filter_included_services",
                'label' => '✅ Servicios Incluidos',
                'name' => "{$prefix}_filter_included_services",
                'type' => 'taxonomy',
                'instructions' => 'Filtrar por servicios que incluye el paquete',
                'taxonomy' => 'included_services',
                'field_type' => 'checkbox',
                'add_term' => 0,
                'save_terms' => 0,
                'load_terms' => 0,
                'return_format' => 'id',
                'multiple' => 1,
            ],

            // Additional Info
            [
                'key' => "field_{$prefix}_filter_additional_info",
                'label' => 'ℹ️ Información Adicional',
                'name' => "{$prefix}_filter_additional_info",
                'type' => 'taxonomy',
                'instructions' => 'Filtrar por información adicional del paquete',
                'taxonomy' => 'additional_info',
                'field_type' => 'checkbox',
                'add_term' => 0,
                'save_terms' => 0,
                'load_terms' => 0,
                'return_format' => 'id',
                'multiple' => 1,
            ],

            // Tag Locations
            [
                'key' => "field_{$prefix}_filter_tag_locations",
                'label' => '🏷️ Tags de Ubicación',
                'name' => "{$prefix}_filter_tag_locations",
                'type' => 'taxonomy',
                'instructions' => 'Filtrar por tags de ubicación',
                'taxonomy' => 'tag_locations',
                'field_type' => 'checkbox',
                'add_term' => 0,
                'save_terms' => 0,
                'load_terms' => 0,
                'return_format' => 'id',
                'multiple' => 1,
            ],

            // Message: Filtros de Campos ACF
            [
                'key' => "field_{$prefix}_filter_acf_message",
                'label' => '🎛️ Filtros por Campos ACF',
                'type' => 'message',
                'message' => 'Filtra los packages por características específicas como tipo de servicio, nivel de actividad, precio, duración, etc.',
                'new_lines' => 'wpautop',
                'esc_html' => 0,
            ],

            // Service Type
            [
                'key' => "field_{$prefix}_filter_service_type",
                'label' => '🎯 Tipo de Servicio',
                'name' => "{$prefix}_filter_service_type",
                'type' => 'checkbox',
                'instructions' => 'Filtrar por tipo de servicio',
                'choices' => [
                    'shared' => 'Compartido',
                    'private' => 'Privado',
                ],
                'layout' => 'horizontal',
                'return_format' => 'value',
            ],

            // Activity Level
            [
                'key' => "field_{$prefix}_filter_activity_level",
                'label' => '🏃 Nivel de Actividad',
                'name' => "{$prefix}_filter_activity_level",
                'type' => 'checkbox',
                'instructions' => 'Filtrar por nivel de actividad física',
                'choices' => [
                    'low' => 'Bajo',
                    'moderate' => 'Moderado',
                    'high' => 'Alto',
                    'very_high' => 'Muy Alto',
                ],
                'layout' => 'vertical',
                'return_format' => 'value',
            ],

            // Physical Difficulty
            [
                'key' => "field_{$prefix}_filter_difficulty",
                'label' => '💪 Dificultad Física',
                'name' => "{$prefix}_filter_difficulty",
                'type' => 'checkbox',
                'instructions' => 'Filtrar por nivel de dificultad física',
                'choices' => [
                    'easy' => 'Fácil',
                    'moderate' => 'Moderado',
                    'moderate_demanding' => 'Moderado Exigente',
                    'difficult' => 'Difícil',
                    'very_difficult' => 'Muy Difícil',
                ],
                'layout' => 'vertical',
                'return_format' => 'value',
            ],

            // Price Range - Minimum
            [
                'key' => "field_{$prefix}_filter_price_min",
                'label' => '💰 Precio Mínimo (USD)',
                'name' => "{$prefix}_filter_price_min",
                'type' => 'number',
                'instructions' => 'Precio mínimo en dólares',
                'min' => 0,
                'step' => 1,
                'placeholder' => 'ej: 50',
            ],

            // Price Range - Maximum
            [
                'key' => "field_{$prefix}_filter_price_max",
                'label' => '💰 Precio Máximo (USD)',
                'name' => "{$prefix}_filter_price_max",
                'type' => 'number',
                'instructions' => 'Precio máximo en dólares',
                'min' => 0,
                'step' => 1,
                'placeholder' => 'ej: 500',
            ],

            // Days Range - Minimum
            [
                'key' => "field_{$prefix}_filter_days_min",
                'label' => '📅 Mínimo de Días',
                'name' => "{$prefix}_filter_days_min",
                'type' => 'number',
                'instructions' => 'Duración mínima en días',
                'min' => 1,
                'step' => 1,
                'placeholder' => 'ej: 1',
            ],

            // Days Range - Maximum
            [
                'key' => "field_{$prefix}_filter_days_max",
                'label' => '📅 Máximo de Días',
                'name' => "{$prefix}_filter_days_max",
                'type' => 'number',
                'instructions' => 'Duración máxima en días',
                'min' => 1,
                'step' => 1,
                'placeholder' => 'ej: 7',
            ],

            // Featured Only
            [
                'key' => "field_{$prefix}_filter_featured_only",
                'label' => '⭐ Solo Destacados',
                'name' => "{$prefix}_filter_featured_only",
                'type' => 'true_false',
                'instructions' => 'Mostrar solo packages marcados como destacados',
                'ui' => 1,
                'default_value' => 0,
            ],

            // Has Active Promotion
            [
                'key' => "field_{$prefix}_filter_active_promo",
                'label' => '🏷️ Solo con Promoción Activa',
                'name' => "{$prefix}_filter_active_promo",
                'type' => 'true_false',
                'instructions' => 'Mostrar solo packages con promoción activa (active_promotion = 1)',
                'ui' => 1,
                'default_value' => 0,
            ],

            // Minimum Rating
            [
                'key' => "field_{$prefix}_filter_min_rating",
                'label' => '⭐ Rating Mínimo',
                'name' => "{$prefix}_filter_min_rating",
                'type' => 'select',
                'instructions' => 'Filtrar por calificación mínima',
                'choices' => [
                    '' => 'Sin filtro',
                    '3' => '⭐⭐⭐ o más (3+)',
                    '4' => '⭐⭐⭐⭐ o más (4+)',
                    '5' => '⭐⭐⭐⭐⭐ (solo 5 estrellas)',
                ],
                'default_value' => '',
                'allow_null' => 1,
                'ui' => 1,
            ],

            // ===== TAB: FILTROS DE BLOG POSTS =====
            [
                'key' => "field_{$prefix}_tab_filters_post",
                'label' => '🔍 Filtros de Blog',
                'type' => 'tab',
                'placement' => 'top',
                'conditional_logic' => [
                    [
                        [
                            'field' => "field_{$prefix}_dynamic_source",
                            'operator' => '==',
                            'value' => 'post',
                        ],
                    ],
                ],
            ],

            // Category Filter
            [
                'key' => "field_{$prefix}_filter_category",
                'label' => '📁 Categorías',
                'name' => "{$prefix}_filter_category",
                'type' => 'taxonomy',
                'instructions' => 'Filtrar por categorías del blog',
                'taxonomy' => 'category',
                'field_type' => 'checkbox',
                'add_term' => 0,
                'save_terms' => 0,
                'load_terms' => 0,
                'return_format' => 'id',
                'multiple' => 1,
                'conditional_logic' => [
                    [
                        [
                            'field' => "field_{$prefix}_dynamic_source",
                            'operator' => '==',
                            'value' => 'post',
                        ],
                    ],
                ],
            ],

            // Tag Filter
            [
                'key' => "field_{$prefix}_filter_post_tag",
                'label' => '🏷️ Etiquetas',
                'name' => "{$prefix}_filter_post_tag",
                'type' => 'taxonomy',
                'instructions' => 'Filtrar por etiquetas del blog',
                'taxonomy' => 'post_tag',
                'field_type' => 'checkbox',
                'add_term' => 0,
                'save_terms' => 0,
                'load_terms' => 0,
                'return_format' => 'id',
                'multiple' => 1,
                'conditional_logic' => [
                    [
                        [
                            'field' => "field_{$prefix}_dynamic_source",
                            'operator' => '==',
                            'value' => 'post',
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Prepare blog post data to match card structure
     *
     * @param \WP_Post $post Post object
     * @param string $prefix Field prefix to use
     * @return array Card data array
     */
    public static function prepare_post_card_data($post, $prefix = '') {
        $post_id = $post->ID;
        $field_prefix = $prefix ? $prefix . '_' : '';

        // Get visible fields (simpler for posts)
        $visible_fields = get_field($field_prefix . 'dynamic_visible_fields')
            ?: ['image', 'category', 'title', 'description'];
        $cta_text = get_field($field_prefix . 'dynamic_cta_text') ?: 'Leer más';

        $card_data = [
            'acf_fc_layout' => 'card',
        ];

        // Image (featured image)
        if (in_array('image', $visible_fields)) {
            $image = null;
            if (has_post_thumbnail($post_id)) {
                $image_id = get_post_thumbnail_id($post_id);
                $image = [
                    'url' => get_the_post_thumbnail_url($post_id, 'full'),
                    'sizes' => [
                        'large' => get_the_post_thumbnail_url($post_id, 'large'),
                        'medium' => get_the_post_thumbnail_url($post_id, 'medium')
                    ],
                    'alt' => get_post_meta($image_id, '_wp_attachment_image_alt', true)
                ];
            }
            $card_data['image'] = $image;
        }

        // Category/Badge
        if (in_array('category', $visible_fields)) {
            $category = '';
            // Get badge color from block settings (default: secondary)
            $badge_color = get_field('badge_color_variant') ?: 'secondary';

            // Get selected badge taxonomy from ACF field
            $badge_taxonomy = get_field($field_prefix . 'badge_taxonomy');

            // Use selected taxonomy if specified
            if (!empty($badge_taxonomy)) {
                if ($badge_taxonomy === 'category') {
                    // Get first category
                    $categories = get_the_category($post_id);
                    if (!empty($categories)) {
                        $category = ucwords(strtolower($categories[0]->name)); // Capitalize properly
                    }
                } elseif ($badge_taxonomy === 'post_tag') {
                    // Get first tag
                    $tags = get_the_tags($post_id);
                    if (!empty($tags) && !is_wp_error($tags)) {
                        $category = ucwords(strtolower($tags[0]->name)); // Capitalize properly
                    }
                }
            }
            // Default: first category
            else {
                $categories = get_the_category($post_id);
                if (!empty($categories)) {
                    $category = ucwords(strtolower($categories[0]->name)); // Capitalize properly
                }
            }

            $card_data['category'] = $category;
            $card_data['badge_color_variant'] = $badge_color;
        }

        // Title
        if (in_array('title', $visible_fields)) {
            $card_data['title'] = get_the_title($post);
        }

        // Description (excerpt)
        if (in_array('description', $visible_fields)) {
            $description = get_the_excerpt($post);
            $card_data['description'] = $description;
            $card_data['excerpt'] = $description;
        }

        // Date
        $card_data['date'] = get_the_date('F j, Y', $post);

        // Link and CTA
        $card_data['link'] = [
            'url' => get_permalink($post),
            'title' => $cta_text,
            'target' => ''
        ];
        $card_data['cta_text'] = $cta_text;

        return $card_data;
    }

    /**
     * Initialize ACF filters for dynamic field population
     *
     * Call this method in plugin bootstrap to enable dynamic choices
     */
    public static function init_acf_filters() {
        // Populate deal selector choices dynamically
        add_filter('acf/load_field', function($field) {
            // Check if this is a deal_selector field
            if (isset($field['name']) && strpos($field['name'], '_deal_selector') !== false) {
                $field['choices'] = self::get_all_deals();

                // If no deals available, show message
                if (empty($field['choices'])) {
                    $field['choices'] = ['' => 'No hay deals activos disponibles'];
                }
            }

            return $field;
        });
    }
}
