<?php

/**
 * Plugin Name: Travel Swiper Blocks v2
 * Description: Bloques Gutenberg (ACF) con Swiper.js en mobile y grid en desktop. Campos ACF registrados dentro del bloque.
 * Author: Attach Devs
 * Version: 2.0.0
 */
if (!defined('ABSPATH')) exit;
final class Travel_Swiper_Blocks_V2
{
    private static $instance = null;
    private $version = '2.0.6';

    public static function instance()
    {
        if (self::$instance === null) self::$instance = new self();
        return self::$instance;
    }
    private function __construct()
    {
        add_action('init', [$this, 'register_assets']);
        add_action('acf/init', [$this, 'register_blocks']);
    }
    public function register_assets()
    {
        wp_register_style('swiper', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css', [], null);
        wp_register_script('swiper', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js', [], null, true);
        wp_register_style('tsb-style', plugin_dir_url(__FILE__) . 'assets/css/travel-swiper-style.css', [], $this->version);
        wp_register_script('tsb-init', plugin_dir_url(__FILE__) . 'assets/js/travel-swiper-init.js', ['swiper'], $this->version, true);
        wp_register_script('tsb-init', plugin_dir_url(__FILE__) . 'assets/js/travel-swiper-init-maps.js', ['swiper'], $this->version, true);
    }
    public function register_blocks()
    {
        if (!function_exists('acf_register_block_type')) return;
        // Bloque Team
        acf_register_block_type([
            'name'            => 'travel-team',
            'title'           => __('Travel Team', 'tsb'),
            'render_template' => plugin_dir_path(__FILE__) . 'blocks/team/render.php',
            'category'        => 'widgets',
            'icon'            => 'groups',
            'mode'            => 'preview',
            'enqueue_assets'  => function () {
                wp_enqueue_style('swiper');
                wp_enqueue_script('swiper');
                wp_enqueue_style('tsb-style');
                wp_enqueue_script('tsb-init');
            },
        ]);
        acf_add_local_field_group([
            'key'    => 'group_tsb_team',
            'title'  => 'Team Block Settings',
            'fields' => [
                [
                    'key'           => 'field_tsb_rows_team',
                    'label'         => 'Filas en móvil',
                    'name'          => 'layout_rows',
                    'type'          => 'select',
                    'choices'       => [
                        '1' => '1 fila',
                        '2' => '2 filas',
                        '3' => '3 filas',
                    ],
                    'default_value' => '1',
                ],
                [
                    'key'          => 'field_tsb_slides_team',
                    'label'        => 'Miembros del equipo',
                    'name'         => 'slides',
                    'type'         => 'repeater',
                    'button_label' => 'Agregar imagen',
                    'sub_fields'   => [
                        [
                            'key'           => 'field_team_img',
                            'label'         => 'Imagen',
                            'name'          => 'image',
                            'type'          => 'image',
                            'return_format' => 'id',
                            'preview_size'  => 'medium',
                            'instructions'  => 'Sube una imagen para mostrar en el slider.',
                        ],
                    ],
                ],
            ],
            'location' => [
                [
                    [
                        'param'    => 'block',
                        'operator' => '==',
                        'value'    => 'acf/travel-team',
                    ],
                ],
            ],
        ]);

        // Bloque Icons Grid
        acf_register_block_type([
            'name' => 'travel-testimonials',
            'title' => __('Travel Icons Grid', 'tsb'),
            'render_template' => plugin_dir_path(__FILE__) . 'blocks/testimonials/render.php',
            'category' => 'widgets',
            'icon' => 'smiley',
            'enqueue_assets' => function () {
                wp_enqueue_style('swiper');
                wp_enqueue_script('swiper');
                wp_enqueue_style('tsb-style');
                wp_enqueue_script('tsb-init');
                // CSS específico del bloque
                wp_enqueue_style('tsb-icons', plugin_dir_url(__FILE__) . 'assets/css/travel-icons.css', [], $this->version);
            }
        ]);

        acf_add_local_field_group([
            'key' => 'group_tsb_testimonials',
            'title' => 'Travel Icons Settings',
            'fields' => [
                [
                    'key' => 'field_tsb_rows_testi',
                    'label' => 'Filas en móvil',
                    'name' => 'layout_rows',
                    'type' => 'select',
                    'choices' => [
                        '1' => '1 fila',
                        '2' => '2 filas',
                        '3' => '3 filas'
                    ],
                    'default_value' => '2'
                ],
                [
                    'key' => 'field_tsb_slides_testi',
                    'label' => 'Items',
                    'name' => 'slides',
                    'type' => 'repeater',
                    'button_label' => 'Agregar item',
                    'sub_fields' => [
                        [
                            'key' => 'field_icon_img',
                            'label' => 'Icono',
                            'name' => 'icon',
                            'type' => 'image',
                            'return_format' => 'id'
                        ],
                        [
                            'key' => 'field_icon_title',
                            'label' => 'Título',
                            'name' => 'title',
                            'type' => 'text'
                        ],
                        [
                            'key' => 'field_icon_desc',
                            'label' => 'Descripción',
                            'name' => 'description',
                            'type' => 'wysiwyg',
                            'toolbar' => 'basic',
                            'media_upload' => 0
                        ]
                    ]
                ]
            ],
            'location' => [
                [
                    ['param' => 'block', 'operator' => '==', 'value' => 'acf/travel-testimonials']
                ]
            ]
        ]);


        // Bloque Steps
        acf_register_block_type([
            'name'            => 'travel-steps',
            'title'           => __('Travel Steps', 'tsb'),
            'render_template' => plugin_dir_path(__FILE__) . 'blocks/steps/render.php',
            'category'        => 'widgets',
            'icon'            => 'editor-ol',
            'mode'            => 'preview',
            'enqueue_assets'  => function () {
                wp_enqueue_style('swiper');
                wp_enqueue_script('swiper');
                wp_enqueue_style('tsb-style');
                wp_enqueue_script('tsb-init');
                wp_enqueue_style('tsb-steps', plugin_dir_url(__FILE__) . 'assets/css/travel-steps.css', [], $this->version);
            },
        ]);

        acf_add_local_field_group([
            'key' => 'group_tsb_steps',
            'title' => 'Travel Steps Settings',
            'fields' => [
                [
                    'key' => 'field_tsb_rows_steps',
                    'label' => 'Filas en móvil',
                    'name' => 'layout_rows',
                    'type' => 'select',
                    'choices' => [
                        '1' => '1 fila',
                        '2' => '2 filas',
                        '3' => '3 filas',
                    ],
                    'default_value' => '3',
                ],
                [
                    'key' => 'field_tsb_repeater_steps',
                    'label' => 'Pasos',
                    'name' => 'steps',
                    'type' => 'repeater',
                    'layout' => 'row',
                    'button_label' => 'Agregar paso',
                    'sub_fields' => [
                        ['key' => 'field_step_icon', 'label' => 'Icono (número)', 'name' => 'icon', 'type' => 'image', 'return_format' => 'id'],
                        ['key' => 'field_step_title', 'label' => 'Título', 'name' => 'title', 'type' => 'text'],
                        ['key' => 'field_step_desc', 'label' => 'Descripción', 'name' => 'description', 'type' => 'wysiwyg', 'toolbar' => 'basic', 'media_upload' => 0],
                        ['key' => 'field_step_image', 'label' => 'Imagen principal', 'name' => 'image', 'type' => 'image', 'return_format' => 'id'],
                        ['key' => 'field_step_cta', 'label' => 'Texto del botón', 'name' => 'cta', 'type' => 'text'],
                        ['key' => 'field_step_link', 'label' => 'Enlace del botón', 'name' => 'link', 'type' => 'url'],
                    ],
                ],
            ],
            'location' => [
                [['param' => 'block', 'operator' => '==', 'value' => 'acf/travel-steps']],
            ],
        ]);

        // Bloque Checklist
        acf_register_block_type([
            'name'            => 'travel-checklist',
            'title'           => __('Travel Checklist', 'tsb'),
            'render_template' => plugin_dir_path(__FILE__) . 'blocks/checklist/render.php',
            'category'        => 'widgets',
            'icon'            => 'editor-ul',
            'mode'            => 'preview',
            'enqueue_assets'  => function () {
                wp_enqueue_style('swiper');
                wp_enqueue_script('swiper');
                wp_enqueue_style('tsb-style');
                wp_enqueue_script('tsb-init');
                wp_enqueue_style('tsb-checklist', plugin_dir_url(__FILE__) . 'assets/css/travel-checklist.css', [], $this->version);
            },
        ]);

        acf_add_local_field_group([
            'key' => 'group_tsb_checklist',
            'title' => 'Travel Checklist Settings',
            'fields' => [
                [
                    'key' => 'field_tsb_rows_checklist',
                    'label' => 'Filas en móvil',
                    'name' => 'layout_rows',
                    'type' => 'select',
                    'choices' => [
                        '1' => '1 fila',
                        '2' => '2 filas',
                        '3' => '3 filas',
                    ],
                    'default_value' => '2',
                ],
                [
                    'key' => 'field_tsb_repeater_checklist',
                    'label' => 'Items del checklist',
                    'name' => 'items',
                    'type' => 'repeater',
                    'layout' => 'block', // ✅ vertical, uno debajo del otro
                    'button_label' => 'Agregar item',
                    'sub_fields' => [
                        ['key' => 'field_check_title', 'label' => 'Título', 'name' => 'title', 'type' => 'text'],
                        ['key' => 'field_check_desc', 'label' => 'Descripción', 'name' => 'description', 'type' => 'textarea', 'rows' => 3],
                    ],
                ],
                [
                    'key' => 'field_tsb_bottom_image',
                    'label' => 'Imagen final',
                    'name' => 'bottom_image',
                    'type' => 'image',
                    'return_format' => 'id',
                ],
                [
                    'key' => 'field_tsb_bottom_cta',
                    'label' => 'Texto del botón',
                    'name' => 'bottom_cta',
                    'type' => 'text',
                ],
                [
                    'key' => 'field_tsb_bottom_link',
                    'label' => 'Enlace del botón',
                    'name' => 'bottom_link',
                    'type' => 'url',
                ],
            ],
            'location' => [
                [['param' => 'block', 'operator' => '==', 'value' => 'acf/travel-checklist']],
            ],
        ]);

        // Bloque Sustainability
        acf_register_block_type([
            'name'            => 'travel-sustainability',
            'title'           => __('Travel Sustainability', 'tsb'),
            'render_template' => plugin_dir_path(__FILE__) . 'blocks/sustainability/render.php',
            'category'        => 'widgets',
            'icon'            => 'leaf',
            'mode'            => 'preview',
            'enqueue_assets'  => function () {
                wp_enqueue_style('swiper');
                wp_enqueue_script('swiper');
                wp_enqueue_style('tsb-style');
                wp_enqueue_script('tsb-init');
                wp_enqueue_style('tsb-sustain', plugin_dir_url(__FILE__) . 'assets/css/travel-sustain.css', [], $this->version);
            },
        ]);

        acf_add_local_field_group([
            'key' => 'group_tsb_sustain',
            'title' => 'Travel Sustainability Settings',
            'fields' => [
                [
                    'key' => 'field_tsb_rows_sustain',
                    'label' => 'Filas en móvil',
                    'name' => 'layout_rows',
                    'type' => 'select',
                    'choices' => [
                        '1' => '1 fila',
                        '2' => '2 filas',
                        '3' => '3 filas',
                    ],
                    'default_value' => '1',
                ],
                [
                    'key' => 'field_tsb_repeater_sustain',
                    'label' => 'Bloques de sostenibilidad',
                    'name' => 'slides',
                    'type' => 'repeater',
                    'layout' => 'block',
                    'button_label' => 'Agregar bloque',
                    'sub_fields' => [
                        [
                            'key' => 'field_sustain_icon',
                            'label' => 'Icono',
                            'name' => 'icon',
                            'type' => 'image',
                            'return_format' => 'id',
                            'preview_size' => 'thumbnail',
                        ],
                        [
                            'key' => 'field_sustain_title',
                            'label' => 'Título',
                            'name' => 'title',
                            'type' => 'text',
                        ],
                        [
                            'key' => 'field_sustain_desc',
                            'label' => 'Descripción',
                            'name' => 'description',
                            'type' => 'wysiwyg',
                            'toolbar' => 'basic',
                            'media_upload' => 0,
                        ],
                        [
                            'key' => 'field_sustain_image',
                            'label' => 'Imagen principal',
                            'name' => 'image',
                            'type' => 'image',
                            'return_format' => 'id',
                        ],
                    ],
                ],
                [
                    'key' => 'field_tsb_bottom_cta_sustain',
                    'label' => 'Texto del botón',
                    'name' => 'bottom_cta',
                    'type' => 'text',
                ],
                [
                    'key' => 'field_tsb_bottom_link_sustain',
                    'label' => 'Enlace del botón',
                    'name' => 'bottom_link',
                    'type' => 'url',
                ],
            ],
            'location' => [
                [
                    ['param' => 'block', 'operator' => '==', 'value' => 'acf/travel-sustainability'],
                ],
            ],
        ]);

        // Bloque Travel Cards
        acf_register_block_type([
            'name'            => 'travel-cards',
            'title'           => __('Travel Cards', 'tsb'),
            'render_template' => plugin_dir_path(__FILE__) . 'blocks/cards/render.php',
            'category'        => 'widgets',
            'icon'            => 'images-alt2',
            'mode'            => 'preview',
            'enqueue_assets'  => function () {
                wp_enqueue_style('swiper');
                wp_enqueue_script('swiper');
                wp_enqueue_style('tsb-style');
                wp_enqueue_script('tsb-init');
                wp_enqueue_style('tsb-cards', plugin_dir_url(__FILE__) . 'assets/css/travel-cards.css', [], $this->version);
            },
        ]);

        acf_add_local_field_group([
            'key' => 'group_tsb_cards',
            'title' => 'Travel Cards Settings',
            'fields' => [
                [
                    'key' => 'field_tsb_rows_cards',
                    'label' => 'Filas en móvil',
                    'name' => 'layout_rows',
                    'type' => 'select',
                    'choices' => [
                        '1' => '1 fila',
                        '2' => '2 filas',
                    ],
                    'default_value' => '1',
                ],
                [
                    'key' => 'field_tsb_repeater_cards',
                    'label' => 'Tarjetas',
                    'name' => 'cards',
                    'type' => 'repeater',
                    'layout' => 'block',
                    'button_label' => 'Agregar tarjeta',
                    'sub_fields' => [
                        [
                            'key' => 'field_card_image',
                            'label' => 'Imagen',
                            'name' => 'image',
                            'type' => 'image',
                            'return_format' => 'id',
                        ],
                        [
                            'key' => 'field_card_link',
                            'label' => 'Botón (texto + enlace)',
                            'name' => 'link',
                            'type' => 'link',
                        ],
                    ],
                ],
            ],
            'location' => [
                [
                    ['param' => 'block', 'operator' => '==', 'value' => 'acf/travel-cards'],
                ],
            ],
        ]);

        // Bloque Travel Departments (aislado)
        acf_register_block_type([
            'name'            => 'travel-departments',
            'title'           => __('Travel Departments', 'tsb'),
            'render_template' => plugin_dir_path(__FILE__) . 'blocks/departments/render.php',
            'category'        => 'widgets',
            'icon'            => 'location-alt',
            'mode'            => 'preview',
            'enqueue_assets'  => function () {
                wp_enqueue_style('swiper');
                wp_enqueue_script('swiper');
                wp_enqueue_style('vtc-departments', plugin_dir_url(__FILE__) . 'assets/css/travel-departments.css', [], $this->version);
                wp_enqueue_script('vtc-departments', plugin_dir_url(__FILE__) . 'assets/js/travel-swiper-init-desktop.js', ['swiper'], $this->version, true);
            },
        ]);


        acf_add_local_field_group([
            'key' => 'group_tsb_departments',
            'title' => 'Travel Departments Settings',
            'fields' => [
                [
                    'key' => 'field_departments_repeater',
                    'label' => 'Departamentos',
                    'name' => 'departments',
                    'type' => 'repeater',
                    'layout' => 'block',
                    'button_label' => 'Agregar Departamento',
                    'sub_fields' => [
                        [
                            'key' => 'field_department_name',
                            'label' => 'Nombre del Departamento',
                            'name' => 'department_name',
                            'type' => 'text',
                            'required' => 1,
                            'instructions' => 'Ejemplo: Cusco, Arequipa, Lima...',
                        ],
                        [
                            'key' => 'field_department_image',
                            'label' => 'Imagen del Departamento',
                            'name' => 'department_image',
                            'type' => 'image',
                            'return_format' => 'id',
                            'preview_size' => 'medium',
                            'instructions' => 'Sube una imagen representativa del departamento.',
                        ],
                    ],
                ],
            ],
            'location' => [
                [
                    ['param' => 'block', 'operator' => '==', 'value' => 'acf/travel-departments'],
                ],
            ],
        ]);


        // Bloque Travel Destinations
        acf_register_block_type([
            'name'            => 'travel-destinations',
            'title'           => __('Travel Destinations', 'tsb'),
            'render_template' => plugin_dir_path(__FILE__) . 'blocks/destinations/render.php',
            'category'        => 'widgets',
            'icon'            => 'location-alt',
            'mode'            => 'preview',
            'enqueue_assets'  => function () {
                wp_enqueue_style('swiper');
                wp_enqueue_script('swiper');
                wp_enqueue_style('tsb-style');
                wp_enqueue_script('tsb-init');
                wp_enqueue_style(
                    'tsb-destinations',
                    plugin_dir_url(__FILE__) . 'assets/css/travel-destinations.css',
                    [],
                    $this->version
                );
            },
        ]);

        acf_add_local_field_group([
            'key' => 'group_tsb_destinations',
            'title' => 'Travel Destinations Settings',
            'fields' => [
                [
                    'key' => 'field_tsb_selected_destinations',
                    'label' => 'Seleccionar Destinos',
                    'name' => 'selected_destinations',
                    'type' => 'taxonomy',
                    'taxonomy' => 'destinations',
                    'field_type' => 'multi_select', // o 'checkbox'
                    'multiple' => 1,
                    'add_term' => 0,
                    'save_terms' => 0,
                    'load_terms' => 0,
                    'return_format' => 'object',
                    'instructions' => 'Selecciona manualmente los destinos que aparecerán en el bloque.',
                ],
                [
                    'key' => 'field_tsb_layout_rows_dest',
                    'label' => 'Filas en móvil',
                    'name' => 'layout_rows',
                    'type' => 'select',
                    'choices' => [
                        '1' => '1 fila',
                        '2' => '2 filas',
                    ],
                    'default_value' => '1',
                ],
                [
                    'key' => 'field_tsb_seo_text',
                    'label' => 'Texto SEO',
                    'name' => 'seo_text',
                    'type' => 'textarea',
                    'rows' => 4,
                    'default_value' => 'Soy un texto SEO. Soy un texto SEO. Soy un texto SEO.',
                ],
            ],
            'location' => [
                [
                    ['param' => 'block', 'operator' => '==', 'value' => 'acf/travel-destinations'],
                ],
            ],
        ]);

        // Bloque Travel Category Card
        acf_register_block_type([
            'name'            => 'travel-category-cards',
            'title'           => __('Travel Category Cards', 'tsb'),
            'render_template' => plugin_dir_path(__FILE__) . 'blocks/travel-category-cards/render.php',
            'category'        => 'widgets',
            'icon'            => 'format-gallery',
            'mode'            => 'preview',
            'enqueue_assets'  => function () {
                wp_enqueue_style('swiper');
                wp_enqueue_script('swiper');
                wp_enqueue_style('tsb-style');
                wp_enqueue_script('tsb-init');
                wp_enqueue_style(
                    'tsb-category-cards',
                    plugin_dir_url(__FILE__) . 'assets/css/travel-category-cards.css',
                    [],
                    $this->version
                );
            },
        ]);

        acf_add_local_field_group([
            'key' => 'group_tsb_category_cards',
            'title' => 'Travel Category Cards Settings',
            'fields' => [
                [
                    'key' => 'field_tsb_selected_categories',
                    'label' => 'Seleccionar Categorías',
                    'name' => 'selected_categories',
                    'type' => 'taxonomy',
                    'taxonomy' => 'destinations',
                    'field_type' => 'multi_select',
                    'multiple' => 1,
                    'add_term' => 0,
                    'save_terms' => 0,
                    'load_terms' => 0,
                    'return_format' => 'object',
                    'instructions' => 'Selecciona hasta 5 categorías que se mostrarán como cards.',
                ],
                [
                    'key' => 'field_tsb_cta_text',
                    'label' => 'Texto del botón',
                    'name' => 'cta_text',
                    'type' => 'text',
                    'default_value' => 'View Trip',
                    'instructions' => 'Texto del botón que aparecerá en cada card (por ejemplo: View Trip, Discover, Explore...).',
                ],
            ],
            'location' => [
                [
                    ['param' => 'block', 'operator' => '==', 'value' => 'acf/travel-category-cards'],
                ],
            ],
        ]);

        // ==========================
        // BLOQUE: Travel Packages
        // ==========================
        acf_register_block_type([
            'name'            => 'travel-packages',
            'title'           => __('Travel Packages', 'tsb'),
            'render_template' => plugin_dir_path(__FILE__) . 'blocks/travel-packages/render.php',
            'category'        => 'widgets',
            'icon'            => 'portfolio',
            'mode'            => 'preview',
            'enqueue_assets'  => function () {
                wp_enqueue_style('swiper');
                wp_enqueue_script('swiper');
                wp_enqueue_style('tsb-style');
                wp_enqueue_script('tsb-init');
                wp_enqueue_style('tsb-packages', plugin_dir_url(__FILE__) . 'assets/css/travel-packages.css', [], $this->version);
                wp_enqueue_script('tsb-packages', plugin_dir_url(__FILE__) . 'assets/js/travel-packages.js', [], $this->version, true);
            },
        ]);

        acf_add_local_field_group([
            'key' => 'group_tsb_travel_packages',
            'title' => 'Travel Packages Settings',
            'fields' => [
                [
                    'key' => 'field_tsb_packages_title',
                    'label' => 'Título principal',
                    'name' => 'packages_title',
                    'type' => 'text',
                    'default_value' => 'Popular Packages',
                    'instructions' => 'Título que aparecerá encima del bloque.',
                ],
                [
                    'key' => 'field_tsb_seo_text_packages',
                    'label' => 'Texto descriptivo',
                    'name' => 'seo_text',
                    'type' => 'textarea',
                    'rows' => 3,
                    'default_value' => 'Texto descriptivo. Texto descriptivo.',
                ],
                [
                    'key' => 'field_tsb_selected_packages',
                    'label' => 'Seleccionar Paquetes',
                    'name' => 'selected_packages',
                    'type' => 'relationship',
                    'post_type' => ['package'],
                    'filters' => ['search'],
                    'max' => 8,
                    'return_format' => 'object',
                ],
                [
                    'key' => 'field_tsb_cta_text',
                    'label' => 'Texto del botón',
                    'name' => 'cta_text',
                    'type' => 'text',
                    'default_value' => 'View Trip',
                    'instructions' => 'Texto que mostrará el botón de cada tarjeta.',
                ],
                [
                    'key' => 'field_tsb_tag_label',
                    'label' => 'Etiqueta del paquete',
                    'name' => 'tag_label',
                    'type' => 'text',
                    'instructions' => 'Texto de la etiqueta que aparece sobre la imagen (por defecto "By Train").',
                    'default_value' => 'By Train',
                ],

            ],
            'location' => [
                [
                    ['param' => 'block', 'operator' => '==', 'value' => 'acf/travel-packages'],
                ],
            ],
        ]);

        // ================================
        // BLOQUE: Travel Packages Category
        // ================================
        acf_register_block_type([
            'name'            => 'travel-category-packages',
            'title'           => __('Travel Category Packages (Dynamic)', 'tsb'),
            'render_template' => plugin_dir_path(__FILE__) . 'blocks/travel-category-packages/render.php',
            'category'        => 'widgets',
            'icon'            => 'portfolio',
            'mode'            => 'preview',
            'enqueue_assets'  => function () {
                wp_enqueue_style('swiper');
                wp_enqueue_script('swiper');
                wp_enqueue_style('tsb-style');
                wp_enqueue_script('tsb-init');
                wp_enqueue_style('tcp-category-packages', plugin_dir_url(__FILE__) . 'assets/css/travel-category-packages.css', [], $this->version);
                wp_enqueue_script('tcp-category-packages', plugin_dir_url(__FILE__) . 'assets/js/travel-category-packages.js', ['swiper'], $this->version, true);
            },
        ]);

        acf_add_local_field_group([
            'key' => 'group_tsb_category_packages',
            'title' => 'Travel Category Packages Settings',
            'fields' => [
                ['key' => 'field_tcp_title', 'label' => 'Título principal', 'name' => 'packages_title', 'type' => 'text', 'default_value' => 'Inca Trail Treks to Machu Picchu'],
                ['key' => 'field_tcp_text', 'label' => 'Texto descriptivo', 'name' => 'seo_text', 'type' => 'textarea', 'rows' => 3],
                ['key' => 'field_tcp_cta_text', 'label' => 'Texto del botón', 'name' => 'cta_text', 'type' => 'text', 'default_value' => 'See Details'],
            ],
            'location' => [
                [['param' => 'block', 'operator' => '==', 'value' => 'acf/travel-category-packages']],
            ],
        ]);

        // =====================================
        // BLOQUE: Travel Weather (Images)
        // =====================================
        acf_register_block_type([
            'name'            => 'travel-weather',
            'title'           => __('Travel Weather (Images)', 'tsb'),
            'render_template' => plugin_dir_path(__FILE__) . 'blocks/weather/render.php',
            'category'        => 'widgets',
            'icon'            => 'cloud',
            'mode'            => 'preview',
            'enqueue_assets'  => function () {
                wp_enqueue_style('swiper');
                wp_enqueue_script('swiper');
                wp_enqueue_style('tsb-style');
                wp_enqueue_script('tsb-init');
                wp_enqueue_style('tsb-weather', plugin_dir_url(__FILE__) . 'assets/css/travel-weather.css', [], '1.0.0');
            },
        ]);

        acf_add_local_field_group([
            'key' => 'group_tsb_weather',
            'title' => 'Travel Weather Settings',
            'fields' => [
                [
                    'key' => 'field_tsb_weather_rows',
                    'label' => 'Filas en móvil',
                    'name' => 'layout_rows',
                    'type' => 'select',
                    'choices' => [
                        '1' => '1 fila',
                        '2' => '2 filas',
                        '3' => '3 filas',
                    ],
                    'default_value' => '1',
                ],
                [
                    'key' => 'field_tsb_weather_bg',
                    'label' => 'Imagen de fondo',
                    'name' => 'background_image',
                    'type' => 'image',
                    'return_format' => 'id',
                ],
                [
                    'key' => 'field_tsb_weather_months',
                    'label' => 'Imágenes de los meses',
                    'name' => 'month_images',
                    'type' => 'repeater',
                    'button_label' => 'Agregar imagen',
                    'sub_fields' => [
                        [
                            'key' => 'field_tsb_weather_img',
                            'label' => 'Imagen',
                            'name' => 'image',
                            'type' => 'image',
                            'return_format' => 'id',
                        ],
                    ],
                ],
                [
                    'key' => 'field_tsb_weather_slides_mobile',
                    'label' => 'Slides visibles en móvil',
                    'name' => 'slides_per_view_mobile',
                    'type' => 'number',
                    'instructions' => 'Define cuántos slides se mostrarán en móvil (ej. 1, 1.5, 2.5). Si se deja vacío, usará 1 por defecto.',
                    'default_value' => 1,
                    'min' => 1,
                    'step' => 0.1,
                ],
            ],
            'location' => [
                [['param' => 'block', 'operator' => '==', 'value' => 'acf/travel-weather']],
            ],
        ]);

        // ============================
        // BLOQUE: Travel InnerBlocks Slider
        // ============================
        acf_register_block_type([
            'name'            => 'travel-innerblocks',
            'title'           => __('Travel InnerBlocks Slider', 'tsb'),
            'render_template' => plugin_dir_path(__FILE__) . 'blocks/InnerBlockSlider/render.php',
            'category'        => 'widgets',
            'icon'            => 'screenoptions',
            'mode'            => 'preview',
            'enqueue_assets'  => function () {
                wp_enqueue_style('swiper');
                wp_enqueue_script('swiper');
                wp_enqueue_style('tsb-style');
                wp_enqueue_script('tsb-init'); // tu core swiper init global
            },
            'supports' => [
                'jsx' => true, // ESSENCIAL para InnerBlocks
            ],
        ]);

        acf_add_local_field_group([
            'key' => 'group_tsb_innerblocks',
            'title' => 'InnerBlocks Slider Settings',
            'fields' => [
                [
                    'key' => 'field_tsb_rows_inner',
                    'label' => 'Filas en móvil',
                    'name'  => 'layout_rows',
                    'type'  => 'select',
                    'choices' => [
                        '1' => '1 fila',
                        '2' => '2 filas',
                        '3' => '3 filas'
                    ],
                    'default_value' => '1'
                ],
            ],
            'location' => [
                [
                    ['param' => 'block', 'operator' => '==', 'value' => 'acf/travel-innerblocks']
                ]
            ]
        ]);
    }
}
Travel_Swiper_Blocks_V2::instance();
