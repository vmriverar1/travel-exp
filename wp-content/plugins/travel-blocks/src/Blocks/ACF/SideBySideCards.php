<?php

/**

 * Block: Side by Side Cards (Horizontal Layout)

 *

 * Horizontal cards with image and text side-by-side layout.

 * Desktop: Flexible grid with column-span control.

 * Mobile: Native scroll-snap slider with navigation.

 *

 * Features:

 * - Manual content or dynamic from packages/posts/deals

 * - Flexible column-span pattern for dynamic content

 * - Image position (left/right) and width control

 * - 6 button color variants + 6 badge variants

 * - Grid hover effects (squeeze/lift/glow/zoom/none)

 * - Mobile slider with arrows, dots, and autoplay

 * - ContentQueryHelper integration for dynamic queries

 *

 * @package Travel\Blocks\ACF

 * @since 1.0.0

 * @version 1.1.0 - Refactored: namespace fix, improved docs, extracted column_span logic

 */



namespace Travel\Blocks\Blocks\ACF;



use Travel\Blocks\Core\BlockBase;

use Travel\Blocks\Helpers\ContentQueryHelper;



class SideBySideCards extends BlockBase

{

    public function __construct()

    {

        $this->name        = 'side-by-side-cards';

        $this->title       = __('Side by Side Cards (Horizontal)', 'travel-blocks');

        $this->description = __('Cards horizontales: imagen + texto lado a lado. Grid en desktop, slider en mobile.', 'travel-blocks');

        $this->category    = 'travel';

        $this->icon        = 'align-pull-left';

        $this->keywords    = ['cards', 'horizontal', 'side', 'slider', 'grid', 'image'];

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

     * Loads CSS for grid/slider styles and JavaScript for:

     * - Mobile slider with scroll-snap behavior

     * - Arrow and dot navigation

     * - Autoplay functionality

     *

     * @return void

     */

    public function enqueue_assets(): void

    {

        // Enqueue CSS

        wp_enqueue_style(

            'side-by-side-cards-style',

            TRAVEL_BLOCKS_URL . 'assets/blocks/side-by-side-cards.css',

            [],

            TRAVEL_BLOCKS_VERSION

        );



        // Enqueue JS

        wp_enqueue_script(

            'side-by-side-cards-script',

            TRAVEL_BLOCKS_URL . 'assets/blocks/side-by-side-cards.js',

            [],

            TRAVEL_BLOCKS_VERSION,

            true

        );

    }



    /**

     * Get default placeholder image URL.

     *

     * Generates a random placeholder image URL from picsum.photos

     * for demo cards when no image is provided.

     *

     * @return string Placeholder image URL

     */

    private function get_placeholder_image(): string

    {

        return 'https://picsum.photos/600/400?random=' . rand(1, 1000);

    }



    /**

     * Register block and its ACF fields.

     *

     * Registers ACF block type and defines comprehensive field groups organized in tabs:

     * - Content: show_favorite, ContentQueryHelper fields, column_span_pattern, cards repeater

     * - Slider (Mobile): show_arrows, show_dots, autoplay, autoplay_delay

     * - Grid (Desktop): grid_columns, card_gap, hover_effect

     * - Styles: image_position, image_width, image_border_radius, alignments, color variants

     *

     * @return void

     */

    public function register(): void

    {

        parent::register();



        // Register ACF fields for this block

        if (function_exists('acf_add_local_field_group')) {

            acf_add_local_field_group([

                'key' => 'group_block_side_by_side_cards',

                'title' => __('Side by Side Cards - Horizontal Layout', 'travel-blocks'),

                'fields' => [



                    // ===== TAB: CONTENIDO =====

                    [

                        'key' => 'field_sbs_tab_content',

                        'label' => __('📝 Contenido', 'travel-blocks'),

                        'type' => 'tab',

                        'placement' => 'top',

                    ],



                    [

                        'key' => 'field_sbs_show_favorite',

                        'label' => __('❤️ Mostrar Botón Favoritos', 'travel-blocks'),

                        'name' => 'show_favorite',

                        'type' => 'true_false',

                        'required' => 0,

                        'default_value' => 1,

                        'ui' => 1,

                        'instructions' => __('Mostrar botón de corazón en la esquina superior derecha', 'travel-blocks'),

                    ],



                    // ===== DYNAMIC CONTENT FIELDS =====

                    ...ContentQueryHelper::get_dynamic_content_fields('sbs'),



                    // ===== COLUMN SPAN PATTERN (para contenido dinámico) =====

                    [

                        'key' => 'field_sbs_column_span_pattern',

                        'label' => __('📏 Patrón de Ancho de Cards (Dinámico)', 'travel-blocks'),

                        'name' => 'column_span_pattern',

                        'type' => 'text',

                        'instructions' => __('Define cuántos espacios ocupa cada card (ej: "1,2,1,1"). El patrón se repite si hay más cards. Deja vacío para que todas ocupen 1 espacio.', 'travel-blocks'),

                        'default_value' => '',

                        'placeholder' => '1,2,1,1',

                        'conditional_logic' => [

                            [

                                [

                                    'field' => 'field_sbs_dynamic_source',

                                    'operator' => '!=',

                                    'value' => 'none',

                                ],

                            ],

                        ],

                    ],



                    // ===== CARDS REPEATER (Manual) =====

                    [

                        'key' => 'field_sbs_cards',

                        'label' => __('Cards (Manual)', 'travel-blocks'),

                        'name' => 'cards',

                        'type' => 'repeater',

                        'instructions' => __('Agrega cards manualmente. Desktop: Grid horizontal. Mobile: Slider.', 'travel-blocks'),

                        'required' => 0,

                        'min' => 1,

                        'max' => 12,

                        'layout' => 'block',

                        'button_label' => __('Agregar Card', 'travel-blocks'),

                        'collapsed' => 'field_sbs_card_title',

                        'conditional_logic' => [

                            [

                                [

                                    'field' => 'field_sbs_dynamic_source',

                                    'operator' => '==',

                                    'value' => 'none',

                                ],

                            ],

                        ],

                        'sub_fields' => [

                            [

                                'key' => 'field_sbs_card_column_span',

                                'label' => __('📏 Ancho (Espacios del Grid)', 'travel-blocks'),

                                'name' => 'column_span',

                                'type' => 'range',

                                'instructions' => __('Cuántos espacios del grid ocupa esta card. 1 = normal, 2 = doble ancho, etc.', 'travel-blocks'),

                                'default_value' => 1,

                                'min' => 1,

                                'max' => 4,

                                'step' => 1,

                                'append' => 'espacios',

                            ],

                            [

                                'key' => 'field_sbs_card_image',

                                'label' => __('Imagen', 'travel-blocks'),

                                'name' => 'image',

                                'type' => 'image',

                                'instructions' => __('Recomendado: 600x400px. Si se deja vacío, usa placeholder.', 'travel-blocks'),

                                'return_format' => 'array',

                                'preview_size' => 'medium',

                                'library' => 'all',

                                'required' => 0,

                            ],

                            [

                                'key' => 'field_sbs_card_title',

                                'label' => __('Título', 'travel-blocks'),

                                'name' => 'title',

                                'type' => 'text',

                                'required' => 1,

                                'default_value' => __('Título de la Card', 'travel-blocks'),

                                'maxlength' => 100,

                            ],

                            [

                                'key' => 'field_sbs_card_excerpt',

                                'label' => __('Descripción', 'travel-blocks'),

                                'name' => 'excerpt',

                                'type' => 'textarea',

                                'required' => 0,

                                'rows' => 3,

                                'maxlength' => 200,

                                'default_value' => __('Descripción breve de la card...', 'travel-blocks'),

                            ],

                            [

                                'key' => 'field_sbs_card_link',

                                'label' => __('Enlace', 'travel-blocks'),

                                'name' => 'link',

                                'type' => 'url',

                                'required' => 0,

                                'placeholder' => 'https://example.com',

                            ],

                            [

                                'key' => 'field_sbs_card_category',

                                'label' => __('Categoría (Badge)', 'travel-blocks'),

                                'name' => 'category',

                                'type' => 'text',

                                'required' => 0,

                                'maxlength' => 30,

                                'placeholder' => __('Ej: Destacado, Nuevo, Promoción', 'travel-blocks'),

                                'instructions' => __('Badge/etiqueta arriba del texto', 'travel-blocks'),

                            ],

                            [

                                'key' => 'field_sbs_card_badge_color',

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

                                'key' => 'field_sbs_card_cta_text',

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

                                'key' => 'field_sbs_card_location',

                                'label' => __('📍 Ubicación', 'travel-blocks'),

                                'name' => 'location',

                                'type' => 'text',

                                'required' => 0,

                                'maxlength' => 50,

                                'placeholder' => __('Ej: Cusco, Perú', 'travel-blocks'),

                                'instructions' => __('Ubicación mostrada debajo de la descripción', 'travel-blocks'),

                            ],

                            [

                                'key' => 'field_sbs_card_price',

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

                    ...ContentQueryHelper::get_filter_fields('sbs'),



                    // ===== SLIDER SETTINGS (Mobile) =====

                    [

                        'key' => 'field_sbs_slider_settings',

                        'label' => __('⚙️ Configuración del Slider (Mobile)', 'travel-blocks'),

                        'type' => 'tab',

                        'placement' => 'top',

                    ],

                    [

                        'key' => 'field_sbs_show_arrows',

                        'label' => __('Mostrar Flechas de Navegación', 'travel-blocks'),

                        'name' => 'show_arrows',

                        'type' => 'true_false',

                        'instructions' => __('Flechas prev/next en slider mobile', 'travel-blocks'),

                        'default_value' => 1,

                        'ui' => 1,

                    ],

                    [

                        'key' => 'field_sbs_show_dots',

                        'label' => __('Mostrar Dots de Paginación', 'travel-blocks'),

                        'name' => 'show_dots',

                        'type' => 'true_false',

                        'instructions' => __('Dots debajo del slider en mobile', 'travel-blocks'),

                        'default_value' => 1,

                        'ui' => 1,

                    ],

                    [

                        'key' => 'field_sbs_autoplay',

                        'label' => __('Autoplay (Mobile)', 'travel-blocks'),

                        'name' => 'autoplay',

                        'type' => 'true_false',

                        'instructions' => __('Avance automático en mobile', 'travel-blocks'),

                        'default_value' => 0,

                        'ui' => 1,

                    ],

                    [

                        'key' => 'field_sbs_autoplay_delay',

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

                                    'field' => 'field_sbs_autoplay',

                                    'operator' => '==',

                                    'value' => '1',

                                ],

                            ],

                        ],

                    ],



                    // ===== DESKTOP GRID SETTINGS =====

                    [

                        'key' => 'field_sbs_grid_settings',

                        'label' => __('🖥️ Configuración del Grid (Desktop)', 'travel-blocks'),

                        'type' => 'tab',

                        'placement' => 'top',

                    ],

                    [

                        'key' => 'field_sbs_grid_columns',

                        'label' => __('📊 Columnas del Grid (Desktop)', 'travel-blocks'),

                        'name' => 'grid_columns',

                        'type' => 'range',

                        'instructions' => __('Número total de columnas del grid. Las cards pueden ocupar 1 o más espacios.', 'travel-blocks'),

                        'default_value' => 3,

                        'min' => 2,

                        'max' => 8,

                        'step' => 1,

                        'append' => 'cols',

                    ],

                    [

                        'key' => 'field_sbs_card_gap',

                        'label' => __('↔️ Espacio entre Cards', 'travel-blocks'),

                        'name' => 'card_gap',

                        'type' => 'range',

                        'instructions' => __('Gap entre cards en el grid', 'travel-blocks'),

                        'default_value' => 32,

                        'min' => 0,

                        'max' => 64,

                        'step' => 4,

                        'append' => 'px',

                    ],

                    [

                        'key' => 'field_sbs_card_min_height',

                        'label' => __('📏 Altura de Cards', 'travel-blocks'),

                        'name' => 'card_min_height',

                        'type' => 'range',

                        'instructions' => __('Altura de las cards.', 'travel-blocks'),

                        'default_value' => 450,

                        'min' => 10,

                        'max' => 800,

                        'step' => 10,

                        'append' => 'px',

                    ],

                    [

                        'key' => 'field_sbs_hover_effect',

                        'label' => __('✨ Efecto Hover (Desktop)', 'travel-blocks'),

                        'name' => 'hover_effect',

                        'type' => 'select',

                        'instructions' => __('Efecto al pasar el mouse sobre una card', 'travel-blocks'),

                        'choices' => [

                            'squeeze' => __('Squeeze - Crece y empuja las demás', 'travel-blocks'),

                            'lift' => __('Lift - Elevar card', 'travel-blocks'),

                            'glow' => __('Glow - Resaltar bordes', 'travel-blocks'),

                            'zoom' => __('Zoom - Agrandar imagen', 'travel-blocks'),

                            'none' => __('Ninguno - Sin efecto', 'travel-blocks'),

                        ],

                        'default_value' => 'squeeze',

                        'ui' => 1,

                    ],



                    // ===== TAB: DESKTOP =====

                    [
                        'key' => 'field_sbs_tab_desktop',
                        'label' => __('🖥️ Desktop', 'travel-blocks'),
                        'type' => 'tab',
                        'placement' => 'top',
                    ],

                    [
                        'key' => 'field_sbs_desktop_message',
                        'label' => '',
                        'type' => 'message',
                        'message' => __('<strong>Configuración para pantallas grandes (≥1025px)</strong><br>Grid de cards lado a lado', 'travel-blocks'),
                    ],

                    [
                        'key' => 'field_sbs_image_position',
                        'label' => __('📐 Posición de la Imagen', 'travel-blocks'),
                        'name' => 'image_position',
                        'type' => 'select',
                        'required' => 0,
                        'choices' => [
                            'left' => __('Izquierda', 'travel-blocks'),
                            'right' => __('Derecha', 'travel-blocks'),
                        ],
                        'default_value' => 'left',
                        'ui' => 1,
                    ],

                    [
                        'key' => 'field_sbs_image_width',
                        'label' => __('📏 Ancho de Imagen (%)', 'travel-blocks'),
                        'name' => 'image_width',
                        'type' => 'range',
                        'instructions' => __('Porcentaje del ancho de la card que ocupa la imagen', 'travel-blocks'),
                        'default_value' => 40,
                        'min' => 30,
                        'max' => 60,
                        'step' => 5,
                        'append' => '%',
                    ],

                    [
                        'key' => 'field_sbs_card_min_height',
                        'label' => __('📐 Altura Mínima de Card (px)', 'travel-blocks'),
                        'name' => 'card_min_height',
                        'type' => 'number',
                        'instructions' => __('Altura mínima de cada card en desktop', 'travel-blocks'),
                        'default_value' => 200,
                        'min' => 150,
                        'max' => 500,
                        'step' => 10,
                        'append' => 'px',
                    ],

                    [
                        'key' => 'field_sbs_image_border_radius',
                        'label' => __('🔘 Radio de Bordes', 'travel-blocks'),
                        'name' => 'image_border_radius',
                        'type' => 'range',
                        'default_value' => 12,
                        'min' => 0,
                        'max' => 40,
                        'step' => 2,
                        'append' => 'px',
                    ],

                    [
                        'key' => 'field_sbs_image_min_width',
                        'label' => __('📏 Ancho Mínimo Imagen (px)', 'travel-blocks'),
                        'name' => 'image_min_width',
                        'type' => 'number',
                        'instructions' => __('Ancho mínimo de la imagen en desktop. 0 = sin mínimo', 'travel-blocks'),
                        'default_value' => 0,
                        'min' => 0,
                        'max' => 400,
                        'step' => 10,
                        'append' => 'px',
                    ],

                    // ===== TAB: MOBILE =====

                    [
                        'key' => 'field_sbs_tab_mobile',
                        'label' => __('📱 Mobile', 'travel-blocks'),
                        'type' => 'tab',
                        'placement' => 'top',
                    ],

                    [
                        'key' => 'field_sbs_mobile_message',
                        'label' => '',
                        'type' => 'message',
                        'message' => __('<strong>Configuración para pantallas pequeñas (≤1024px)</strong><br>Slider horizontal con navegación', 'travel-blocks'),
                    ],

                    [
                        'key' => 'field_sbs_mobile_image_width',
                        'label' => __('📏 Ancho de Imagen Mobile (%)', 'travel-blocks'),
                        'name' => 'mobile_image_width',
                        'type' => 'range',
                        'instructions' => __('Porcentaje del ancho de la card que ocupa la imagen en mobile', 'travel-blocks'),
                        'default_value' => 40,
                        'min' => 30,
                        'max' => 50,
                        'step' => 5,
                        'append' => '%',
                    ],

                    [
                        'key' => 'field_sbs_mobile_card_height',
                        'label' => __('📐 Altura de Card Mobile (px)', 'travel-blocks'),
                        'name' => 'mobile_card_height',
                        'type' => 'number',
                        'instructions' => __('Altura de cada card en mobile', 'travel-blocks'),
                        'default_value' => 180,
                        'min' => 120,
                        'max' => 350,
                        'step' => 10,
                        'append' => 'px',
                    ],

                    [
                        'key' => 'field_sbs_mobile_image_min_width',
                        'label' => __('📏 Ancho Mínimo Imagen (px)', 'travel-blocks'),
                        'name' => 'mobile_image_min_width',
                        'type' => 'number',
                        'instructions' => __('Ancho mínimo de la imagen en mobile. 0 = sin mínimo', 'travel-blocks'),
                        'default_value' => 0,
                        'min' => 0,
                        'max' => 300,
                        'step' => 10,
                        'append' => 'px',
                    ],

                    // ===== TAB: COLORES =====

                    [
                        'key' => 'field_sbs_tab_colors',
                        'label' => __('🎨 Colores', 'travel-blocks'),
                        'type' => 'tab',
                        'placement' => 'top',
                    ],

                    [
                        'key' => 'field_sbs_text_alignment',
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
                    ],

                    [
                        'key' => 'field_sbs_button_alignment',
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
                    ],

                    [
                        'key' => 'field_sbs_button_color_variant',
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
                        ],
                        'default_value' => 'primary',
                        'ui' => 1,
                    ],

                    [
                        'key' => 'field_sbs_badge_color_variant',
                        'label' => __('🏷️ Color de la Etiqueta', 'travel-blocks'),
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
                    ],

                ],

                'location' => [

                    [

                        [

                            'param' => 'block',

                            'operator' => '==',

                            'value' => 'acf/side-by-side-cards',

                        ],

                    ],

                ],

            ]);

        }

    }



