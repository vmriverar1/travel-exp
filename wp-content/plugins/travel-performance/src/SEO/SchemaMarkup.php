<?php
/**
 * Schema Markup Generator
 *
 * Generates JSON-LD structured data for improved SEO.
 *
 * @package Travel\Performance\SEO
 * @since 1.0.0
 */

namespace Travel\Performance\SEO;

class SchemaMarkup
{
    /**
     * Register schema markup hooks.
     *
     * @return void
     */
    public function register(): void
    {
        // Add schema markup to head
        add_action('wp_head', [$this, 'output_schema_markup'], 99);
    }

    /**
     * Output schema markup based on current page.
     *
     * @return void
     */
    public function output_schema_markup(): void
    {
        $schema = [];

        // Organization schema (sitewide)
        $schema[] = $this->get_organization_schema();

        // Page-specific schema
        if (is_singular('tour')) {
            $schema[] = $this->get_tour_product_schema();
        } elseif (is_singular('review')) {
            $schema[] = $this->get_review_schema();
        } elseif (is_front_page()) {
            $schema[] = $this->get_website_schema();
        }

        // Breadcrumb schema (all pages except homepage)
        if (!is_front_page()) {
            $breadcrumb = $this->get_breadcrumb_schema();
            if ($breadcrumb) {
                $schema[] = $breadcrumb;
            }
        }

        // Output JSON-LD
        if (!empty($schema)) {
            echo '<script type="application/ld+json">' . "\n";
            echo wp_json_encode(['@context' => 'https://schema.org', '@graph' => $schema], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
            echo "\n" . '</script>' . "\n";
        }
    }

    /**
     * Get organization schema.
     *
     * @return array
     */
    private function get_organization_schema(): array
    {
        $logo = get_field('site_logo', 'option');
        $logo_url = is_array($logo) ? $logo['url'] : '';

        return [
            '@type' => 'Organization',
            'name' => get_bloginfo('name'),
            'url' => home_url(),
            'logo' => $logo_url ?: get_site_icon_url(),
            'description' => get_bloginfo('description'),
            'sameAs' => array_filter([
                get_field('social_facebook', 'option'),
                get_field('social_instagram', 'option'),
                get_field('social_twitter', 'option'),
                get_field('social_youtube', 'option'),
                get_field('social_tripadvisor', 'option'),
            ]),
            'contactPoint' => [
                '@type' => 'ContactPoint',
                'telephone' => get_field('contact_phone', 'option'),
                'email' => get_field('contact_email', 'option'),
                'contactType' => 'Customer Service',
            ],
        ];
    }

    /**
     * Get website schema.
     *
     * @return array
     */
    private function get_website_schema(): array
    {
        return [
            '@type' => 'WebSite',
            'name' => get_bloginfo('name'),
            'url' => home_url(),
            'description' => get_bloginfo('description'),
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => home_url('/?s={search_term_string}'),
                'query-input' => 'required name=search_term_string',
            ],
        ];
    }

    /**
     * Get tour product schema.
     *
     * @return array
     */
    private function get_tour_product_schema(): array
    {
        $post_id = get_the_ID();

        $price = get_field('tour_price', $post_id);
        $sale_price = get_field('tour_sale_price', $post_id);
        $currency = get_field('tour_currency', $post_id) ?: 'USD';
        $duration_days = get_field('tour_duration_days', $post_id);
        $thumbnail = get_the_post_thumbnail_url($post_id, 'large');

        $schema = [
            '@type' => 'Product',
            'name' => get_the_title(),
            'description' => wp_strip_all_tags(get_the_excerpt()),
            'image' => $thumbnail ?: '',
            'url' => get_permalink(),
            'offers' => [
                '@type' => 'Offer',
                'price' => $sale_price ?: $price,
                'priceCurrency' => $currency,
                'availability' => 'https://schema.org/InStock',
                'url' => get_permalink(),
            ],
        ];

        // Add duration if available
        if ($duration_days) {
            $schema['duration'] = 'P' . $duration_days . 'D';
        }

        // Add aggregate rating if reviews exist
        $rating = $this->get_aggregate_rating($post_id);
        if ($rating) {
            $schema['aggregateRating'] = $rating;
        }

        return $schema;
    }

    /**
     * Get review schema.
     *
     * @return array
     */
    private function get_review_schema(): array
    {
        $post_id = get_the_ID();

        $rating = get_field('rating', $post_id);
        $client_name = get_field('client_name', $post_id);
        $review_date = get_field('review_date', $post_id) ?: get_the_date('Y-m-d');

        return [
            '@type' => 'Review',
            'reviewRating' => [
                '@type' => 'Rating',
                'ratingValue' => $rating,
                'bestRating' => '5',
            ],
            'author' => [
                '@type' => 'Person',
                'name' => $client_name ?: 'Anonymous',
            ],
            'reviewBody' => wp_strip_all_tags(get_the_content()),
            'datePublished' => $review_date,
        ];
    }

    /**
     * Get breadcrumb schema.
     *
     * @return array|null
     */
    private function get_breadcrumb_schema(): ?array
    {
        $items = [];
        $position = 1;

        // Home
        $items[] = [
            '@type' => 'ListItem',
            'position' => $position++,
            'name' => 'Home',
            'item' => home_url(),
        ];

        // Current page
        if (is_singular()) {
            $post_type = get_post_type();
            $post_type_object = get_post_type_object($post_type);

            // Add post type archive
            if ($post_type_object && $post_type_object->has_archive) {
                $items[] = [
                    '@type' => 'ListItem',
                    'position' => $position++,
                    'name' => $post_type_object->labels->name,
                    'item' => get_post_type_archive_link($post_type),
                ];
            }

            // Add current post
            $items[] = [
                '@type' => 'ListItem',
                'position' => $position++,
                'name' => get_the_title(),
                'item' => get_permalink(),
            ];
        } elseif (is_post_type_archive()) {
            $post_type = get_query_var('post_type');
            $post_type_object = get_post_type_object($post_type);

            $items[] = [
                '@type' => 'ListItem',
                'position' => $position++,
                'name' => $post_type_object->labels->name,
                'item' => get_post_type_archive_link($post_type),
            ];
        }

        if (empty($items) || count($items) < 2) {
            return null;
        }

        return [
            '@type' => 'BreadcrumbList',
            'itemListElement' => $items,
        ];
    }

    /**
     * Get aggregate rating for a tour.
     *
     * @param int $tour_id Tour post ID
     *
     * @return array|null
     */
    private function get_aggregate_rating(int $tour_id): ?array
    {
        // Query reviews for this tour
        $reviews = new \WP_Query([
            'post_type' => 'review',
            'posts_per_page' => -1,
            'meta_query' => [
                [
                    'key' => 'related_tour',
                    'value' => $tour_id,
                ],
                [
                    'key' => 'rating',
                    'value' => 0,
                    'compare' => '>',
                    'type' => 'NUMERIC',
                ],
            ],
            'fields' => 'ids',
        ]);

        if ($reviews->post_count === 0) {
            return null;
        }

        // Calculate average rating
        $total_rating = 0;
        foreach ($reviews->posts as $review_id) {
            $rating = get_field('rating', $review_id);
            $total_rating += (float) $rating;
        }

        $average_rating = round($total_rating / $reviews->post_count, 1);

        return [
            '@type' => 'AggregateRating',
            'ratingValue' => $average_rating,
            'reviewCount' => $reviews->post_count,
            'bestRating' => '5',
            'worstRating' => '1',
        ];
    }
}
