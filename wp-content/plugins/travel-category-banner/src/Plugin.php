<?php
namespace Travel\CategoryBanner;

class Plugin {
    public function init(): void {
        add_action('init', [$this, 'registerAssets']);
        add_action('acf/init', [$this, 'registerBlocks']);
        add_action('acf/init', [$this, 'registerFieldGroups']);
    }

    public function registerAssets(): void {
        $url = plugin_dir_url(TRAVEL_CATEGORY_BANNER_FILE ?? __FILE__);
        $ver = '1.0.0';

        // CSS principal del banner
        wp_register_style('tcb-banner', $url . 'src/Assets/css/banner-swiper.css', [], $ver);

        // Swiper (desde CDN para no duplicar archivos; se carga solo cuando el bloque está presente)
        if (!wp_script_is('swiper', 'registered')) {
            wp_register_script('swiper', 'https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js', [], '10.3.0', true);
            wp_register_style('swiper', 'https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css', [], '10.3.0');
        }

        // JS de inicialización
        wp_register_script('tcb-banner', $url . 'src/Assets/js/banner-swiper.js', ['swiper'], $ver, true);
    }

    public function registerBlocks(): void {
        if (!function_exists('acf_register_block_type')) return;

        // Bloque Dinámico
        \acf_register_block_type([
            'name'              => 'tcb-category-banner-dynamic',
            'title'             => __('Travel • Category Banner (Dynamic)', 'travel-category-banner'),
            'description'       => __('Banner dinámico por taxonomía + carrusel de paquetes en oferta relacionados.', 'travel-category-banner'),
            'category'          => 'widgets',
            'icon'              => 'format-image',
            'mode'              => 'preview',
            'supports'          => [ 'align' => ['full', 'wide'], 'anchor' => true ],
            'enqueue_assets'    => function(){
                wp_enqueue_style('swiper');
                wp_enqueue_style('tcb-banner');
                wp_enqueue_script('swiper');
                wp_enqueue_script('tcb-banner');
            },
            'render_callback'   => [new \Travel\CategoryBanner\Blocks\CategoryBannerDynamic(), 'render'],
        ]);

        // Bloque Estático
        \acf_register_block_type([
            'name'              => 'tcb-category-banner-static',
            'title'             => __('Travel • Category Banner (Static)', 'travel-category-banner'),
            'description'       => __('Banner manual + carrusel de paquetes en oferta globales.', 'travel-category-banner'),
            'category'          => 'widgets',
            'icon'              => 'images-alt2',
            'mode'              => 'preview',
            'supports'          => [ 'align' => ['full', 'wide'], 'anchor' => true ],
            'enqueue_assets'    => function(){
                wp_enqueue_style('swiper');
                wp_enqueue_style('tcb-banner');
                wp_enqueue_script('swiper');
                wp_enqueue_script('tcb-banner');
            },
            'render_callback'   => [new \Travel\CategoryBanner\Blocks\CategoryBannerStatic(), 'render'],
        ]);
    }

    public function registerFieldGroups(): void {
        if (!function_exists('acf_add_local_field_group')) return;

        // === Campo ACF para TAXONOMÍAS: background_image (aplica a todos los términos) ===
        \acf_add_local_field_group([
            'key' => 'group_tcb_taxonomy_background',
            'title' => '🖼️ Term Background',
            'fields' => [
                [
                    'key' => 'field_tcb_term_background',
                    'label' => 'Background Image',
                    'name' => 'background_image',
                    'type' => 'image',
                    'return_format' => 'array',
                    'preview_size' => 'medium',
                    'library' => 'all',
                    'instructions' => 'Imagen de fondo para el banner de esta categoría/término.'
                ]
            ],
            'location' => [
                [
                    [
                        'param' => 'taxonomy',
                        'operator' => '==',
                        'value' => 'all',
                    ]
                ]
            ]
        ]);

        // === Campos del Bloque Dinámico (solo Logo) ===
        \acf_add_local_field_group([
            'key' => 'group_tcb_block_dynamic',
            'title' => 'Block • Category Banner (Dynamic)',
            'fields' => [
                [
                    'key' => 'field_tcb_dyn_logo',
                    'label' => 'Logo',
                    'name' => 'logo',
                    'type' => 'image',
                    'return_format' => 'array',
                    'preview_size' => 'medium',
                    'library' => 'all',
                ]
            ],
            'location' => [
                [
                    [
                        'param' => 'block',
                        'operator' => '==',
                        'value' => 'acf/tcb-category-banner-dynamic'
                    ]
                ]
            ]
        ]);

        // === Campos del Bloque Estático ===
        \acf_add_local_field_group([
            'key' => 'group_tcb_block_static',
            'title' => 'Block • Category Banner (Static)',
            'fields' => [
                [
                    'key' => 'field_tcb_sta_title',
                    'label' => 'Title',
                    'name' => 'title',
                    'type' => 'text',
                ],
                [
                    'key' => 'field_tcb_sta_desc',
                    'label' => 'Description',
                    'name' => 'description',
                    'type' => 'textarea',
                    'rows' => 3
                ],
                [
                    'key' => 'field_tcb_sta_button',
                    'label' => 'Button Text',
                    'name' => 'button_text',
                    'type' => 'text',
                ],
                [
                    'key' => 'field_tcb_sta_bg',
                    'label' => 'Background Image',
                    'name' => 'background_image',
                    'type' => 'image',
                    'return_format' => 'array',
                    'preview_size' => 'medium',
                    'library' => 'all',
                ],
                [
                    'key' => 'field_tcb_sta_logo',
                    'label' => 'Logo',
                    'name' => 'logo',
                    'type' => 'image',
                    'return_format' => 'array',
                    'preview_size' => 'medium',
                    'library' => 'all',
                ],
                [
                    'key' => 'field_tcb_sta_packages_source',
                    'label' => 'Packages Source',
                    'name' => 'packages_source',
                    'type' => 'select',
                    'choices' => [
                        'offers_all' => 'All Offers (global)',
                        'manual'     => 'Manual (repeater below)'
                    ],
                    'default_value' => 'offers_all',
                    'ui' => 1
                ],
                [
                    'key' => 'field_tcb_sta_packages',
                    'label' => 'Manual Packages',
                    'name' => 'packages',
                    'type' => 'repeater',
                    'conditional_logic' => [[['field' => 'field_tcb_sta_packages_source', 'operator' => '==', 'value' => 'manual']]],
                    'min' => 0,
                    'layout' => 'row',
                    'button_label' => 'Add package',
                    'sub_fields' => [
                        [
                            'key' => 'field_tcb_sta_pkg_title',
                            'label' => 'Title',
                            'name' => 'title',
                            'type' => 'text',
                        ],
                        [
                            'key' => 'field_tcb_sta_pkg_image',
                            'label' => 'Image',
                            'name' => 'image',
                            'type' => 'image',
                            'return_format' => 'array',
                            'preview_size' => 'medium'
                        ],
                        [
                            'key' => 'field_tcb_sta_pkg_link',
                            'label' => 'Link',
                            'name' => 'link',
                            'type' => 'link'
                        ]
                    ]
                ]
            ],
            'location' => [
                [
                    [
                        'param' => 'block',
                        'operator' => '==',
                        'value' => 'acf/tcb-category-banner-static'
                    ]
                ]
            ]
        ]);
    }
}