    /**

     * Render the block output.

     *

     * Generates horizontal cards with flexible layout:

     * - Detects dynamic content source (packages/posts/deals) or uses manual cards

     * - Applies column-span pattern for dynamic content

     * - Loads demo cards if content is empty (preview mode)

     * - Configures grid, slider, and style settings

     * - Passes data to template for rendering

     *

     * @param array  $block      Block settings and attributes

     * @param string $content    Block content (unused)

     * @param bool   $is_preview Whether block is being previewed in editor

     * @param int    $post_id    Current post ID

     *

     * @return void

     */

    public function render(array $block, string $content = '', bool $is_preview = false, int $post_id = 0): void

    {

        try {

            // Check if using dynamic content

            $dynamic_source = get_field('sbs_dynamic_source');



            if ($dynamic_source === 'package') {

                $cards = ContentQueryHelper::get_content('sbs', 'package');

            } elseif ($dynamic_source === 'post') {

                $cards = ContentQueryHelper::get_content('sbs', 'post');

            } elseif ($dynamic_source === 'deal') {

                // Dynamic content from selected deal's packages

                $deal_id = get_field('sbs_deal_selector');

                if ($deal_id) {

                    $cards = ContentQueryHelper::get_deal_packages($deal_id, 'sbs');

                } else {

                    $cards = [];

                }

            } else {

                $cards = get_field('cards') ?: [];



                // Si no hay cards, mostrar placeholders de ejemplo

                if (empty($cards)) {

                    $cards = $this->get_demo_cards();

                }

            }



            // Apply column_span pattern to dynamic cards

            if ($dynamic_source && $dynamic_source !== 'none') {

                $column_span_pattern = get_field('column_span_pattern') ?: '';

                $cards = $this->apply_column_span_pattern($cards, $column_span_pattern);

            }



            // Get settings - Desktop
            $image_position = get_field('image_position') ?: 'left';

            $image_width_raw = get_field('image_width');
            $image_width = is_numeric($image_width_raw) && $image_width_raw > 0 ? (int)$image_width_raw : 40;
            $image_width = max(30, min(60, $image_width));

            $image_border_radius = get_field('image_border_radius') ?: 12;
            $image_min_width = (int)(get_field('image_min_width') ?: 0);

            // Get settings - Mobile
            $mobile_image_width_raw = get_field('mobile_image_width');
            $mobile_image_width = is_numeric($mobile_image_width_raw) && $mobile_image_width_raw > 0 ? (int)$mobile_image_width_raw : 40;
            $mobile_image_width = max(30, min(50, $mobile_image_width));

            $mobile_card_height = (int)(get_field('mobile_card_height') ?: 180);
            $mobile_image_min_width = (int)(get_field('mobile_image_min_width') ?: 0);

            $button_color_variant = get_field('button_color_variant') ?: 'primary';

            $badge_color_variant = get_field('badge_color_variant') ?: 'secondary';

            $text_alignment = get_field('text_alignment') ?: 'left';

            $button_alignment = get_field('button_alignment') ?: 'left';



            // Slider settings

            $show_arrows = (bool)(get_field('show_arrows') ?? true);

            $show_dots = (bool)(get_field('show_dots') ?? true);

            $autoplay = (bool)(get_field('autoplay') ?? false);

            $autoplay_delay = (float)(get_field('autoplay_delay') ?: 5) * 1000; // convert to ms



            // Favorite button

            $show_favorite = get_field('show_favorite');

            if ($show_favorite === null || $show_favorite === '') {

                $show_favorite = true; // Default to true

            }



            // Grid settings

            $grid_columns = (int)(get_field('grid_columns') ?: 3);

            $card_gap = (int)(get_field('card_gap') ?: 32);

            $card_min_height = (int)(get_field('card_min_height') ?: 450);

            $hover_effect = get_field('hover_effect') ?: 'squeeze';



            // Block attributes

            $block_id = 'sbs-' . ($block['id'] ?? uniqid());

            $align = $block['align'] ?? 'wide';



            // Pass data to template
            $data = [
                'block_id' => $block_id,
                'align' => $align,
                // Desktop settings
                'image_position' => $image_position,
                'image_width' => $image_width,
                'image_border_radius' => $image_border_radius,
                'image_min_width' => $image_min_width,
                'card_min_height' => $card_min_height,
                // Mobile settings
                'mobile_image_width' => $mobile_image_width,
                'mobile_card_height' => $mobile_card_height,
                'mobile_image_min_width' => $mobile_image_min_width,
                // Colors & alignment
                'button_color_variant' => $button_color_variant,
                'badge_color_variant' => $badge_color_variant,
                'text_alignment' => $text_alignment,
                'button_alignment' => $button_alignment,
                // Content
                'cards' => $cards,
                // Slider settings
                'show_arrows' => $show_arrows,
                'show_dots' => $show_dots,
                'autoplay' => $autoplay,
                'autoplay_delay' => $autoplay_delay,
                'show_favorite' => $show_favorite,
                // Grid settings
                'grid_columns' => $grid_columns,
                'card_gap' => $card_gap,
                'hover_effect' => $hover_effect,
                'is_preview' => $is_preview,
                'block' => $block,
            ];



            $this->load_template('side-by-side-cards', $data);



        } catch (\Exception $e) {

            // Error handling

            if (defined('WP_DEBUG') && WP_DEBUG) {

                echo '<div style="padding: 20px; background: #ffebee; border: 2px solid #f44336; border-radius: 4px;">';

                echo '<h3 style="margin: 0 0 10px; color: #c62828;">Error en Side by Side Cards</h3>';

                echo '<p style="margin: 0; font-family: monospace; font-size: 13px;">' . esc_html($e->getMessage()) . '</p>';

                echo '</div>';

            }

        }

    }



