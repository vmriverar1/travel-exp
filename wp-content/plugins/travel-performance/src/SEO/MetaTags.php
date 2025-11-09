<?php
/**
 * Meta Tags Generator
 *
 * Generates dynamic meta tags for SEO, Open Graph, and Twitter Cards.
 *
 * @package Travel\Performance\SEO
 * @since 1.0.0
 */

namespace Travel\Performance\SEO;

class MetaTags
{
    /**
     * Register meta tags hooks.
     *
     * @return void
     */
    public function register(): void
    {
        // Remove default WordPress meta tags
        remove_action('wp_head', '_wp_render_title_tag', 1);

        // Add custom meta tags
        add_action('wp_head', [$this, 'output_meta_tags'], 1);
    }

    /**
     * Output all meta tags.
     *
     * @return void
     */
    public function output_meta_tags(): void
    {
        ?>
<!-- SEO Meta Tags -->
<title><?php echo esc_html($this->get_title()); ?></title>
<meta name="description" content="<?php echo esc_attr($this->get_description()); ?>">
<meta name="robots" content="<?php echo esc_attr($this->get_robots()); ?>">
<link rel="canonical" href="<?php echo esc_url($this->get_canonical_url()); ?>">

<!-- Open Graph Tags -->
<meta property="og:type" content="<?php echo esc_attr($this->get_og_type()); ?>">
<meta property="og:title" content="<?php echo esc_attr($this->get_title()); ?>">
<meta property="og:description" content="<?php echo esc_attr($this->get_description()); ?>">
<meta property="og:url" content="<?php echo esc_url($this->get_canonical_url()); ?>">
<meta property="og:site_name" content="<?php echo esc_attr(get_bloginfo('name')); ?>">
<?php if ($image = $this->get_og_image()): ?>
<meta property="og:image" content="<?php echo esc_url($image); ?>">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<?php endif; ?>
<meta property="og:locale" content="<?php echo esc_attr(get_locale()); ?>">

<!-- Twitter Card Tags -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?php echo esc_attr($this->get_title()); ?>">
<meta name="twitter:description" content="<?php echo esc_attr($this->get_description()); ?>">
<?php if ($image = $this->get_og_image()): ?>
<meta name="twitter:image" content="<?php echo esc_url($image); ?>">
<?php endif; ?>

        <?php
    }

    /**
     * Get page title.
     *
     * @return string
     */
    private function get_title(): string
    {
        $title = '';
        $separator = '|';
        $site_name = get_bloginfo('name');

        if (is_singular('tour')) {
            $tour_title = get_the_title();
            $duration = get_field('tour_duration_days');
            $suffix = $duration ? " - {$duration} Day Tour" : '';
            $title = $tour_title . $suffix . " {$separator} {$site_name}";
        } elseif (is_singular('destination')) {
            $title = get_the_title() . " Travel Guide {$separator} {$site_name}";
        } elseif (is_singular('deal')) {
            $title = get_the_title() . " - Special Offer {$separator} {$site_name}";
        } elseif (is_post_type_archive('tour')) {
            $title = "Peru Tours & Travel Packages {$separator} {$site_name}";
        } elseif (is_post_type_archive('destination')) {
            $title = "Destinations in Peru {$separator} {$site_name}";
        } elseif (is_front_page()) {
            $title = $site_name . " {$separator} " . get_bloginfo('description');
        } elseif (is_singular()) {
            $title = get_the_title() . " {$separator} {$site_name}";
        } elseif (is_search()) {
            $title = sprintf('Search Results for "%s" %s %s', get_search_query(), $separator, $site_name);
        } elseif (is_404()) {
            $title = "Page Not Found {$separator} {$site_name}";
        } else {
            $title = wp_get_document_title();
        }

        return $title;
    }

    /**
     * Get meta description.
     *
     * @return string
     */
    private function get_description(): string
    {
        $description = '';

        if (is_singular('tour')) {
            $excerpt = get_the_excerpt();
            $price = get_field('tour_price');
            $currency = get_field('tour_currency') ?: 'USD';

            $description = wp_trim_words($excerpt, 25);

            if ($price) {
                $description .= " Starting from {$currency} {$price}.";
            }

            $description .= " Book now for an unforgettable Peru adventure.";
        } elseif (is_singular('destination')) {
            $description = wp_trim_words(get_the_excerpt(), 30);
        } elseif (is_post_type_archive('tour')) {
            $description = "Explore our curated collection of Peru tours and travel packages. Machu Picchu, Cusco, Amazon, and more. Expert guides, best prices guaranteed.";
        } elseif (is_front_page()) {
            $description = get_bloginfo('description');

            if (!$description) {
                $description = "Discover Peru with our expertly curated tours. Visit Machu Picchu, explore Cusco, trek the Inca Trail, and experience the Amazon rainforest.";
            }
        } elseif (is_singular()) {
            $description = wp_trim_words(get_the_excerpt(), 30);
        }

        if (!$description) {
            $description = get_bloginfo('description');
        }

        return wp_strip_all_tags($description);
    }

    /**
     * Get robots meta content.
     *
     * @return string
     */
    private function get_robots(): string
    {
        // No index for search, 404, and private pages
        if (is_search() || is_404() || post_password_required()) {
            return 'noindex, nofollow';
        }

        // No index for paginated pages beyond page 1
        if (is_paged() && get_query_var('paged') > 1) {
            return 'noindex, follow';
        }

        return 'index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1';
    }

    /**
     * Get canonical URL.
     *
     * @return string
     */
    private function get_canonical_url(): string
    {
        global $wp;

        if (is_singular()) {
            return get_permalink();
        }

        if (is_front_page()) {
            return home_url('/');
        }

        return home_url(add_query_arg([], $wp->request));
    }

    /**
     * Get Open Graph type.
     *
     * @return string
     */
    private function get_og_type(): string
    {
        if (is_singular('tour') || is_singular('deal')) {
            return 'product';
        }

        if (is_singular()) {
            return 'article';
        }

        return 'website';
    }

    /**
     * Get Open Graph image.
     *
     * @return string|null
     */
    private function get_og_image(): ?string
    {
        $image = null;

        // Featured image for single posts
        if (is_singular() && has_post_thumbnail()) {
            $thumbnail_id = get_post_thumbnail_id();
            $image_array = wp_get_attachment_image_src($thumbnail_id, 'large');
            $image = $image_array[0] ?? null;
        }

        // Tour gallery first image
        if (!$image && is_singular('tour')) {
            $gallery = get_field('tour_gallery');
            if ($gallery && is_array($gallery) && !empty($gallery[0])) {
                $image = $gallery[0]['url'] ?? null;
            }
        }

        // Site logo as fallback
        if (!$image) {
            $logo = get_field('site_logo', 'option');
            $image = is_array($logo) ? ($logo['url'] ?? null) : null;
        }

        // Site icon as last resort
        if (!$image) {
            $image = get_site_icon_url(1200);
        }

        return $image;
    }
}
