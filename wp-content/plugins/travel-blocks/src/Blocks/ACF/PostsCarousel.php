<?php
/**
 * Block: Posts Carousel (Material Design)
 *
 * Grid 3 columnas en desktop con hover effect.
 * Slider solo en mobile con Material Design.
 * ACF Repeater para control manual de cards.
 *
 * @package Travel\Blocks\Blocks
 * @since 1.0.0
 */

namespace Travel\Blocks\Blocks\ACF;

use Travel\Blocks\Core\BlockBase;
use Travel\Blocks\Helpers\ContentQueryHelper;

class PostsCarousel extends BlockBase
{
    public function __construct()
    {
        $this->name        = 'posts-carousel';
        $this->title       = __('Posts Carousel (Material)', 'travel-blocks');
        $this->description = __('Grid 3 columnas (desktop) + Slider Material Design (mobile). ACF Repeater para control manual.', 'travel-blocks');
        $this->category    = 'travel';
        $this->icon        = 'images-alt2';
        $this->keywords    = ['posts', 'carousel', 'slider', 'material', 'grid'];
        $this->mode        = 'preview';

        $this->supports = [
            'align' => ['wide', 'full'],
            'mode'  => true,
            'multiple' => true,
            'anchor' => true,
        ];
    }

    /**
     * Enqueue block-specific assets.
     *
     * @return void
     */
    public function enqueue_assets(): void
    {
        // Enqueue CSS (frontend + editor)
        wp_enqueue_style(
            'posts-carousel-style',
            TRAVEL_BLOCKS_URL . 'assets/blocks/posts-carousel.css',
            [],
            TRAVEL_BLOCKS_VERSION
        );

        // Enqueue JS (frontend + editor)
        wp_enqueue_script(
            'posts-carousel-script',
            TRAVEL_BLOCKS_URL . 'assets/blocks/posts-carousel.js',
            [],
            TRAVEL_BLOCKS_VERSION,
            true
        );
    }

    /**
     * Get default placeholder image URL
     */
    private function get_placeholder_image(): string
    {
        // URL de placeholder desde picsum.photos
        return 'https://picsum.photos/800/600?random=' . rand(1, 1000);
    }

