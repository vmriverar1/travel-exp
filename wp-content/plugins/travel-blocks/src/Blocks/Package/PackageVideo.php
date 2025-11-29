<?php
/**
 * Block: Package Video
 *
 * Renders YouTube video from package video_url field
 * Converts YouTube URLs to embeddable format
 *
 * @package Travel\Blocks\Blocks\Package
 * @since 1.0.0
 */

namespace Travel\Blocks\Blocks\Package;

use Travel\Blocks\Helpers\EditorHelper;

class PackageVideo
{
    private string $name = 'package-video';
    private string $title = 'Package Video';
    private string $description = 'Video de YouTube del paquete';

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
            'icon' => 'video-alt2',
            'keywords' => ['video', 'youtube', 'package'],
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
                'package-video-style',
                TRAVEL_BLOCKS_URL . 'assets/blocks/package-video.css',
                [],
                TRAVEL_BLOCKS_VERSION
            );
        }
    }

    /**
     * Render the block
     */
    public function render($attributes, $content, $block): string
    {
        // Only render on package singles
        if (!is_singular('package')) {
            return '';
        }

        $post_id = get_the_ID();
        $video_url = get_field('video_url', $post_id);

        // If no video URL, don't render anything
        if (empty($video_url)) {
            return '';
        }

        // Convert YouTube URL to embed format
        $embed_url = $this->get_youtube_embed_url($video_url);

        if (empty($embed_url)) {
            return '';
        }

        // Start output buffering
        ob_start();
        ?>
        <div class="package-video-wrapper">
            <div class="package-video-container">
                <iframe
                    src="<?php echo esc_url($embed_url); ?>"
                    title="<?php echo esc_attr(get_the_title()); ?> - Video"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                    referrerpolicy="strict-origin-when-cross-origin"
                    allowfullscreen
                    loading="lazy"
                ></iframe>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Convert YouTube URL to embed format
     *
     * Accepts:
     * - https://www.youtube.com/watch?v=VIDEO_ID
     * - https://youtu.be/VIDEO_ID
     * - https://www.youtube.com/embed/VIDEO_ID
     *
     * Returns: https://www.youtube-nocookie.com/embed/VIDEO_ID
     */
    private function get_youtube_embed_url(string $url): string
    {
        // Validate URL
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return '';
        }

        // Parse URL
        $parsed = parse_url($url);

        if (!$parsed || !isset($parsed['host'])) {
            return '';
        }

        $host = $parsed['host'];
        $video_id = '';

        // youtube.com/watch?v=VIDEO_ID
        if (preg_match('/youtube\.com/', $host)) {
            if (isset($parsed['query'])) {
                parse_str($parsed['query'], $query);
                $video_id = $query['v'] ?? '';
            }
        }
        // youtu.be/VIDEO_ID
        elseif (preg_match('/youtu\.be/', $host)) {
            $video_id = trim($parsed['path'], '/');
        }

        // Validate video ID (YouTube IDs are 11 characters)
        if (empty($video_id) || !preg_match('/^[a-zA-Z0-9_-]{11}$/', $video_id)) {
            return '';
        }

        // Return privacy-enhanced YouTube embed URL
        return 'https://www.youtube-nocookie.com/embed/' . $video_id;
    }
}