    /**

     * Get demo cards with placeholders.

     *

     * Provides sample cards for preview mode when no content is available.

     * Includes varied column_span values to demonstrate grid flexibility.

     *

     * @return array Array of demo card data

     */

    private function get_demo_cards(): array

    {

        return [

            [

                'image' => ['url' => 'https://picsum.photos/600/400?random=1'],

                'title' => 'Machu Picchu Classic Tour',

                'excerpt' => 'Descubre la ciudadela inca más famosa del mundo en este tour guiado de día completo.',

                'link' => '#',

                'category' => 'Destacado',

                'location' => 'Cusco, Perú',

                'price' => '$299',

                'cta_text' => 'Ver detalles',

                'column_span' => 1,

            ],

            [

                'image' => ['url' => 'https://picsum.photos/600/400?random=2'],

                'title' => 'Inca Trail 4 Days',

                'excerpt' => 'Camino del Inca clásico de 4 días hasta Machu Picchu. Una experiencia única.',

                'link' => '#',

                'category' => 'Aventura',

                'location' => 'Cusco, Perú',

                'price' => '$599',

                'cta_text' => 'Reservar ahora',

                'column_span' => 2, // Doble ancho para destacar

            ],

            [

                'image' => ['url' => 'https://picsum.photos/600/400?random=3'],

                'title' => 'Rainbow Mountain',

                'excerpt' => 'Visita la montaña de 7 colores, una maravilla natural de los Andes peruanos.',

                'link' => '#',

                'category' => 'Nuevo',

                'location' => 'Vinicunca, Perú',

                'price' => '$89',

                'cta_text' => 'Descubrir',

                'column_span' => 1,

            ],

        ];

    }