    /**
     * Register block and its ACF fields.
     *
     * @return void
     */
    public function register(): void
    {
        parent::register();

        // Register ACF fields for this block
        if (function_exists('acf_add_local_field_group')) {
            acf_add_local_field_group([
                'key' => 'group_block_posts_carousel',
                'title' => __('Posts Carousel - Material Design', 'travel-blocks'),
                'fields' => [

                    // ===== TAB: CARD STYLES =====
                    [
                        'key' => 'field_pc_tab_styles',
                        'label' => __('🎨 Card Styles', 'travel-blocks'),
                        'type' => 'tab',
                        'placement' => 'top',
                    ],

                    // CARD LAYOUT STYLE
                    [
                        'key' => 'field_pc_card_style',
                        'label' => __('🎴 Estilo de Card', 'travel-blocks'),
                        'name' => 'card_style',
                        'type' => 'select',
                        'required' => 0,
                        'choices' => [
                            'overlay' => __('Overlay - Imagen de fondo con texto encima', 'travel-blocks'),
                            'vertical' => __('Vertical - Imagen arriba, texto abajo (card normal)', 'travel-blocks'),
                            'overlay-split' => __('Overlay Split - Badge arriba, título/descripción en medio, meta/botón 50-50 abajo', 'travel-blocks'),
                        ],
                        'default_value' => 'overlay',
                        'ui' => 1,
                        'instructions' => __('Estilo de diseño de las cards', 'travel-blocks'),
                    ],

                    [
                        'key' => 'field_pc_button_color_variant',
                        'label' => __('🎨 Color del Botón', 'travel-blocks'),
                        'name' => 'button_color_variant',
                        'type' => 'select',
                        'required' => 0,
                        'choices' => [
                            'primary' => __('Primario - Rosa (#E78C85)', 'travel-blocks'),
                            'secondary' => __('Secundario - Morado (#311A42)', 'travel-blocks'),
                            'white' => __('Blanco con texto negro', 'travel-blocks'),
                            'gold' => __('Dorado (#CEA02D)', 'travel-blocks'),
                            'dark' => __('Negro (#1A1A1A)', 'travel-blocks'),
                            'transparent' => __('Transparente con borde blanco', 'travel-blocks'),
                            'read-more' => __('Texto "Read More" (sin fondo)', 'travel-blocks'),
                            'line-arrow' => __('Línea superior + Texto Rosa + Flecha', 'travel-blocks'),
                        ],
                        'default_value' => 'primary',
                        'ui' => 1,
                        'instructions' => __('Color aplicado a todos los botones del bloque', 'travel-blocks'),
                    ],
                    [
                        'key' => 'field_pc_badge_color_variant',
                        'label' => __('🎨 Color de la Etiqueta', 'travel-blocks'),
                        'name' => 'badge_color_variant',
                        'type' => 'select',
                        'required' => 0,
                        'choices' => [
                            'primary' => __('Primario - Rosa (#E78C85)', 'travel-blocks'),
                            'secondary' => __('Secundario - Morado (#311A42)', 'travel-blocks'),
                            'white' => __('Blanco con texto negro', 'travel-blocks'),
                            'gold' => __('Dorado (#CEA02D)', 'travel-blocks'),
                            'dark' => __('Negro (#1A1A1A)', 'travel-blocks'),
                            'transparent' => __('Transparente con borde blanco', 'travel-blocks'),
                        ],
                        'default_value' => 'secondary',
                        'ui' => 1,
                        'instructions' => __('Color aplicado a todas las etiquetas/badges del bloque', 'travel-blocks'),
                    ],
                    [
                        'key' => 'field_pc_text_alignment',
                        'label' => __('📐 Alineación de Texto', 'travel-blocks'),
                        'name' => 'text_alignment',
                        'type' => 'select',
                        'required' => 0,
                        'choices' => [
                            'left' => __('Izquierda', 'travel-blocks'),
                            'center' => __('Centro', 'travel-blocks'),
                            'right' => __('Derecha', 'travel-blocks'),
                        ],
                        'default_value' => 'left',
                        'ui' => 1,
                        'instructions' => __('Alineación del texto (título, descripción, ubicación, precio)', 'travel-blocks'),
                    ],
                    [
                        'key' => 'field_pc_button_alignment',
                        'label' => __('📍 Alineación de Botón', 'travel-blocks'),
                        'name' => 'button_alignment',
                        'type' => 'select',
                        'required' => 0,
                        'choices' => [
                            'left' => __('Izquierda', 'travel-blocks'),
                            'center' => __('Centro', 'travel-blocks'),
                            'right' => __('Derecha', 'travel-blocks'),
                        ],
                        'default_value' => 'left',
                        'ui' => 1,
                        'instructions' => __('Alineación del botón/CTA', 'travel-blocks'),
                    ],
                    [
                        'key' => 'field_pc_show_favorite',
                        'label' => __('❤️ Mostrar Botón Favoritos', 'travel-blocks'),
                        'name' => 'show_favorite',
                        'type' => 'true_false',
                        'required' => 0,
                        'default_value' => 1,
                        'ui' => 1,
                        'instructions' => __('Mostrar botón de corazón en la esquina superior derecha', 'travel-blocks'),
                    ],

                    // ===== DYNAMIC CONTENT FIELDS =====
                    ...ContentQueryHelper::get_dynamic_content_fields('pc_mat'),

                    // ===== TAB: CARDS =====
                    [
                        'key' => 'field_pc_tab_cards',
                        'label' => __('🃏 Cards', 'travel-blocks'),
                        'type' => 'tab',
                        'placement' => 'top',
                        'conditional_logic' => [
                            [
                                [
                                    'field' => 'field_pc_mat_dynamic_source',
                                    'operator' => '==',
                                    'value' => 'none',
                                ],
                            ],
                        ],
                    ],

                    // ===== CARDS REPEATER =====
                    [
                        'key' => 'field_pc_cards',
                        'label' => __('Cards', 'travel-blocks'),
                        'name' => 'cards',
                        'type' => 'repeater',
                        'instructions' => __('Agrega cards manualmente. Desktop: Grid 3 columnas. Mobile: Slider.', 'travel-blocks'),
                        'required' => 0,
                        'min' => 1,
                        'max' => 12,
                        'layout' => 'block',
                        'button_label' => __('Agregar Card', 'travel-blocks'),
                        'collapsed' => 'field_pc_card_title',
                        'conditional_logic' => [
                            [
                                [
                                    'field' => 'field_pc_mat_dynamic_source',
                                    'operator' => '==',
                                    'value' => 'none',
                                ],
                            ],
                        ],
                        'sub_fields' => [
                            [
                                'key' => 'field_pc_card_image',
                                'label' => __('Imagen', 'travel-blocks'),
                                'name' => 'image',
                                'type' => 'image',
                                'instructions' => __('Recomendado: 800x600px. Si se deja vacío, usa placeholder.', 'travel-blocks'),
                                'return_format' => 'array',
                                'preview_size' => 'medium',
                                'library' => 'all',
                                'required' => 0,
                            ],
                            [
                                'key' => 'field_pc_card_title',
                                'label' => __('Título', 'travel-blocks'),
                                'name' => 'title',
                                'type' => 'text',
                                'required' => 1,
                                'default_value' => __('Título de la Card', 'travel-blocks'),
                                'maxlength' => 100,
                            ],
                            [
                                'key' => 'field_pc_card_excerpt',
                                'label' => __('Descripción', 'travel-blocks'),
                                'name' => 'excerpt',
                                'type' => 'textarea',
                                'required' => 0,
                                'rows' => 3,
                                'maxlength' => 200,
                                'default_value' => __('Descripción breve de la card...', 'travel-blocks'),
                            ],
                            [
                                'key' => 'field_pc_card_link',
                                'label' => __('Enlace', 'travel-blocks'),
                                'name' => 'link',
                                'type' => 'url',
                                'required' => 0,
                                'placeholder' => 'https://example.com',
                            ],
                            [
                                'key' => 'field_pc_card_category',
                                'label' => __('Categoría (Badge)', 'travel-blocks'),
                                'name' => 'category',
                                'type' => 'text',
                                'required' => 0,
                                'maxlength' => 30,
                                'placeholder' => __('Ej: Destacado, Nuevo, Promoción', 'travel-blocks'),
                                'instructions' => __('Badge/etiqueta en la esquina superior', 'travel-blocks'),
                            ],
                            [
                                'key' => 'field_pc_card_badge_color',
                                'label' => __('🎨 Color Badge (Individual)', 'travel-blocks'),
                                'name' => 'badge_color_variant',
                                'type' => 'select',
                                'required' => 0,
                                'choices' => [
                                    '' => __('Usar configuración general', 'travel-blocks'),
                                    'primary' => __('Rosa (#E78C85)', 'travel-blocks'),
                                    'secondary' => __('Morado (#311A42)', 'travel-blocks'),
                                    'white' => __('Blanco', 'travel-blocks'),
                                    'gold' => __('Dorado (#CEA02D)', 'travel-blocks'),
                                    'dark' => __('Negro (#1A1A1A)', 'travel-blocks'),
                                    'transparent' => __('Transparente con borde', 'travel-blocks'),
                                ],
                                'default_value' => '',
                                'allow_null' => 1,
                                'ui' => 1,
                                'instructions' => __('Sobrescribe el color general solo para esta card. Si está vacío, usa la configuración general.', 'travel-blocks'),
                            ],
                            [
                                'key' => 'field_pc_card_cta_text',
                                'label' => __('🔘 Texto Botón / CTA', 'travel-blocks'),
                                'name' => 'cta_text',
                                'type' => 'text',
                                'required' => 0,
                                'maxlength' => 30,
                                'default_value' => __('Ver más', 'travel-blocks'),
                                'placeholder' => __('Ej: Explorar, Read more', 'travel-blocks'),
                                'instructions' => __('Texto del botón/enlace de la card', 'travel-blocks'),
                            ],
                            [
                                'key' => 'field_pc_card_location',
                                'label' => __('📍 Ubicación', 'travel-blocks'),
                                'name' => 'location',
                                'type' => 'text',
                                'required' => 0,
                                'maxlength' => 50,
                                'placeholder' => __('Ej: Cusco, Perú', 'travel-blocks'),
                                'instructions' => __('Ubicación mostrada debajo de la descripción', 'travel-blocks'),
                            ],
                            [
                                'key' => 'field_pc_card_price',
                                'label' => __('💰 Precio', 'travel-blocks'),
                                'name' => 'price',
                                'type' => 'text',
                                'required' => 0,
                                'maxlength' => 20,
                                'placeholder' => __('Ej: $299', 'travel-blocks'),
                                'instructions' => __('Precio del tour/producto', 'travel-blocks'),
                            ],
                        ],
                    ],

                    // ===== FILTER FIELDS =====
                    ...ContentQueryHelper::get_filter_fields('pc_mat'),

                    // ===== SLIDER SETTINGS (Mobile) =====
                    [
                        'key' => 'field_pc_slider_settings',
                        'label' => __('⚙️ Configuración del Slider (Mobile)', 'travel-blocks'),
                        'type' => 'tab',
                        'placement' => 'top',
                    ],
                    [
                        'key' => 'field_pc_show_arrows',
                        'label' => __('Mostrar Flechas de Navegación', 'travel-blocks'),
                        'name' => 'show_arrows',
                        'type' => 'true_false',
                        'instructions' => __('Flechas prev/next en slider mobile', 'travel-blocks'),
                        'default_value' => 1,
                        'ui' => 1,
                    ],
                    [
                        'key' => 'field_pc_arrows_position',
                        'label' => __('Posición de las Flechas (Mobile)', 'travel-blocks'),
                        'name' => 'arrows_position',
                        'type' => 'select',
                        'instructions' => __('Ubicación de los botones de navegación en mobile', 'travel-blocks'),
                        'choices' => [
                            'sides' => __('A los lados del slider (70% ancho)', 'travel-blocks'),
                            'overlay' => __('Encima de la imagen', 'travel-blocks'),
                            'bottom' => __('Abajo, al lado de los dots', 'travel-blocks'),
                        ],
                        'default_value' => 'sides',
                        'ui' => 1,
                        'conditional_logic' => [
                            [
                                [
                                    'field' => 'field_pc_show_arrows',
                                    'operator' => '==',
                                    'value' => '1',
                                ],
                            ],
                        ],
                    ],
                    [
                        'key' => 'field_pc_show_dots',
                        'label' => __('Mostrar Dots de Paginación', 'travel-blocks'),
                        'name' => 'show_dots',
                        'type' => 'true_false',
                        'instructions' => __('Dots debajo del slider en mobile', 'travel-blocks'),
                        'default_value' => 1,
                        'ui' => 1,
                    ],
                    [
                        'key' => 'field_pc_autoplay',
                        'label' => __('Autoplay (Mobile)', 'travel-blocks'),
                        'name' => 'autoplay',
                        'type' => 'true_false',
                        'instructions' => __('Avance automático en mobile (pausa en hover)', 'travel-blocks'),
                        'default_value' => 0,
                        'ui' => 1,
                    ],
                    [
                        'key' => 'field_pc_autoplay_delay',
                        'label' => __('Delay del Autoplay', 'travel-blocks'),
                        'name' => 'autoplay_delay',
                        'type' => 'range',
                        'instructions' => __('Tiempo entre slides (segundos)', 'travel-blocks'),
                        'required' => 0,
                        'default_value' => 5,
                        'min' => 2,
                        'max' => 10,
                        'step' => 0.5,
                        'append' => 's',
                        'conditional_logic' => [
                            [
                                [
                                    'field' => 'field_pc_autoplay',
                                    'operator' => '==',
                                    'value' => '1',
                                ],
                            ],
                        ],
                    ],
                    [
                        'key' => 'field_pc_slider_speed',
                        'label' => __('Velocidad de Transición', 'travel-blocks'),
                        'name' => 'slider_speed',
                        'type' => 'range',
                        'instructions' => __('Velocidad de animación entre slides', 'travel-blocks'),
                        'default_value' => 0.4,
                        'min' => 0.2,
                        'max' => 1,
                        'step' => 0.1,
                        'append' => 's',
                    ],

                    // ===== DESKTOP GRID SETTINGS =====
                    [
                        'key' => 'field_pc_grid_settings',
                        'label' => __('🖥️ Configuración del Grid (Desktop)', 'travel-blocks'),
                        'type' => 'tab',
                        'placement' => 'top',
                    ],
                    [
                        'key' => 'field_pc_desktop_columns',
                        'label' => __('Columnas en Desktop', 'travel-blocks'),
                        'name' => 'desktop_columns',
                        'type' => 'range',
                        'instructions' => __('Número de columnas en pantallas grandes (≥1025px)', 'travel-blocks'),
                        'default_value' => 3,
                        'min' => 1,
                        'max' => 6,
                        'step' => 1,
                    ],
                    [
                        'key' => 'field_pc_tablet_columns',
                        'label' => __('Columnas en Tablet', 'travel-blocks'),
                        'name' => 'tablet_columns',
                        'type' => 'range',
                        'instructions' => __('Número de columnas en tablets (769px - 1024px)', 'travel-blocks'),
                        'default_value' => 2,
                        'min' => 1,
                        'max' => 4,
                        'step' => 1,
                    ],
                    [
                        'key' => 'field_pc_hover_effect',
                        'label' => __('Efecto Hover', 'travel-blocks'),
                        'name' => 'hover_effect',
                        'type' => 'select',
                        'instructions' => __('Efecto al pasar el mouse sobre una card', 'travel-blocks'),
                        'choices' => [
                            'zoom' => __('Zoom - Agrandar card', 'travel-blocks'),
                            'squeeze' => __('Squeeze - Crece y empuja las demás', 'travel-blocks'),
                            'lift' => __('Lift - Elevar card', 'travel-blocks'),
                            'glow' => __('Glow - Resaltar bordes', 'travel-blocks'),
                            'tilt' => __('Tilt - Inclinar en 3D', 'travel-blocks'),
                            'fade' => __('Fade - Desvanecer las demás', 'travel-blocks'),
                            'slide' => __('Slide - Deslizar hacia arriba', 'travel-blocks'),
                            'none' => __('Ninguno - Sin efecto', 'travel-blocks'),
                        ],
                        'default_value' => 'squeeze',
                    ],
                    [
                        'key' => 'field_pc_card_gap',
                        'label' => __('Espacio entre Cards', 'travel-blocks'),
                        'name' => 'card_gap',
                        'type' => 'range',
                        'instructions' => __('Separación en el grid desktop', 'travel-blocks'),
                        'default_value' => 24,
                        'min' => 12,
                        'max' => 48,
                        'step' => 4,
                        'append' => 'px',
                    ],
                    [
                        'key' => 'field_pc_card_height',
                        'label' => __('Altura de Cards', 'travel-blocks'),
                        'name' => 'card_height',
                        'type' => 'range',
                        'instructions' => __('Altura mínima de las cards (solo para variación Travel)', 'travel-blocks'),
                        'default_value' => 450,
                        'min' => 300,
                        'max' => 700,
                        'step' => 10,
                        'append' => 'px',
                    ],
                ],
                'location' => [
                    [
                        [
                            'param' => 'block',
                            'operator' => '==',
                            'value' => 'acf/posts-carousel',
                        ],
                    ],
                ],
            ]);
        }
    }

