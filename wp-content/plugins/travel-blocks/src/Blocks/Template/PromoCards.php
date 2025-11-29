<?php
/**
 * Promo Cards Block (ACF)
 *
 * Two image cards with editable heights
 *
 * @package Travel\Blocks\Blocks\Template
 * @since 2.0.0
 */

namespace Travel\Blocks\Blocks\Template;

use Travel\Blocks\Core\BlockBase;

class PromoCards extends BlockBase
{
    public function __construct()
    {
        $this->name = 'promo-cards';
        $this->title = 'Promo Cards';
        $this->description = 'Two image cards with editable heights';
        $this->category = 'template-blocks';
        $this->icon = 'slides';
        $this->keywords = ['promo', 'cards', 'images'];
        $this->mode = 'preview';

        $this->supports = [
            'align' => false,
            'mode' => false,
            'multiple' => true,
            'anchor' => false,
        ];
    }

    /**
     * Register block and ACF fields
     */
    public function register(): void
    {
        parent::register();

        // Register ACF fields
        if (function_exists('acf_add_local_field_group')) {
            acf_add_local_field_group([
                'key' => 'group_promo_cards',
                'title' => __('Promo Cards Settings', 'travel-blocks'),
                'fields' => [
                    // Card 1
                    [
                        'key' => 'field_promo_card_1_image',
                        'label' => __('Card 1 - Image', 'travel-blocks'),
                        'name' => 'card_1_image',
                        'type' => 'image',
                        'instructions' => __('Select image for left card', 'travel-blocks'),
                        'required' => 0,
                        'return_format' => 'array',
                        'preview_size' => 'medium',
                        'library' => 'all',
                        'wrapper' => ['width' => '50'],
                    ],
                    [
                        'key' => 'field_promo_card_1_height',
                        'label' => __('Card 1 - Height (px)', 'travel-blocks'),
                        'name' => 'card_1_height',
                        'type' => 'number',
                        'instructions' => __('Height in pixels (default: 400)', 'travel-blocks'),
                        'default_value' => 400,
                        'min' => 200,
                        'max' => 800,
                        'step' => 10,
                        'wrapper' => ['width' => '50'],
                    ],

                    // Card 2
                    [
                        'key' => 'field_promo_card_2_image',
                        'label' => __('Card 2 - Image', 'travel-blocks'),
                        'name' => 'card_2_image',
                        'type' => 'image',
                        'instructions' => __('Select image for right card', 'travel-blocks'),
                        'required' => 0,
                        'return_format' => 'array',
                        'preview_size' => 'medium',
                        'library' => 'all',
                        'wrapper' => ['width' => '50'],
                    ],
                    [
                        'key' => 'field_promo_card_2_height',
                        'label' => __('Card 2 - Height (px)', 'travel-blocks'),
                        'name' => 'card_2_height',
                        'type' => 'number',
                        'instructions' => __('Height in pixels (default: 400)', 'travel-blocks'),
                        'default_value' => 400,
                        'min' => 200,
                        'max' => 800,
                        'step' => 10,
                        'wrapper' => ['width' => '50'],
                    ],
                    [
                        'key' => 'field_promo_card_2_link',
                        'label' => __('Card 2 - Link URL', 'travel-blocks'),
                        'name' => 'card_2_link',
                        'type' => 'url',
                        'instructions' => __('Optional: Add a link to redirect when clicking the second card', 'travel-blocks'),
                        'required' => 0,
                        'placeholder' => 'https://example.com',
                    ],
                ],
                'location' => [
                    [
                        [
                            'param' => 'block',
                            'operator' => '==',
                            'value' => 'acf/' . $this->name,
                        ],
                    ],
                ],
            ]);
        }
    }

    /**
     * Enqueue block assets
     */
    public function enqueue_assets(): void
    {
        $css_path = TRAVEL_BLOCKS_PATH . 'assets/blocks/template/promo-cards.css';

        if (file_exists($css_path)) {
            wp_enqueue_style(
                'travel-blocks-promo-cards',
                TRAVEL_BLOCKS_URL . 'assets/blocks/template/promo-cards.css',
                [],
                TRAVEL_BLOCKS_VERSION
            );
        }
    }

    /**
     * Render block callback
     */
    public function render(array $block, string $content = '', bool $is_preview = false, int $post_id = 0): void
    {
        // Get ACF field values
        $card_1_image = get_field('card_1_image');
        $card_1_height = get_field('card_1_height') ?: 400;

        $card_2_image = get_field('card_2_image');
        $card_2_height = get_field('card_2_height') ?: 400;
        $card_2_link = get_field('card_2_link') ?: '';

        // Get current post/page ID and check if it's a package
        $current_post_id = get_the_ID();
        $current_post_type = get_post_type($current_post_id);

        // Enable PDF download only if we're on a package single page
        $enable_pdf = ($current_post_type === 'package');
        $package_id = $enable_pdf ? $current_post_id : null;

        // Debug log
        error_log('PromoCards Debug: post_type=' . $current_post_type . ', enable_pdf=' . var_export($enable_pdf, true) . ', package_id=' . var_export($package_id, true));

        // Default preview images
        $default_img_1 = 'https://images.unsplash.com/photo-1526392060635-9d6019884377?w=800&h=600&fit=crop';
        $default_img_2 = 'https://images.unsplash.com/photo-1488646953014-85cb44e25828?w=800&h=600&fit=crop';

        $cards = [
            [
                'image' => is_array($card_1_image) ? $card_1_image['url'] : $default_img_1,
                'alt' => is_array($card_1_image) ? $card_1_image['alt'] : 'Promo Card 1',
                'height' => $card_1_height,
                'enable_pdf' => $enable_pdf,
                'package_id' => $package_id,
            ],
            [
                'image' => is_array($card_2_image) ? $card_2_image['url'] : $default_img_2,
                'alt' => is_array($card_2_image) ? $card_2_image['alt'] : 'Promo Card 2',
                'height' => $card_2_height,
                'enable_pdf' => false,
                'package_id' => null,
                'link' => $card_2_link,
            ],
        ];

        // Render template
        ob_start();
        include TRAVEL_BLOCKS_PATH . 'templates/template/promo-cards.php';
        echo ob_get_clean();
    }
}