    /**

     * Apply column-span pattern to cards array.

     *

     * Parses a comma-separated pattern (e.g., "1,2,1,1") and applies it

     * cyclically to the cards array. If pattern is empty, defaults to 1.

     *

     * Pattern format: "1,2,1,1" means card 1 = 1 space, card 2 = 2 spaces, etc.

     * Pattern repeats if there are more cards than pattern values.

     *

     * @param array  $cards   Array of card data

     * @param string $pattern Comma-separated column span pattern

     *

     * @return array Cards array with column_span values applied

     */

    private function apply_column_span_pattern(array $cards, string $pattern): array

    {

        // If no pattern, set all cards to column_span = 1

        if (empty($pattern)) {

            foreach ($cards as &$card) {

                if (!isset($card['column_span'])) {

                    $card['column_span'] = 1;

                }

            }

            return $cards;

        }



        // Parse pattern: "1,2,1,1" -> [1, 2, 1, 1]

        $pattern_array = array_map('intval', array_map('trim', explode(',', $pattern)));



        // Filter out invalid values (0 or negative)

        $pattern_array = array_filter($pattern_array, fn($val) => $val > 0);



        // If pattern is empty after filtering, return cards unchanged

        if (empty($pattern_array)) {

            return $cards;

        }



        // Apply pattern cyclically to cards

        foreach ($cards as $index => &$card) {

            $pattern_index = $index % count($pattern_array);

            $card['column_span'] = array_values($pattern_array)[$pattern_index];

        }



        return $cards;

    }

}

