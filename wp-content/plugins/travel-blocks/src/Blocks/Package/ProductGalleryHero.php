<?php
/**
 * Block: Product Gallery Hero
 *
 * Full-width image gallery carousel with:
 * - Diagonal discount ribbon
 * - Circular thumbnail navigation
 * - View Photos button
 * - Lightbox integration
 *
 * NATIVE WORDPRESS BLOCK - Does NOT use ACF
 * Gets data from Package post meta fields
 *
 * @package Travel\Blocks\Blocks\Package
 * @since 1.0.0
 */

namespace Travel\Blocks\Blocks\Package;

use Travel\Blocks\Helpers\EditorHelper;

class ProductGalleryHero
{
    private string $name = 'product-gallery-hero';
    private string $title = 'Product Gallery Hero';
    private string $description = 'Galería de imágenes full-width con cinta promocional, thumbnails y lightbox';

    /**
     * Register the block
     */
    public function register(): void
    {
        register_block_type('travel-blocks/' . $this->name, [
            'api_version' => 2,
            'title' => __($this->title, 'travel-blocks'),
            'description' => __($this->description, 'travel-blocks'),
            'category' => 'travel',
            'icon' => 'format-gallery',
            'keywords' => ['gallery', 'hero', 'carousel', 'images', 'package', 'discount'],
            'supports' => [
                'align' => ['wide', 'full'],
                'anchor' => true,
                'html' => false,
            ],
            'render_callback' => [$this, 'render'],
            'show_in_rest' => true,
        ]);

        // Enqueue assets
        add_action('enqueue_block_assets', [$this, 'enqueue_assets']);
    }

    /**
     * Enqueue block-specific assets
     */
    public function enqueue_assets(): void
    {
        if (!is_admin()) {
            // Swiper CSS
            wp_enqueue_style(
                'swiper-css',
                'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css',
                [],
                '11.0.0'
            );

            // GLightbox CSS
            wp_enqueue_style(
                'glightbox-css',
                'https://cdn.jsdelivr.net/npm/glightbox@3.2.0/dist/css/glightbox.min.css',
                [],
                '3.2.0'
            );

            // Block CSS
            wp_enqueue_style(
                'product-gallery-hero-style',
                TRAVEL_BLOCKS_URL . 'assets/blocks/product-gallery-hero.css',
                ['swiper-css', 'glightbox-css'],
                TRAVEL_BLOCKS_VERSION
            );

            // Swiper JS
            wp_enqueue_script(
                'swiper-js',
                'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js',
                [],
                '11.0.0',
                true
            );

            // GLightbox JS
            wp_enqueue_script(
                'glightbox-js',
                'https://cdn.jsdelivr.net/npm/glightbox@3.2.0/dist/js/glightbox.min.js',
                [],
                '3.2.0',
                true
            );

            // Block JS
            wp_enqueue_script(
                'product-gallery-hero-script',
                TRAVEL_BLOCKS_URL . 'assets/blocks/product-gallery-hero.js',
                ['swiper-js', 'glightbox-js'],
                TRAVEL_BLOCKS_VERSION,
                true
            );
        }
    }

