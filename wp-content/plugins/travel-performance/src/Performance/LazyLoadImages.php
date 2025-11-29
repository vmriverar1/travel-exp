<?php
/**
 * Lazy Load Images
 *
 * Implements native lazy loading for images to improve page load performance.
 *
 * @package Travel\Performance\Performance
 * @since 1.0.0
 */

namespace Travel\Performance\Performance;

class LazyLoadImages
{
    /**
     * Register lazy loading hooks.
     *
     * @return void
     */
    public function register(): void
    {
        // Add loading="lazy" to content images
        add_filter('the_content', [$this, 'add_lazy_loading_to_content'], 99);

        // Add loading="lazy" to post thumbnails
        add_filter('post_thumbnail_html', [$this, 'add_lazy_loading_to_thumbnail'], 99, 5);

        // Add loading="lazy" to ACF images
        add_filter('acf/format_value/type=image', [$this, 'add_lazy_loading_to_acf_image'], 99, 3);

        // Add loading="lazy" to gallery images
        add_filter('acf/format_value/type=gallery', [$this, 'add_lazy_loading_to_gallery'], 99, 3);
    }

    /**
     * Add lazy loading to images in post content.
     *
     * @param string $content Post content
     *
     * @return string Modified content
     */
    public function add_lazy_loading_to_content(string $content): string
    {
        if (is_feed() || is_preview()) {
            return $content;
        }

        // Add loading="lazy" to img tags that don't have it
        $content = preg_replace_callback(
            '/<img([^>]+)>/i',
            function ($matches) {
                $img_tag = $matches[0];

                // Skip if already has loading attribute
                if (strpos($img_tag, 'loading=') !== false) {
                    return $img_tag;
                }

                // Skip first image (above the fold)
                static $first_image = true;
                if ($first_image) {
                    $first_image = false;
                    return $img_tag;
                }

                // Add loading="lazy"
                return str_replace('<img', '<img loading="lazy"', $img_tag);
            },
            $content
        );

        return $content;
    }

    /**
     * Add lazy loading to post thumbnails.
     *
     * @param string $html              Thumbnail HTML
     * @param int    $post_id           Post ID
     * @param int    $thumbnail_id      Thumbnail ID
     * @param string $size              Image size
     * @param array  $attr              Image attributes
     *
     * @return string Modified HTML
     */
    public function add_lazy_loading_to_thumbnail(string $html, int $post_id, int $thumbnail_id, $size, array $attr): string
    {
        if (is_feed() || is_preview()) {
            return $html;
        }

        // Skip if already has loading attribute
        if (strpos($html, 'loading=') !== false) {
            return $html;
        }

        // Don't lazy load featured image on single posts (above the fold)
        if (is_singular() && in_the_loop()) {
            return $html;
        }

        // Add loading="lazy"
        return str_replace('<img', '<img loading="lazy"', $html);
    }

    /**
     * Add lazy loading to ACF image fields.
     *
     * @param mixed $value   Field value
     * @param mixed $post_id Post ID (can be int or string in Gutenberg blocks)
     * @param array $field   Field array
     *
     * @return mixed
     */
    public function add_lazy_loading_to_acf_image($value, $post_id, array $field)
    {
        // Convert post_id to int if it's a string (happens in Gutenberg blocks)
        $post_id = is_numeric($post_id) ? (int) $post_id : 0;

        if (empty($value)) {
            return $value;
        }

        // If it's an image array with 'url'
        if (is_array($value) && isset($value['url'])) {
            // Add loading attribute to sizes array
            if (isset($value['sizes'])) {
                foreach ($value['sizes'] as $key => &$size_value) {
                    if (is_string($size_value) && strpos($size_value, '<img') !== false) {
                        $size_value = str_replace('<img', '<img loading="lazy"', $size_value);
                    }
                }
            }
        }

        // If it's HTML string
        if (is_string($value) && strpos($value, '<img') !== false) {
            $value = str_replace('<img', '<img loading="lazy"', $value);
        }

        return $value;
    }

    /**
     * Add lazy loading to ACF gallery fields.
     *
     * @param mixed $value   Field value
     * @param int   $post_id Post ID
     * @param array $field   Field array
     *
     * @return mixed
     */
    public function add_lazy_loading_to_gallery($value, int $post_id, array $field)
    {
        if (empty($value) || !is_array($value)) {
            return $value;
        }

        foreach ($value as &$image) {
            if (is_array($image) && isset($image['sizes'])) {
                foreach ($image['sizes'] as $key => &$size_value) {
                    if (is_string($size_value) && strpos($size_value, '<img') !== false) {
                        $size_value = str_replace('<img', '<img loading="lazy"', $size_value);
                    }
                }
            }
        }

        return $value;
    }
}
