<?php
/**
 * Deals Slider Block
 *
 * Displays active deals with countdown timer and related packages in a slider
 *
 * @package Travel\Blocks\Blocks\Deal
 * @since 1.4.0
 */

namespace Travel\Blocks\Blocks\Deal;

use Travel\Blocks\Core\BlockBase;

class DealsSlider extends BlockBase
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct(
            'deals-slider',
            __('Deals Slider', 'travel-blocks'),
            __('Slider con ofertas vigentes, contador regresivo y packages relacionados', 'travel-blocks'),
            ['travel'],
            [
                'align' => ['wide', 'full'],
                'mode'  => true,
                'multiple' => true,
                'anchor' => true,
            ]
        );

        $this->icon        = 'tickets-alt';
        $this->keywords    = ['deals', 'slider', 'countdown', 'offers', 'packages'];
        $this->mode        = 'preview';
    }

    /**
     * Enqueue block-specific assets
     */
    public function enqueue_assets(): void
    {
        // CSS
        wp_enqueue_style(
            'deals-slider-style',
            TRAVEL_BLOCKS_URL . 'assets/blocks/deals-slider.css',
            [],
            TRAVEL_BLOCKS_VERSION
        );

        // Swiper CSS (if not already enqueued)
        if (!wp_style_is('swiper', 'enqueued')) {
            wp_enqueue_style(
                'swiper',
                'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css',
                [],
                '11.0.0'
            );
        }

        // Swiper JS (if not already enqueued)
        if (!wp_script_is('swiper', 'enqueued')) {
            wp_enqueue_script(
                'swiper',
                'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js',
                [],
                '11.0.0',
                true
            );
        }

        // Block JS
        wp_enqueue_script(
            'deals-slider-script',
            TRAVEL_BLOCKS_URL . 'assets/blocks/deals-slider.js',
            ['swiper'],
            TRAVEL_BLOCKS_VERSION,
            true
        );
    }

    /**
     * Register block and ACF fields
     */
    public function register(): void
    {
        parent::register();

        // Register ACF fields for this block
        if (function_exists('acf_add_local_field_group')) {
            acf_add_local_field_group([
                'key' => 'group_block_deals_slider',
                'title' => __('Deals Slider Settings', 'travel-blocks'),
                'fields' => [
            // ===== TAB: GENERAL =====
            [
                'key' => 'field_ds_tab_general',
                'label' => __('⚙️ General', 'travel-blocks'),
                'type' => 'tab',
                'placement' => 'top',
            ],

            // Deal Source
            [
                'key' => 'field_ds_deal_source',
                'label' => __('🎯 Fuente del Deal', 'travel-blocks'),
                'name' => 'deal_source',
                'type' => 'select',
                'instructions' => __('Automático usa el deal activo más próximo a expirar', 'travel-blocks'),
                'choices' => [
                    'auto' => __('Automático - Deal activo próximo a expirar', 'travel-blocks'),
                    'manual' => __('Manual - Seleccionar deal específico', 'travel-blocks'),
                ],
                'default_value' => 'auto',
                'ui' => 1,
            ],

            // Manual Deal Selector
            [
                'key' => 'field_ds_deal_manual',
                'label' => __('📌 Seleccionar Deal', 'travel-blocks'),
                'name' => 'deal_manual',
                'type' => 'post_object',
                'instructions' => __('Selecciona un deal específico', 'travel-blocks'),
                'post_type' => ['deal'],
                'return_format' => 'id',
                'ui' => 1,
                'conditional_logic' => [
                    [
                        [
                            'field' => 'field_ds_deal_source',
                            'operator' => '==',
                            'value' => 'manual',
                        ],
                    ],
                ],
            ],

            // Show Countdown
            [
                'key' => 'field_ds_show_countdown',
                'label' => __('⏱️ Mostrar Contador', 'travel-blocks'),
                'name' => 'show_countdown',
                'type' => 'true_false',
                'instructions' => __('Mostrar contador regresivo en la barra superior', 'travel-blocks'),
                'default_value' => 1,
                'ui' => 1,
            ],

            // Show Ribbon
            [
                'key' => 'field_ds_show_ribbon',
                'label' => __('🏷️ Mostrar Cinta', 'travel-blocks'),
                'name' => 'show_ribbon',
                'type' => 'true_false',
                'instructions' => __('Mostrar cinta "TOP SELLER" en la imagen', 'travel-blocks'),
                'default_value' => 1,
                'ui' => 1,
            ],

            // ===== TAB: BACKGROUND =====
            [
                'key' => 'field_ds_tab_background',
                'label' => __('🖼️ Fondo', 'travel-blocks'),
                'type' => 'tab',
                'placement' => 'top',
            ],

            // Background Image Desktop
            [
                'key' => 'field_ds_bg_desktop',
                'label' => __('Imagen de Fondo (Desktop)', 'travel-blocks'),
                'name' => 'background_image_desktop',
                'type' => 'image',
                'instructions' => __('Recomendado: 1920x800px. Textura/patrón para el fondo del slider', 'travel-blocks'),
                'return_format' => 'array',
                'preview_size' => 'large',
                'library' => 'all',
            ],

            // Background Image Mobile
            [
                'key' => 'field_ds_bg_mobile',
                'label' => __('Imagen de Fondo (Móvil)', 'travel-blocks'),
                'name' => 'background_image_mobile',
                'type' => 'image',
                'instructions' => __('Opcional: Imagen específica para móvil. Si está vacío, usa la de desktop', 'travel-blocks'),
                'return_format' => 'array',
                'preview_size' => 'medium',
                'library' => 'all',
            ],

            // Background Position
            [
                'key' => 'field_ds_bg_position',
                'label' => __('Posición del Fondo', 'travel-blocks'),
                'name' => 'background_position',
                'type' => 'select',
                'instructions' => __('Posición focal de la imagen de fondo', 'travel-blocks'),
                'choices' => [
                    'center center' => __('Centro', 'travel-blocks'),
                    'top center' => __('Arriba Centro', 'travel-blocks'),
                    'bottom center' => __('Abajo Centro', 'travel-blocks'),
                    'center left' => __('Centro Izquierda', 'travel-blocks'),
                    'center right' => __('Centro Derecha', 'travel-blocks'),
                ],
                'default_value' => 'center center',
                'ui' => 1,
            ],

            // ===== TAB: TEXTS =====
            [
                'key' => 'field_ds_tab_texts',
                'label' => __('✏️ Textos', 'travel-blocks'),
                'type' => 'tab',
                'placement' => 'top',
            ],

            // Countdown Text Line 1
            [
                'key' => 'field_ds_countdown_text_1',
                'label' => __('Texto Contador - Línea 1', 'travel-blocks'),
                'name' => 'countdown_text_1',
                'type' => 'text',
                'default_value' => 'Limited Time Offer',
                'maxlength' => 50,
                'wrapper' => ['width' => 50],
            ],

            // Countdown Text Line 2
            [
                'key' => 'field_ds_countdown_text_2',
                'label' => __('Texto Contador - Línea 2', 'travel-blocks'),
                'name' => 'countdown_text_2',
                'type' => 'text',
                'default_value' => 'Book Now And Save!',
                'maxlength' => 50,
                'wrapper' => ['width' => 50],
            ],

            // View Button Text
            [
                'key' => 'field_ds_view_button_text',
                'label' => __('Texto Botón "Ver"', 'travel-blocks'),
                'name' => 'view_button_text',
                'type' => 'text',
                'default_value' => 'View Trip',
                'maxlength' => 30,
                'wrapper' => ['width' => 50],
            ],

            // Book Button Text
            [
                'key' => 'field_ds_book_button_text',
                'label' => __('Texto Botón "Reservar"', 'travel-blocks'),
                'name' => 'book_button_text',
                'type' => 'text',
                'default_value' => 'Book Now',
                'maxlength' => 30,
                'wrapper' => ['width' => 50],
            ],

            // ===== TAB: SLIDER SETTINGS =====
            [
                'key' => 'field_ds_tab_slider',
                'label' => __('🎠 Slider', 'travel-blocks'),
                'type' => 'tab',
                'placement' => 'top',
            ],

            // Autoplay
            [
                'key' => 'field_ds_autoplay',
                'label' => __('⏯️ Autoplay', 'travel-blocks'),
                'name' => 'slider_autoplay',
                'type' => 'true_false',
                'instructions' => __('Avance automático del slider (pausa en hover)', 'travel-blocks'),
                'default_value' => 1,
                'ui' => 1,
                'wrapper' => ['width' => 33],
            ],

            // Autoplay Delay
            [
                'key' => 'field_ds_autoplay_delay',
                'label' => __('Delay del Autoplay (ms)', 'travel-blocks'),
                'name' => 'slider_delay',
                'type' => 'number',
                'instructions' => __('Tiempo entre slides en milisegundos', 'travel-blocks'),
                'default_value' => 6000,
                'min' => 2000,
                'max' => 15000,
                'step' => 500,
                'wrapper' => ['width' => 33],
                'conditional_logic' => [
                    [
                        [
                            'field' => 'field_ds_autoplay',
                            'operator' => '==',
                            'value' => '1',
                        ],
                    ],
                ],
            ],

            // Loop
            [
                'key' => 'field_ds_loop',
                'label' => __('🔁 Loop Infinito', 'travel-blocks'),
                'name' => 'slider_loop',
                'type' => 'true_false',
                'instructions' => __('Volver al inicio al llegar al final', 'travel-blocks'),
                'default_value' => 1,
                'ui' => 1,
                'wrapper' => ['width' => 34],
            ],

            // Show Arrows
            [
                'key' => 'field_ds_show_arrows',
                'label' => __('← → Mostrar Flechas', 'travel-blocks'),
                'name' => 'show_arrows',
                'type' => 'true_false',
                'instructions' => __('Mostrar flechas de navegación', 'travel-blocks'),
                'default_value' => 1,
                'ui' => 1,
                'wrapper' => ['width' => 50],
            ],

            // Show Dots
            [
                'key' => 'field_ds_show_dots',
                'label' => __('⚫ Mostrar Dots', 'travel-blocks'),
                'name' => 'show_dots',
                'type' => 'true_false',
                'instructions' => __('Mostrar dots de paginación', 'travel-blocks'),
                'default_value' => 1,
                'ui' => 1,
                'wrapper' => ['width' => 50],
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/deals-slider',
                ],
            ],
        ],
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
    ]);
        }
    }

    /**
     * Render block content
     */
    public function render(array $block, string $content = '', bool $is_preview = false, int $post_id = 0): void
    {
        // Get settings
        $deal_source = get_field('deal_source') ?: 'auto';
        $deal_id = null;

        // Get deal based on source
        if ($deal_source === 'manual') {
            $deal_id = get_field('deal_manual');
        } else {
            // Auto: Get active deal closest to expiration
            $deal_id = $this->get_active_deal();
        }

        // If no deal found, show message or hide
        if (!$deal_id) {
            if ($is_preview) {
                echo '<div style="padding: 2rem; text-align: center; background: #f0f0f0; border: 2px dashed #ccc;">';
                echo '<p style="margin: 0; color: #666;">⚠️ No hay deals activos disponibles</p>';
                echo '</div>';
            }
            return;
        }

        // Get deal data
        $deal_data = $this->get_deal_data($deal_id);

        // Get packages from deal
        $packages = $this->get_deal_packages($deal_id);

        // If no packages, don't render
        if (empty($packages)) {
            if ($is_preview) {
                echo '<div style="padding: 2rem; text-align: center; background: #fff3cd; border: 2px dashed #ffc107;">';
                echo '<p style="margin: 0; color: #856404;">⚠️ El deal no tiene packages asociados</p>';
                echo '</div>';
            }
            return;
        }

        // Get block settings
        $settings = [
            'show_countdown' => get_field('show_countdown') ?? true,
            'show_ribbon' => get_field('show_ribbon') ?? true,
            'background_image_desktop' => get_field('background_image_desktop'),
            'background_image_mobile' => get_field('background_image_mobile'),
            'background_position' => get_field('background_position') ?: 'center center',
            'countdown_text_1' => get_field('countdown_text_1') ?: 'Limited Time Offer',
            'countdown_text_2' => get_field('countdown_text_2') ?: 'Book Now And Save!',
            'view_button_text' => get_field('view_button_text') ?: 'View Trip',
            'book_button_text' => get_field('book_button_text') ?: 'Book Now',
            'slider_autoplay' => get_field('slider_autoplay') ?? true,
            'slider_delay' => get_field('slider_delay') ?: 6000,
            'slider_loop' => get_field('slider_loop') ?? true,
            'show_arrows' => get_field('show_arrows') ?? true,
            'show_dots' => get_field('show_dots') ?? true,
        ];

        // Block attributes
        $block_id = 'deals-slider-' . ($block['id'] ?? uniqid());
        $align = $block['align'] ?? 'full';

        // Pass data to template
        $data = [
            'block_id' => $block_id,
            'align' => $align,
            'deal_data' => $deal_data,
            'packages' => $packages,
            'settings' => $settings,
            'is_preview' => $is_preview,
        ];

        $this->load_template('deals-slider', $data);
    }

    /**
     * Get active deal closest to expiration
     */
    private function get_active_deal(): ?int
    {
        $args = [
            'post_type' => 'deal',
            'posts_per_page' => 1,
            'post_status' => 'publish',
            'meta_query' => [
                'relation' => 'AND',
                [
                    'key' => 'active',
                    'value' => '1',
                    'compare' => '=',
                ],
                [
                    'key' => 'end_date',
                    'value' => current_time('Y-m-d H:i:s'),
                    'compare' => '>=',
                    'type' => 'DATETIME',
                ],
            ],
            'orderby' => 'meta_value',
            'meta_key' => 'end_date',
            'order' => 'ASC',
        ];

        $query = new \WP_Query($args);

        if ($query->have_posts()) {
            return $query->posts[0]->ID;
        }

        return null;
    }

    /**
     * Get deal data
     */
    private function get_deal_data(int $deal_id): array
    {
        return [
            'id' => $deal_id,
            'title' => get_the_title($deal_id),
            'end_date' => get_post_meta($deal_id, 'end_date', true),
            'discount_percentage' => get_post_meta($deal_id, 'discount_percentage', true),
        ];
    }

    /**
     * Get packages from deal with promo enabled
     */
    private function get_deal_packages(int $deal_id): array
    {
        $package_ids = get_post_meta($deal_id, 'packages', true);

        if (empty($package_ids) || !is_array($package_ids)) {
            return [];
        }

        $packages = [];

        foreach ($package_ids as $package_id) {
            $package_id = intval($package_id);

            // Only include published packages with promo enabled
            if (get_post_status($package_id) !== 'publish') {
                continue;
            }

            $promo_enabled = get_post_meta($package_id, 'promo_enabled', true);
            if (!$promo_enabled) {
                continue;
            }

            // Get package data
            $packages[] = $this->get_package_data($package_id);
        }

        return $packages;
    }

    /**
     * Get single package data
     */
    private function get_package_data(int $package_id): array
    {
        // Get thumbnail
        $thumbnail_id = get_post_thumbnail_id($package_id);
        $thumbnail_url = $thumbnail_id ? wp_get_attachment_image_url($thumbnail_id, 'large') : '';

        // Get days
        $days = get_post_meta($package_id, 'days', true);

        // Get package type taxonomy
        $package_type_terms = wp_get_post_terms($package_id, 'package_type');
        $package_type = !empty($package_type_terms) ? $package_type_terms[0]->name : '';

        // Get physical difficulty
        $physical_difficulty = get_post_meta($package_id, 'physical_difficulty', true);

        // Get rating
        $rating = get_post_meta($package_id, 'rating', true);

        // Get included services (taxonomy)
        $included_services_terms = wp_get_post_terms($package_id, 'included_services');
        $included_services = [];
        if (!empty($included_services_terms)) {
            foreach ($included_services_terms as $term) {
                $included_services[] = [
                    'id' => $term->term_id,
                    'name' => $term->name,
                    'slug' => $term->slug,
                ];
            }
        }

        // Get summary
        $summary = get_post_meta($package_id, 'summary', true);

        // Get pricing
        $price_normal = get_post_meta($package_id, 'price_normal', true);
        $price_offer = get_post_meta($package_id, 'price_offer', true);

        // Get promo tag
        $promo_tag = get_post_meta($package_id, 'promo_tag', true) ?: 'TOP SELLER';
        $promo_tag_color = get_post_meta($package_id, 'promo_tag_color', true) ?: '#e78c85';

        return [
            'id' => $package_id,
            'title' => get_the_title($package_id),
            'url' => get_permalink($package_id),
            'thumbnail_url' => $thumbnail_url,
            'days' => $days,
            'package_type' => $package_type,
            'physical_difficulty' => $physical_difficulty,
            'rating' => floatval($rating),
            'included_services' => $included_services,
            'summary' => $summary,
            'price_normal' => floatval($price_normal),
            'price_offer' => floatval($price_offer),
            'promo_tag' => $promo_tag,
            'promo_tag_color' => $promo_tag_color,
        ];
    }
}
