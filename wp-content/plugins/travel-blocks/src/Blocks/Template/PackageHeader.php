<?php
/**
 * Package Header Template Block
 *
 * Displays package title, overview, and key metadata
 *
 * @package Travel\Blocks\Blocks\Template
 * @since 2.0.0
 */

namespace Travel\Blocks\Blocks\Template;

use Travel\Blocks\Core\TemplateBlockBase;
use Travel\Blocks\Core\PreviewDataTrait;

class PackageHeader extends TemplateBlockBase
{
    use PreviewDataTrait;

    public function __construct()
    {
        $this->name = 'package-header';
        $this->title = 'Package Header';
        $this->description = 'Package title, overview, and metadata';
        $this->icon = 'heading';
        $this->keywords = ['header', 'title', 'overview', 'metadata', 'package'];
    }

    protected function render_preview(array $attributes): string
    {
        $preview_data = $this->get_preview_package_data();

        $data = [
            'subtitle' => $preview_data['subtitle'] ?? '',
            'overview' => $preview_data['overview'],
            'metadata' => [
                'duration' => $preview_data['duration'],
                'departure' => $preview_data['departure'],
                'difficulty' => $preview_data['physical_difficulty'],
                'service_type' => $preview_data['service_type'],
            ],
            'is_preview' => true,
        ];

        return $this->load_template('package-header', $data);
    }

    protected function render_live(int $post_id, array $attributes): string
    {
        $data = [
            'subtitle' => get_field('subtitle', $post_id) ?? '',
            'overview' => get_field('description', $post_id) ?? '',
            'metadata' => $this->get_package_metadata($post_id),
            'is_preview' => false,
        ];

        return $this->load_template('package-header', $data);
    }

    /**
     * Get package metadata
     *
     * @param int $post_id Package post ID
     * @return array Metadata items
     */
    private function get_package_metadata(int $post_id): array
    {
        return [
            'duration' => get_field('duration', $post_id) ?? '',
            'departure' => get_field('departure', $post_id) ?? '',
            'difficulty' => get_field('physical_difficulty', $post_id) ?? '',
            'service_type' => get_field('service_type', $post_id) ?? '',
        ];
    }

    /**
     * Enqueue package header assets
     */
    public function enqueue_assets(): void
    {
        $css_path = TRAVEL_BLOCKS_PATH . 'assets/blocks/template/package-header.css';

        if (file_exists($css_path)) {
            wp_enqueue_style(
                'travel-blocks-package-header',
                TRAVEL_BLOCKS_URL . 'assets/blocks/template/package-header.css',
                [],
                TRAVEL_BLOCKS_VERSION
            );
        }
    }
}
