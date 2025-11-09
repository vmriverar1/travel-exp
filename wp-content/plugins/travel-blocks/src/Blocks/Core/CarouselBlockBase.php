<?php
/**
 * Abstract Base Class: Carousel Blocks
 *
 * Shared functionality for carousel-based blocks (HeroCarousel, FlexibleGridCarousel).
 * Eliminates ~70% code duplication by providing common fields and methods.
 *
 * @package Travel\Blocks\Core
 * @since 2.1.0
 * @version 1.0.0
 *
 * Shared Features:
 * - Carousel settings (arrows, dots, autoplay)
 * - Style settings (button/badge color variants)
 * - Grid columns configuration
 * - ContentQueryHelper integration
 * - Text alignment settings
 * - Demo data support
 */

namespace Travel\Blocks\Core;

use Travel\Blocks\Config\ButtonStyles;
use Travel\Blocks\Helpers\ContentQueryHelper;

abstract class CarouselBlockBase extends BlockBase
{
    /**
     * Get common carousel settings fields.
     *
     * Returns ACF fields for carousel configuration:
     * - Show arrows
     * - Show dots
     * - Enable autoplay
     * - Autoplay delay
     *
     * @param string $prefix Field key prefix (e.g., 'hc', 'fgc')
     * @return array ACF fields configuration
     */
    protected function get_carousel_settings_fields(string $prefix): array
    {
        return [
            // Tab: Carousel
            [
                'key' => "field_{$prefix}_tab_carousel",
                'label' => '🎬 Carousel',
                'type' => 'tab',
                'placement' => 'top',
            ],

            // Show Arrows
            [
                'key' => "field_{$prefix}_show_arrows",
                'label' => __('Show Navigation Arrows', 'travel-blocks'),
                'name' => 'show_arrows',
                'type' => 'true_false',
                'instructions' => __('Display prev/next arrows', 'travel-blocks'),
                'default_value' => 1,
                'ui' => 1,
            ],

            // Show Dots
            [
                'key' => "field_{$prefix}_show_dots",
                'label' => __('Show Pagination Dots', 'travel-blocks'),
                'name' => 'show_dots',
                'type' => 'true_false',
                'instructions' => __('Display pagination dots', 'travel-blocks'),
                'default_value' => 1,
                'ui' => 1,
            ],

            // Enable Autoplay
            [
                'key' => "field_{$prefix}_enable_autoplay",
                'label' => __('Enable Autoplay', 'travel-blocks'),
                'name' => 'enable_autoplay',
                'type' => 'true_false',
                'instructions' => __('Automatically advance slides', 'travel-blocks'),
                'default_value' => 0,
                'ui' => 1,
            ],

            // Autoplay Delay
            [
                'key' => "field_{$prefix}_autoplay_delay",
                'label' => __('Autoplay Delay (ms)', 'travel-blocks'),
                'name' => 'autoplay_delay',
                'type' => 'number',
                'instructions' => __('Delay between slides in milliseconds', 'travel-blocks'),
                'required' => 0,
                'conditional_logic' => [
                    [
                        [
                            'field' => "field_{$prefix}_enable_autoplay",
                            'operator' => '==',
                            'value' => '1',
                        ],
                    ],
                ],
                'default_value' => 5000,
                'min' => 1000,
                'max' => 30000,
                'step' => 1000,
            ],
        ];
    }

    /**
     * Get common style settings fields.
     *
     * ✅ REFACTORED v2.1.0: Now uses ButtonStyles config class.
     *
     * Returns ACF fields for styling:
     * - Button color variant
     * - Badge color variant
     * - Text alignment
     * - Button alignment
     *
     * @param string $prefix Field key prefix (e.g., 'hc', 'fgc')
     * @param bool $include_alignments Whether to include text/button alignment fields
     * @return array ACF fields configuration
     */
    protected function get_style_settings_fields(string $prefix, bool $include_alignments = true): array
    {
        $fields = [
            // Tab: Styles
            [
                'key' => "field_{$prefix}_tab_styles",
                'label' => '🎨 Card Styles',
                'type' => 'tab',
                'placement' => 'top',
            ],

            // ✅ Button Color Variant - using ButtonStyles config
            ButtonStyles::get_button_field(
                "field_{$prefix}_button_color_variant",
                'button_color_variant',
                'primary',
                true // include read-more variant
            ),

            // ✅ Badge Color Variant - using ButtonStyles config
            ButtonStyles::get_badge_field(
                "field_{$prefix}_badge_color_variant",
                'badge_color_variant',
                'secondary'
            ),
        ];

        // Add alignment fields if requested
        if ($include_alignments) {
            $fields[] = ButtonStyles::get_text_alignment_field(
                "field_{$prefix}_text_alignment",
                'text_alignment',
                'left'
            );
            $fields[] = ButtonStyles::get_button_alignment_field(
                "field_{$prefix}_button_alignment",
                'button_alignment',
                'left'
            );
        }

        return $fields;
    }

