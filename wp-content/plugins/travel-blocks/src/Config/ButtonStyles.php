<?php
/**
 * Button and Badge Styles Configuration
 *
 * Centralized configuration for button and badge color variants used across blocks.
 * Eliminates duplication of style choices in multiple ACF field definitions.
 *
 * @package Travel\Blocks\Config
 * @since 2.1.0
 * @version 1.0.0
 */

namespace Travel\Blocks\Config;

class ButtonStyles
{
    /**
     * Get button color variant field configuration.
     *
     * Returns a complete ACF select field for button color variants.
     *
     * @param string $key Field key (e.g., 'field_button_color_variant')
     * @param string $name Field name (default: 'button_color_variant')
     * @param string $default Default value (default: 'primary')
     * @param bool $include_read_more Whether to include "Read More" variant (default: true)
     * @return array ACF field configuration
     */
    public static function get_button_field(
        string $key,
        string $name = 'button_color_variant',
        string $default = 'primary',
        bool $include_read_more = true
    ): array {
        $choices = self::get_button_choices($include_read_more);

        return [
            'key' => $key,
            'label' => __('🎨 Button Color', 'travel-blocks'),
            'name' => $name,
            'type' => 'select',
            'required' => 0,
            'choices' => $choices,
            'default_value' => $default,
            'ui' => 1,
            'instructions' => __('Color applied to all card buttons', 'travel-blocks'),
        ];
    }

    /**
     * Get badge color variant field configuration.
     *
     * Returns a complete ACF select field for badge color variants.
     *
     * @param string $key Field key (e.g., 'field_badge_color_variant')
     * @param string $name Field name (default: 'badge_color_variant')
     * @param string $default Default value (default: 'secondary')
     * @return array ACF field configuration
     */
    public static function get_badge_field(
        string $key,
        string $name = 'badge_color_variant',
        string $default = 'secondary'
    ): array {
        return [
            'key' => $key,
            'label' => __('🎨 Badge Color', 'travel-blocks'),
            'name' => $name,
            'type' => 'select',
            'required' => 0,
            'choices' => self::get_badge_choices(),
            'default_value' => $default,
            'ui' => 1,
            'instructions' => __('Color applied to all badges', 'travel-blocks'),
        ];
    }

    /**
     * Get button color choices.
     *
     * Returns array of color options for buttons.
     *
     * @param bool $include_read_more Whether to include "Read More" text-only variant
     * @return array Associative array of color choices
     */
    public static function get_button_choices(bool $include_read_more = true): array
    {
        $choices = [
            'primary' => __('Primary - Pink (#E78C85)', 'travel-blocks'),
            'secondary' => __('Secondary - Purple (#311A42)', 'travel-blocks'),
            'white' => __('White with black text', 'travel-blocks'),
            'gold' => __('Gold (#CEA02D)', 'travel-blocks'),
            'dark' => __('Dark (#1A1A1A)', 'travel-blocks'),
            'transparent' => __('Transparent with white border', 'travel-blocks'),
        ];

        if ($include_read_more) {
            $choices['read-more'] = __('Text "Read More" (no background)', 'travel-blocks');
        }

        return $choices;
    }

    /**
     * Get badge color choices.
     *
     * Returns array of color options for badges.
     *
     * @return array Associative array of color choices
     */
    public static function get_badge_choices(): array
    {
        return [
            'primary' => __('Primary - Pink (#E78C85)', 'travel-blocks'),
            'secondary' => __('Secondary - Purple (#311A42)', 'travel-blocks'),
            'white' => __('White with black text', 'travel-blocks'),
            'gold' => __('Gold (#CEA02D)', 'travel-blocks'),
            'dark' => __('Dark (#1A1A1A)', 'travel-blocks'),
            'transparent' => __('Transparent with white border', 'travel-blocks'),
        ];
    }

    /**
     * Get text alignment field configuration.
     *
     * Returns a complete ACF select field for text alignment.
     *
     * @param string $key Field key (e.g., 'field_text_alignment')
     * @param string $name Field name (default: 'text_alignment')
     * @param string $default Default value (default: 'left')
     * @return array ACF field configuration
     */
    public static function get_text_alignment_field(
        string $key,
        string $name = 'text_alignment',
        string $default = 'left'
    ): array {
        return [
            'key' => $key,
            'label' => __('📐 Text Alignment', 'travel-blocks'),
            'name' => $name,
            'type' => 'select',
            'required' => 0,
            'choices' => [
                'left' => __('Left', 'travel-blocks'),
                'center' => __('Center', 'travel-blocks'),
                'right' => __('Right', 'travel-blocks'),
            ],
            'default_value' => $default,
            'ui' => 1,
            'instructions' => __('Text alignment (title, description, location, price)', 'travel-blocks'),
        ];
    }

    /**
     * Get button alignment field configuration.
     *
     * Returns a complete ACF select field for button alignment.
     *
     * @param string $key Field key (e.g., 'field_button_alignment')
     * @param string $name Field name (default: 'button_alignment')
     * @param string $default Default value (default: 'left')
     * @return array ACF field configuration
     */
    public static function get_button_alignment_field(
        string $key,
        string $name = 'button_alignment',
        string $default = 'left'
    ): array {
        return [
            'key' => $key,
            'label' => __('📍 Button Alignment', 'travel-blocks'),
            'name' => $name,
            'type' => 'select',
            'required' => 0,
            'choices' => [
                'left' => __('Left', 'travel-blocks'),
                'center' => __('Center', 'travel-blocks'),
                'right' => __('Right', 'travel-blocks'),
            ],
            'default_value' => $default,
            'ui' => 1,
            'instructions' => __('Button/CTA alignment', 'travel-blocks'),
        ];
    }
}