    /**
     * Render block content
     */
    public function render($attributes, $content, $block): string
    {
        try {
            $post_id = get_the_ID();
            $is_preview = EditorHelper::is_editor_mode($post_id);

            // Obtener datos
            if ($is_preview || !$post_id) {
                $data = $this->get_preview_data();
            } else {
                $data = $this->get_post_data($post_id);
            }

            // Block attributes
            $block_id = 'product-gallery-hero-' . uniqid();
            $class_name = 'product-gallery-hero';

            if (!empty($attributes['className'])) {
                $class_name .= ' ' . $attributes['className'];
            }

            if (!empty($attributes['align'])) {
                $class_name .= ' align' . $attributes['align'];
            }

            $data['block_id'] = $block_id;
            $data['class_name'] = $class_name;
            $data['is_preview'] = $is_preview;

            // Renderizar template
            ob_start();
            $this->load_template('product-gallery-hero', $data);
            return ob_get_clean();

        } catch (\Exception $e) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                return '<div style="padding: 10px; background: #ffebee; border: 1px solid #f44336;">' .
                       '<p>Error en Product Gallery Hero: ' . esc_html($e->getMessage()) . '</p>' .
                       '</div>';
            }
            return '';
        }
    }

    /**
     * Get preview data (for editor)
     */
    private function get_preview_data(): array
    {
        return [
            'gallery' => [
                ['url' => 'https://picsum.photos/1200/600?random=1', 'alt' => 'Machu Picchu'],
                ['url' => 'https://picsum.photos/1200/600?random=2', 'alt' => 'Inca Trail'],
                ['url' => 'https://picsum.photos/1200/600?random=3', 'alt' => 'Sacred Valley'],
            ],
            'show_discount' => true,
            'discount_text' => '15% OFF',
            'discount_color' => '#E78C85',
            'badge_position' => 'top-left',
            'show_thumbnails' => true,
            'thumbnail_shape' => 'circle',
            'show_view_photos' => true,
            'button_text' => 'View all Photos',
            'enable_lightbox' => true,
            'autoplay_interval' => 0,
            'activity_level' => 'moderate',
            'activity_label' => 'Moderate',
            'activity_dots_count' => 3,
        ];
    }

    /**
     * Get package data from post meta
     */
    private function get_post_data(int $post_id): array
    {
        // Obtener galería (ACF Gallery field)
        $gallery_raw = get_post_meta($post_id, 'gallery', true);
        $gallery = [];

        if (!empty($gallery_raw) && is_array($gallery_raw)) {
            foreach ($gallery_raw as $image_id) {
                if (is_numeric($image_id)) {
                    $gallery[] = [
                        'url' => wp_get_attachment_image_url($image_id, 'full'),
                        'alt' => get_post_meta($image_id, '_wp_attachment_image_alt', true),
                    ];
                } elseif (is_array($image_id) && isset($image_id['ID'])) {
                    $gallery[] = [
                        'url' => $image_id['url'] ?? wp_get_attachment_image_url($image_id['ID'], 'full'),
                        'alt' => $image_id['alt'] ?? '',
                    ];
                }
            }
        }

        // Si no hay galería, usar featured image
        if (empty($gallery)) {
            $featured_id = get_post_thumbnail_id($post_id);
            if ($featured_id) {
                $gallery[] = [
                    'url' => get_the_post_thumbnail_url($post_id, 'full'),
                    'alt' => get_post_meta($featured_id, '_wp_attachment_image_alt', true),
                ];
            }
        }

        // Discount/Promo data
        $promo_text = get_post_meta($post_id, 'promo_tag', true) ?: get_post_meta($post_id, 'discount_text', true);
        $promo_color = get_post_meta($post_id, 'promo_tag_color', true) ?: '#E78C85';
        $promo_enabled = get_post_meta($post_id, 'promo_enabled', true) === '1' || !empty($promo_text);

        // Activity Level data
        $activity_level = get_post_meta($post_id, 'activity_level', true);
        $activity_labels = [
            'low' => 'Low',
            'moderate' => 'Moderate',
            'high' => 'High',
            'very_high' => 'Very High',
        ];
        $activity_dots = [
            'low' => 2,
            'moderate' => 3,
            'high' => 4,
            'very_high' => 5,
        ];

        return [
            'gallery' => $gallery,
            'show_discount' => $promo_enabled,
            'discount_text' => $promo_text,
            'discount_color' => $promo_color,
            'badge_position' => 'top-left',
            'show_thumbnails' => true,
            'thumbnail_shape' => 'circle',
            'show_view_photos' => true,
            'button_text' => __('View all Photos', 'travel-blocks'),
            'enable_lightbox' => true,
            'autoplay_interval' => 0,
            'activity_level' => $activity_level,
            'activity_label' => $activity_labels[$activity_level] ?? '',
            'activity_dots_count' => $activity_dots[$activity_level] ?? 0,
        ];
    }

    /**
     * Load block template
     */
    protected function load_template(string $template_name, array $data = []): void
    {
        $template_path = TRAVEL_BLOCKS_PATH . 'templates/' . $template_name . '.php';

        if (!file_exists($template_path)) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                echo '<div style="padding:1rem;background:#fff3cd;border-left:4px solid #ffc107;">';
                echo '<strong>Template not found:</strong> ' . esc_html($template_name . '.php');
                echo '</div>';
            }
            return;
        }

        extract($data, EXTR_SKIP);
        include $template_path;
    }
}