    /**
     * Get common grid/columns configuration fields.
     *
     * Returns ACF fields for grid layout:
     * - Columns desktop (2-4)
     *
     * @param string $prefix Field key prefix (e.g., 'hc', 'fgc')
     * @param int $min Minimum columns (default: 2)
     * @param int $max Maximum columns (default: 4)
     * @param int $default Default columns (default: 3)
     * @return array ACF fields configuration
     */
    protected function get_grid_columns_field(string $prefix, int $min = 2, int $max = 4, int $default = 3): array
    {
        $choices = [];
        for ($i = $min; $i <= $max; $i++) {
            $choices[(string)$i] = sprintf(__('%d Columns', 'travel-blocks'), $i);
        }

        return [
            [
                'key' => "field_{$prefix}_columns_desktop",
                'label' => __('Columns (Desktop)', 'travel-blocks'),
                'name' => 'columns_desktop',
                'type' => 'select',
                'instructions' => __('Number of cards to show at once (if cards exceed this, carousel activates)', 'travel-blocks'),
                'required' => 0,
                'choices' => $choices,
                'default_value' => (string)$default,
                'ui' => 1,
                'return_format' => 'value',
            ],
        ];
    }

    /**
     * Get dynamic content from ContentQueryHelper.
     *
     * Unified method for getting content from packages/posts/deals.
     *
     * @param string $prefix Field prefix for ACF fields (e.g., 'hc', 'fgc')
     * @param string $source Content source ('package', 'post', 'deal', 'none')
     * @return array Cards array or empty array
     */
    protected function get_dynamic_content(string $prefix, string $source): array
    {
        if ($source === 'package') {
            return ContentQueryHelper::get_content($prefix, 'package');
        }

        if ($source === 'post') {
            return ContentQueryHelper::get_content($prefix, 'post');
        }

        if ($source === 'deal') {
            $deal_id = get_field("{$prefix}_deal_selector");
            if ($deal_id) {
                return ContentQueryHelper::get_deal_packages($deal_id, $prefix);
            }
        }

        return [];
    }

    /**
     * Get common carousel data for template.
     *
     * Returns standard carousel configuration data.
     *
     * @param int $total_cards Total number of cards
     * @param int $columns_desktop Desktop columns configuration
     * @return array Carousel data
     */
    protected function get_carousel_data(int $total_cards, int $columns_desktop): array
    {
        return [
            'columns_desktop' => $columns_desktop,
            'show_arrows' => get_field('show_arrows'),
            'show_dots' => get_field('show_dots'),
            'enable_autoplay' => get_field('enable_autoplay'),
            'autoplay_delay' => get_field('autoplay_delay') ?: 5000,
            'is_carousel' => $total_cards > $columns_desktop,
        ];
    }

    /**
     * Get common style data for template.
     *
     * Returns standard styling configuration data.
     *
     * @param bool $include_alignments Whether to include text/button alignment
     * @return array Style data
     */
    protected function get_style_data(bool $include_alignments = true): array
    {
        $data = [
            'button_color_variant' => get_field('button_color_variant') ?: 'primary',
            'badge_color_variant' => get_field('badge_color_variant') ?: 'secondary',
        ];

        if ($include_alignments) {
            $data['text_alignment'] = get_field('text_alignment') ?: 'left';
            $data['button_alignment'] = get_field('button_alignment') ?: 'left';
        }

        return $data;
    }

    /**
     * Fill missing images with demo placeholders.
     *
     * Ensures all cards have images for preview purposes.
     *
     * @param array $cards Array of cards
     * @param int $start_id Starting ID for random images
     * @return array Cards with filled images
     */
    protected function fill_demo_images(array $cards, int $start_id = 310): array
    {
        foreach ($cards as $index => &$card) {
            if (empty($card['image'])) {
                $random_id = $start_id + $index + 1;
                $card['image'] = [
                    'url' => 'https://picsum.photos/800/600?random=' . $random_id,
                    'sizes' => [
                        'large' => 'https://picsum.photos/800/600?random=' . $random_id,
                        'medium' => 'https://picsum.photos/400/300?random=' . $random_id,
                    ],
                    'alt' => $card['title'] ?? 'Card Image',
                ];
            }
        }
        unset($card); // Break reference

        return $cards;
    }

    /**
     * Abstract method: Get block-specific fields.
     *
     * Each carousel block must implement this to return its unique ACF fields.
     *
     * @param string $prefix Field key prefix
     * @return array ACF fields configuration
     */
    abstract protected function get_block_specific_fields(string $prefix): array;
}
