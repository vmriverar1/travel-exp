<?php
/**
 * Package Map Block
 * Displays the route map image for a package
 */

namespace Travel\Blocks\Blocks\Package;

use Travel\Blocks\Helpers\EditorHelper;

class PackageMap
{
    private string $name = 'package-map';
    private string $title = 'Package Map';
    private string $description = 'Displays the route map image for a package';

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
            'icon' => 'location-alt',
            'keywords' => ['map', 'route', 'package'],
            'supports' => [
                'align' => false,
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
        if (!is_admin() && is_singular('package')) {
            wp_enqueue_style(
                'travel-blocks-package-map',
                TRAVEL_BLOCKS_URL . 'assets/blocks/package-map.css',
                [],
                TRAVEL_BLOCKS_VERSION
            );
        }
    }

    /**
     * Render the block on the frontend
     */
    public function render(array $attributes = [], string $content = ''): string
    {
        // Only show on single package pages
        if (!is_singular('package')) {
            return $this->render_preview();
        }

        $post_id = get_the_ID();
        $map_image = get_field('map_image', $post_id);

        // If no map image, don't render anything
        if (!$map_image) {
            return '';
        }

        // Get image data
        $image_url = is_array($map_image) ? $map_image['url'] : wp_get_attachment_image_url($map_image, 'large');
        $image_alt = is_array($map_image) ? $map_image['alt'] : get_post_meta($map_image, '_wp_attachment_image_alt', true);

        if (!$image_url) {
            return '';
        }

        // Default alt text
        if (empty($image_alt)) {
            $image_alt = sprintf(__('Route map for %s', 'travel-blocks'), get_the_title($post_id));
        }

        ob_start();
        ?>
        <div class="package-map-wrapper">
            <figure class="package-map-figure">
                <a href="<?php echo esc_url($image_url); ?>" class="glightbox package-map-link" data-gallery="package-map">
                    <img
                        src="<?php echo esc_url($image_url); ?>"
                        alt="<?php echo esc_attr($image_alt); ?>"
                        class="package-map-image"
                        loading="lazy"
                    />
                </a>
            </figure>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Render preview in the editor
     */
    protected function render_preview(): string
    {
        ob_start();
        ?>
        <div class="package-map-wrapper package-map-preview">
            <figure class="package-map-figure">
                <img
                    src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='400' height='300'%3E%3Crect fill='%23E8F5E9' width='400' height='300'/%3E%3Ctext x='50%25' y='50%25' text-anchor='middle' fill='%234CAF50' font-size='16' dy='.3em'%3E📍 Route Map%3C/text%3E%3C/svg%3E"
                    alt="Route map preview"
                    class="package-map-image"
                />
                <figcaption class="package-map-caption">
                    <?php _e('Package Map - Select a map image in the Media & Gallery step of the wizard', 'travel-blocks'); ?>
                </figcaption>
            </figure>
        </div>
        <?php
        return ob_get_clean();
    }
}
