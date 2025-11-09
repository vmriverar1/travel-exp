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
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'discount_badge' => [
                'show' => true,
                'percentage' => 15,
                'text' => 'Early Bird Discount',
            ],
            'is_preview' => true,
        ];

        return $this->load_template('hero-media-grid', $data);
    }

    protected function render_live(int $post_id, array $attributes): string
    {
        $data = [
            'gallery' => $this->get_package_gallery($post_id),
            'map_image' => $this->get_package_map_image($post_id),
            'video_url' => $this->get_package_video_url($post_id),
            'discount_badge' => $this->get_package_discount($post_id),
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
