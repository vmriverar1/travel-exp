<?php
/**
 * Breadcrumb Template Block
 *
 * Displays hierarchical breadcrumb navigation for packages
 *
 * @package Travel\Blocks\Blocks\Template
 * @since 2.0.0
 */

namespace Travel\Blocks\Blocks\Template;

use Travel\Blocks\Core\TemplateBlockBase;
use Travel\Blocks\Core\PreviewDataTrait;

class Breadcrumb extends TemplateBlockBase
{
    use PreviewDataTrait;

    public function __construct()
    {
        $this->name = 'breadcrumb';
        $this->title = 'Breadcrumb Navigation';
        $this->description = 'Hierarchical breadcrumb navigation for packages';
        $this->icon = 'arrow-right-alt';
        $this->keywords = ['breadcrumb', 'navigation', 'hierarchy', 'path'];
    }

    protected function render_preview(array $attributes): string
    {
        $data = [
            'breadcrumbs' => $this->get_preview_breadcrumbs(),
            'is_preview' => true,
        ];

        return $this->load_template('breadcrumb', $data);
    }

    protected function render_live(int $post_id, array $attributes): string
    {
        $data = [
            'breadcrumbs' => $this->get_package_breadcrumbs($post_id),
            'is_preview' => false,
        ];

        return $this->load_template('breadcrumb', $data);
    }

    /**
     * Get preview breadcrumbs
     *
     * @return array Sample breadcrumb items
     */
    private function get_preview_breadcrumbs(): array
    {
        return [
            ['title' => 'Home', 'url' => home_url('/')],
            ['title' => 'Tours', 'url' => home_url('/tours')],
            ['title' => 'Peru', 'url' => home_url('/tours/peru')],
            ['title' => 'Cusco', 'url' => home_url('/tours/peru/cusco')],
            ['title' => '4-Day Inca Trail Trek to Machu Picchu', 'url' => ''],
        ];
    }

    /**
     * Get real breadcrumbs for package
     *
     * @param int $post_id Package post ID
     * @return array Breadcrumb items
     */
    private function get_package_breadcrumbs(int $post_id): array
    {
        $breadcrumbs = [];

        // Home
        $breadcrumbs[] = [
            'title' => __('Home', 'travel-blocks'),
            'url' => home_url('/'),
        ];

        // Tours archive
        $post_type_object = get_post_type_object('package');
        if ($post_type_object && $post_type_object->has_archive) {
            $breadcrumbs[] = [
                'title' => $post_type_object->labels->name ?? __('Tours', 'travel-blocks'),
                'url' => get_post_type_archive_link('package'),
            ];
        }

        // Get primary destination/location taxonomy
        $destinations = wp_get_post_terms($post_id, 'destination', ['orderby' => 'term_id', 'order' => 'ASC']);
        if (!empty($destinations) && !is_wp_error($destinations)) {
            $primary_destination = $destinations[0];

            // Get parent destinations if hierarchical
            if ($primary_destination->parent) {
                $ancestors = get_ancestors($primary_destination->term_id, 'destination', 'taxonomy');
                $ancestors = array_reverse($ancestors);

                foreach ($ancestors as $ancestor_id) {
                    $ancestor = get_term($ancestor_id, 'destination');
                    if ($ancestor && !is_wp_error($ancestor)) {
                        $breadcrumbs[] = [
                            'title' => $ancestor->name,
                            'url' => get_term_link($ancestor),
                        ];
                    }
                }
            }

            // Add primary destination
            $breadcrumbs[] = [
                'title' => $primary_destination->name,
                'url' => get_term_link($primary_destination),
            ];
        }

        // Current package (no link)
        $breadcrumbs[] = [
            'title' => get_the_title($post_id),
            'url' => '',
        ];

        return $breadcrumbs;
    }

    /**
     * Enqueue breadcrumb styles
     */
    public function enqueue_assets(): void
    {
        $css_path = TRAVEL_BLOCKS_PATH . 'assets/blocks/template/breadcrumb.css';

        if (file_exists($css_path)) {
            wp_enqueue_style(
                'travel-blocks-breadcrumb',
                TRAVEL_BLOCKS_URL . 'assets/blocks/template/breadcrumb.css',
                [],
                TRAVEL_BLOCKS_VERSION
            );
        }
    }
}
