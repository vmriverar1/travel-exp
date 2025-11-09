<?php
/**
 * Hero Media Grid Template Block
 *
 * Combines gallery carousel, map image, and video in a 65/35 grid layout
 *
 * @package Travel\Blocks\Blocks\Template
 * @since 2.0.0
 */

namespace Travel\Blocks\Blocks\Template;

use Travel\Blocks\Core\TemplateBlockBase;
use Travel\Blocks\Core\PreviewDataTrait;

class HeroMediaGrid extends TemplateBlockBase
{
    use PreviewDataTrait;

    public function __construct()
    {
        $this->name = 'hero-media-grid';
        $this->title = 'Hero Media Grid';
        $this->description = 'Gallery carousel with map and video in split layout';
        $this->icon = 'format-gallery';
        $this->keywords = ['hero', 'gallery', 'map', 'video', 'carousel', 'media'];
    }

    protected function render_preview(array $attributes): string
    {
        $data = [
            'gallery' => $this->get_preview_images(6),
            'map_image' => 'https://picsum.photos/600/400?random=100',
            'video_embed' => $this->parse_video_embed_url('https://www.youtube.com/watch?v=dQw4w9WgXcQ'),
            'discount_badge' => [
                'show' => true,
                'percentage' => 15,
                'text' => 'Early Bird Discount',
            ],
            'activity_level' => [
                'label' => __('Moderate', 'travel-blocks'),
                'dots' => 3,
            ],
            'is_preview' => true,
        ];

        return $this->load_template('hero-media-grid', $data);
    }

    protected function render_live(int $post_id, array $attributes): string
    {
        $video_url = $this->get_package_video_url($post_id);

        $data = [
            'gallery' => $this->get_package_gallery($post_id),
            'map_image' => $this->get_package_map_image($post_id),
            'video_embed' => $this->parse_video_embed_url($video_url),
            'discount_badge' => $this->get_package_discount($post_id),
            'activity_level' => $this->get_package_physical_difficulty($post_id),
            'is_preview' => false,
        ];

        return $this->load_template('hero-media-grid', $data);
    }

    /**
     * Get package gallery images
     *
     * @param int $post_id Package post ID
     * @return array Gallery images
     */
    private function get_package_gallery(int $post_id): array
    {
        $gallery = get_field('gallery', $post_id);

        if (empty($gallery) || !is_array($gallery)) {
            return [];
        }

        $images = [];
        foreach ($gallery as $image) {
            if (is_array($image)) {
                $images[] = [
                    'url' => $image['url'] ?? '',
                    'alt' => $image['alt'] ?? '',
                    'title' => $image['title'] ?? '',
                ];
            }
        }

        return $images;
    }

    /**
     * Get package map image
     *
     * @param int $post_id Package post ID
     * @return string Map image URL or empty string
     */
    private function get_package_map_image(int $post_id): string
    {
        $map_image = get_field('map_image', $post_id);

        if (is_array($map_image) && !empty($map_image['url'])) {
            return $map_image['url'];
        }

        return '';
    }

    /**
     * Get package video URL
     *
     * @param int $post_id Package post ID
     * @return string Video URL or empty string
     */
    private function get_package_video_url(int $post_id): string
    {
        $video_url = get_field('video_url', $post_id);
        return !empty($video_url) ? $video_url : '';
    }

    /**
     * Get package discount badge data
     *
     * @param int $post_id Package post ID
     * @return array Discount badge data
     */
    private function get_package_discount(int $post_id): array
    {
        $price_normal = (float) get_field('price_normal', $post_id);
        $price_offer = (float) get_field('price_offer', $post_id);

        $show_discount = $price_offer > 0 && $price_offer < $price_normal;
        $percentage = 0;

        if ($show_discount) {
            $percentage = round((($price_normal - $price_offer) / $price_normal) * 100);
        }

        return [
            'show' => $show_discount,
            'percentage' => $percentage,
            'text' => $show_discount ? __('Special Offer', 'travel-blocks') : '',
        ];
    }

    /**
     * Parse video URL and return embed iframe HTML
     *
     * Supports YouTube and Vimeo URLs
     *
     * @param string $video_url Video URL from ACF field
     * @return string Iframe HTML or empty string
     */
    private function parse_video_embed_url(string $video_url): string
    {
        if (empty($video_url)) {
            return '';
        }

        // YouTube
        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $video_url, $matches)) {
            $video_id = sanitize_text_field($matches[1]);
            return sprintf(
                '<iframe src="https://www.youtube.com/embed/%s" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>',
                esc_attr($video_id)
            );
        }

        // Vimeo
        if (preg_match('/vimeo\.com\/([0-9]+)/', $video_url, $matches)) {
            $video_id = sanitize_text_field($matches[1]);
            return sprintf(
                '<iframe src="https://player.vimeo.com/video/%s" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>',
                esc_attr($video_id)
            );
        }

        return '';
    }

    /**
     * Get package physical difficulty level
     *
     * Maps ACF field value to display label and dots count (1-5)
     *
     * @param int $post_id Package post ID
     * @return array Activity level data with label and dots
     */
    private function get_package_physical_difficulty(int $post_id): array
    {
        $physical_difficulty = get_field('physical_difficulty', $post_id);

        $difficulty_map = [
            'easy' => ['label' => __('Easy', 'travel-blocks'), 'dots' => 1],
            'moderate' => ['label' => __('Moderate', 'travel-blocks'), 'dots' => 2],
            'moderate_demanding' => ['label' => __('Moderate - Demanding', 'travel-blocks'), 'dots' => 3],
            'difficult' => ['label' => __('Difficult', 'travel-blocks'), 'dots' => 4],
            'very_difficult' => ['label' => __('Very Difficult', 'travel-blocks'), 'dots' => 5],
        ];

        if (!empty($physical_difficulty) && isset($difficulty_map[$physical_difficulty])) {
            return $difficulty_map[$physical_difficulty];
        }

        return ['label' => '', 'dots' => 0];
    }

    /**
     * Enqueue hero media grid assets
     */
    public function enqueue_assets(): void
    {
        $css_path = TRAVEL_BLOCKS_PATH . 'assets/blocks/template/hero-media-grid.css';
        $js_path = TRAVEL_BLOCKS_PATH . 'assets/blocks/template/hero-media-grid.js';

        if (file_exists($css_path)) {
            wp_enqueue_style(
                'travel-blocks-hero-media-grid',
                TRAVEL_BLOCKS_URL . 'assets/blocks/template/hero-media-grid.css',
                [],
                TRAVEL_BLOCKS_VERSION
            );
        }

        if (file_exists($js_path)) {
            wp_enqueue_script(
                'travel-blocks-hero-media-grid',
                TRAVEL_BLOCKS_URL . 'assets/blocks/template/hero-media-grid.js',
                [],
                TRAVEL_BLOCKS_VERSION,
                true
            );
        }
    }
}