    /**
     * Render block content.
     */
    public function render(array $block, string $content = '', bool $is_preview = false, int $post_id = 0): void
    {
        try {
            // Log render start
            if (function_exists('travel_info')) {
                travel_info('PostsCarousel render iniciado', [
                    'block_id' => $block['id'] ?? 'unknown',
                    'is_preview' => $is_preview,
                    'post_id' => $post_id,
                ]);
            }

            // Check if using dynamic content
            $dynamic_source = get_field('pc_mat_dynamic_source');

            if ($dynamic_source === 'package') {
                // Get dynamic packages
                $cards = ContentQueryHelper::get_content('pc_mat', 'package');

                if (function_exists('travel_info')) {
                    travel_info('Usando contenido dinámico de packages', [
                        'cards_count' => count($cards),
                    ]);
                }
            } elseif ($dynamic_source === 'post') {
                // Get dynamic blog posts
                $cards = ContentQueryHelper::get_content('pc_mat', 'post');

                if (function_exists('travel_info')) {
                    travel_info('Usando contenido dinámico de blog posts', [
                        'cards_count' => count($cards),
                    ]);
                }
            } elseif ($dynamic_source === 'deal') {
                // Dynamic content from selected deal's packages
                $deal_id = get_field('pc_mat_deal_selector');
                if ($deal_id) {
                    $cards = ContentQueryHelper::get_deal_packages($deal_id, 'pc_mat');
                    if (function_exists('travel_info')) {
                        travel_info('Usando paquetes del deal seleccionado', [
                            'deal_id' => $deal_id,
                            'cards_count' => count($cards),
                        ]);
                    }
                } else {
                    $cards = [];
                }
            } else {
                // Get cards from ACF Repeater (manual mode)
                if (function_exists('travel_info')) {
                    travel_info('Obteniendo field: cards (manual)');
                }

                $cards = get_field('cards') ?: [];

                if (function_exists('travel_info')) {
                    travel_info('Cards obtenidas (manual)', [
                        'cards_count' => count($cards),
                        'is_empty' => empty($cards),
                    ]);
                }

                // Si no hay cards, mostrar placeholders de ejemplo
                if (empty($cards)) {
                    $cards = $this->get_demo_cards();
                    if (function_exists('travel_info')) {
                        travel_info('Usando demo cards (no hay cards definidas)', [
                            'demo_count' => count($cards),
                        ]);
                    }
                }
            }

            // Get settings
            if (function_exists('travel_info')) {
                travel_info('Obteniendo settings globales');
            }

        $card_style = get_field('card_style') ?: 'overlay';
        $button_color_variant = get_field('button_color_variant') ?: 'primary';
        $badge_color_variant = get_field('badge_color_variant') ?: 'secondary';
        $text_alignment = get_field('text_alignment') ?: 'left';
        $button_alignment = get_field('button_alignment') ?: 'left';
        $show_favorite = get_field('show_favorite');
        if ($show_favorite === null || $show_favorite === '') {
            $show_favorite = true; // Default to true
        }

            if (function_exists('travel_info')) {
                travel_info('Settings globales obtenidos', [
                    'button_color' => $button_color_variant,
                    'text_alignment' => $text_alignment,
                    'button_alignment' => $button_alignment,
                    'show_favorite' => $show_favorite,
                ]);
            }
            if (function_exists('travel_info')) {
                travel_info('Obteniendo settings del slider');
            }

        $show_arrows = (bool)(get_field('show_arrows') ?? true);
        $arrows_position = get_field('arrows_position') ?: 'sides';
        $show_dots = (bool)(get_field('show_dots') ?? true);
        $autoplay = (bool)(get_field('autoplay') ?? false);
        $autoplay_delay = (float)(get_field('autoplay_delay') ?: 5) * 1000; // convert to ms
        $slider_speed = (float)(get_field('slider_speed') ?: 0.4);
        $hover_effect = get_field('hover_effect') ?: 'squeeze';
        $card_gap = (int)(get_field('card_gap') ?: 24);
        $desktop_columns = (int)(get_field('desktop_columns') ?: 3);
        $tablet_columns = (int)(get_field('tablet_columns') ?: 2);
        $card_height = (int)(get_field('card_height') ?: 450);

        // Get Display Fields (control what to show in each card)
        $display_fields_packages = get_field('pc_mat_dynamic_visible_fields') ?: [];
        $display_fields_posts = get_field('pc_mat_dynamic_visible_fields') ?: [];

            if (function_exists('travel_info')) {
                travel_info('Settings obtenidos correctamente', [
                    'show_arrows' => $show_arrows,
                    'show_dots' => $show_dots,
                    'autoplay' => $autoplay,
                    'hover_effect' => $hover_effect,
                ]);
            }

        // Block attributes
        $block_id = 'pc-' . ($block['id'] ?? uniqid());
        $align = $block['align'] ?? 'wide';

        // Pass data to template
        $data = [
            'block_id' => $block_id,
            'align' => $align,
            'card_style' => $card_style,
            'button_color_variant' => $button_color_variant,
            'badge_color_variant' => $badge_color_variant,
            'text_alignment' => $text_alignment,
            'button_alignment' => $button_alignment,
            'show_favorite' => $show_favorite,
            'cards' => $cards,
            'show_arrows' => $show_arrows,
            'arrows_position' => $arrows_position,
            'show_dots' => $show_dots,
            'autoplay' => $autoplay,
            'autoplay_delay' => $autoplay_delay,
            'slider_speed' => $slider_speed,
            'hover_effect' => $hover_effect,
            'card_gap' => $card_gap,
            'desktop_columns' => $desktop_columns,
            'tablet_columns' => $tablet_columns,
            'card_height' => $card_height,
            'display_fields_packages' => $display_fields_packages,
            'display_fields_posts' => $display_fields_posts,
            'is_preview' => $is_preview,
            'block' => $block,
        ];

            if (function_exists('travel_info')) {
                travel_info('Cargando template posts-carousel', [
                    'data_keys' => array_keys($data),
                ]);
            }

            $this->load_template('posts-carousel', $data);

            if (function_exists('travel_info')) {
                travel_info('PostsCarousel render completado exitosamente');
            }
        } catch (\Exception $e) {
            // Log error
            if (function_exists('travel_error')) {
                travel_error('Error en PostsCarousel render', [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }

            // Error handling
            if (defined('WP_DEBUG') && WP_DEBUG) {
                echo '<div style="padding: 20px; background: #ffebee; border: 2px solid #f44336; border-radius: 4px;">';
                echo '<h3 style="margin: 0 0 10px; color: #c62828;">Error en Posts Carousel</h3>';
                echo '<p style="margin: 0; font-family: monospace; font-size: 13px;">' . esc_html($e->getMessage()) . '</p>';
                echo '<p style="margin: 10px 0 0; font-size: 12px; color: #666;">' . esc_html($e->getFile()) . ':' . $e->getLine() . '</p>';
                echo '<details style="margin-top: 10px;"><summary style="cursor: pointer; color: #1976d2;">Ver stack trace</summary>';
                echo '<pre style="margin-top: 10px; font-size: 11px; overflow: auto;">' . esc_html($e->getTraceAsString()) . '</pre>';
                echo '</details>';
                echo '</div>';
            } else {
                echo '<div style="padding: 20px; text-align: center; color: #666;">';
                echo '<p>No se pudo cargar el contenido. Por favor, contacta al administrador.</p>';
                echo '</div>';
            }
        }
    }

    /**
     * Get demo cards con placeholders
     */
    private function get_demo_cards(): array
    {
        return [
            [
                'image' => ['url' => 'https://picsum.photos/800/600?random=1'],
                'title' => 'Machu Picchu Classic Tour',
                'excerpt' => 'Descubre la ciudadela inca más famosa del mundo en este tour guiado de día completo.',
                'link' => '#',
                'category' => 'Destacado',
                'location' => 'Cusco, Perú',
                'price' => '$299',
                'cta_text' => 'Ver detalles',
            ],
            [
                'image' => ['url' => 'https://picsum.photos/800/600?random=2'],
                'title' => 'Inca Trail 4 Days',
                'excerpt' => 'Camino del Inca clásico de 4 días hasta Machu Picchu. Una experiencia única.',
                'link' => '#',
                'category' => 'Aventura',
                'location' => 'Cusco, Perú',
                'price' => '$599',
                'cta_text' => 'Reservar ahora',
            ],
            [
                'image' => ['url' => 'https://picsum.photos/800/600?random=3'],
                'title' => 'Rainbow Mountain',
                'excerpt' => 'Visita la montaña de 7 colores, una maravilla natural de los Andes peruanos.',
                'link' => '#',
                'category' => 'Nuevo',
                'location' => 'Vinicunca, Perú',
                'price' => '$89',
                'cta_text' => 'Descubrir',
            ],
        ];
    }
}
