<?php

/**

 * Block: Posts Carousel Unified

 *

 * Unified implementation that consolidates PostsCarousel (Material Design)

 * and PostsCarouselNative, eliminating ~70% code duplication.

 *

 * Architecture:

 * - PostsCarousel.php extends this class with variant='material'

 * - PostsCarouselNative.php extends this class with variant='native'

 * - Both keep their original block names for 100% backward compatibility

 * - Shared logic (70%) lives here

 * - Variant-specific logic (30%) is conditional

 *

 * Benefits:

 * - Eliminates ~800 lines of duplicated code

 * - Single source of truth for bug fixes

 * - Consistent behavior across variants

 * - Easier maintenance and testing

 * - 100% backward compatible (no content migration needed)

 *

 * @package Travel\Blocks\ACF

 * @since 2.0.0

 * @version 2.0.0 - Created: Consolidation of PostsCarousel + PostsCarouselNative

 */



namespace Travel\Blocks\Blocks\ACF;



use Travel\Blocks\Core\BlockBase;

use Travel\Blocks\Helpers\ContentQueryHelper;



abstract class PostsCarouselUnified extends BlockBase

{

    /**

     * Block variant: 'material' or 'native'

     *

     * @var string

     */

    protected string $variant;



    /**

     * Field prefix for ContentQueryHelper

     * - Material: 'pc_mat'

     * - Native: 'pc'

     *

     * @var string

     */

    protected string $field_prefix;



    /**

     * Constructor - Must be called by child classes

     *

     * @param string $variant 'material' or 'native'

     */

    public function __construct(string $variant = 'material')

    {

        $this->variant = $variant;

        $this->field_prefix = $variant === 'material' ? 'pc_mat' : 'pc';



        // Set block name (preserves backward compatibility)

        $this->name = $variant === 'native'

            ? 'acf-gbr-posts-carousel'  // Keep Native's old name

            : 'posts-carousel';          // Keep Material's name



        // Set title based on variant

        $this->title = $variant === 'native'

            ? __('Posts Carousel (Native CSS)', 'travel-blocks')

            : __('Posts Carousel (Material)', 'travel-blocks');



        // Set description based on variant

        $this->description = $variant === 'native'

            ? __('Native CSS scroll-snap carousel with vanilla JavaScript.', 'travel-blocks')

            : __('Grid 3 columnas (desktop) + Slider Material Design (mobile). ACF Repeater para control manual.', 'travel-blocks');



        // Common configuration

        $this->category = 'travel';

        $this->icon     = 'images-alt2';

        $this->keywords = ['posts', 'carousel', 'slider'];

        $this->mode     = 'preview';



        $this->supports = [

            'align'    => ['wide', 'full'],

            'mode'     => true,

            'multiple' => true,

            'anchor'   => true,

        ];

    }



    /**

     * Enqueue block-specific assets.

     * Loads variant-specific CSS and JS.

     *

     * @return void

     */

    public function enqueue_assets(): void

    {

        if ($this->variant === 'native') {

            // Native variant assets

            wp_enqueue_style(

                'acf-gbr-posts-carousel-style',

                TRAVEL_BLOCKS_URL . 'assets/blocks/PostsCarousel/style.css',

                [],

                TRAVEL_BLOCKS_VERSION

            );



            wp_enqueue_script(

                'acf-gbr-posts-carousel-script',

                TRAVEL_BLOCKS_URL . 'assets/blocks/PostsCarousel/carousel.js',

                [],

                TRAVEL_BLOCKS_VERSION,

                true

            );

        } else {

            // Material variant assets

            wp_enqueue_style(

                'posts-carousel-style',

                TRAVEL_BLOCKS_URL . 'assets/blocks/posts-carousel.css',

                [],

                TRAVEL_BLOCKS_VERSION

            );



            wp_enqueue_script(

                'posts-carousel-script',

                TRAVEL_BLOCKS_URL . 'assets/blocks/posts-carousel.js',

                [],

                TRAVEL_BLOCKS_VERSION,

                true

            );

        }

    }



    /**

     * Get default placeholder image URL

     *

     * @return string

     */

    protected function get_placeholder_image(): string

    {

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



        if (!function_exists('acf_add_local_field_group')) {

            return;

        }



        // Build fields array based on variant

        $fields = $this->get_fields_for_variant();



        acf_add_local_field_group([

            'key'    => $this->variant === 'native' ? 'group_posts_carousel' : 'group_block_posts_carousel',

            'title'  => $this->variant === 'native'

                ? __('Posts Carousel - Settings', 'travel-blocks')

                : __('Posts Carousel - Material Design', 'travel-blocks'),

            'fields' => $fields,

            'location' => [

                [

                    [

                        'param'    => 'block',

                        'operator' => '==',

                        'value'    => 'acf/' . $this->name,

                    ],

                ],

            ],

        ]);

    }



    /**

     * Get ACF fields configuration for current variant.

     * This is where we handle the 30% difference between variants.

     *

     * @return array

     */

    protected function get_fields_for_variant(): array

    {

        if ($this->variant === 'native') {

            return $this->get_native_fields();

        }



        return $this->get_material_fields();

    }



    /**

     * Get Native variant fields (simpler configuration).

     *

     * @return array

     */

    protected function get_native_fields(): array

    {

        $dynamic_fields = ContentQueryHelper::get_dynamic_content_fields('pc');

        $filter_fields = ContentQueryHelper::get_filter_fields('pc');



        return array_merge(

            [

                // ===== TAB: GENERAL =====

                [

                    'key'       => 'field_pc_tab_general',

                    'label'     => '⚙️ General',

                    'type'      => 'tab',

                    'placement' => 'top',

                ],

                [

                    'key'           => 'field_pc_posts_per_page',

                    'label'         => __('Posts to Display', 'travel-blocks'),

                    'name'          => 'pc_posts_per_page',

                    'type'          => 'number',

                    'instructions'  => __('Number of posts to show in the carousel', 'travel-blocks'),

                    'required'      => 0,

                    'default_value' => 6,

                    'min'           => 1,

                    'max'           => 20,

                    'step'          => 1,

                ],

                [

                    'key'           => 'field_pc_show_arrows',

                    'label'         => __('Show Navigation Arrows', 'travel-blocks'),

                    'name'          => 'pc_show_arrows',

                    'type'          => 'true_false',

                    'instructions'  => __('Display prev/next navigation arrows', 'travel-blocks'),

                    'default_value' => 1,

                    'ui'            => 1,

                ],

                [

                    'key'           => 'field_pc_show_dots',

                    'label'         => __('Show Pagination Dots', 'travel-blocks'),

                    'name'          => 'pc_show_dots',

                    'type'          => 'true_false',

                    'instructions'  => __('Display pagination dots below carousel', 'travel-blocks'),

                    'default_value' => 1,

                    'ui'            => 1,

                ],

                [

                    'key'           => 'field_pc_autoplay',

                    'label'         => __('Enable Autoplay', 'travel-blocks'),

                    'name'          => 'pc_autoplay',

                    'type'          => 'true_false',

                    'instructions'  => __('Automatically advance slides', 'travel-blocks'),

                    'default_value' => 0,

                    'ui'            => 1,

                ],

                [

                    'key'           => 'field_pc_autoplay_delay',

                    'label'         => __('Autoplay Delay (ms)', 'travel-blocks'),

                    'name'          => 'pc_autoplay_delay',

                    'type'          => 'number',

                    'instructions'  => __('Time between slide transitions in milliseconds', 'travel-blocks'),

                    'required'      => 0,

                    'default_value' => 5000,

                    'min'           => 1000,

                    'max'           => 30000,

                    'step'          => 500,

                    'conditional_logic' => [

                        [

                            [

                                'field'    => 'field_pc_autoplay',

                                'operator' => '==',

                                'value'    => '1',

                            ],

                        ],

                    ],

                ],

            ],

            $dynamic_fields,

            $filter_fields

        );

    }



    /**

     * Get Material variant fields (full featured configuration).

     * This preserves all the advanced options from the original PostsCarousel.

     *

     * @return array

     */

    protected function get_material_fields(): array

    {

        // Material has MUCH more fields - this is the 30% difference

        // Copy from original PostsCarousel.php register() method

        return [

            // ===== TAB: CARD STYLES =====

            [

                'key'       => 'field_pc_tab_styles',

                'label'     => __('🎨 Card Styles', 'travel-blocks'),

                'type'      => 'tab',

                'placement' => 'top',

            ],

            [

                'key'     => 'field_pc_card_style',

                'label'   => __('🎴 Estilo de Card', 'travel-blocks'),

                'name'    => 'card_style',

                'type'    => 'select',

                'required' => 0,

                'choices' => [

                    'overlay'       => __('Overlay - Botón inline con descripción', 'travel-blocks'),

                    'overlay-2'     => __('Overlay 2 - Botón separado abajo', 'travel-blocks'),

                    'vertical'      => __('Vertical - Imagen arriba, texto abajo (card normal)', 'travel-blocks'),

                    'overlay-split' => __('Overlay Split - Badge arriba, título/descripción en medio, meta/botón 50-50 abajo', 'travel-blocks'),

                ],

                'default_value' => 'overlay',

                'ui'            => 1,

                'instructions'  => __('Estilo de diseño de las cards', 'travel-blocks'),

            ],

            [
                'key'           => 'field_pc_description_lines',
                'label'         => __('📝 Líneas de Descripción', 'travel-blocks'),
                'name'          => 'description_lines',
                'type'          => 'number',
                'instructions'  => __('Número de líneas a mostrar en la descripción (por defecto 3).', 'travel-blocks'),
                'required'      => 0,
                'default_value' => 3,
                'min'           => 1,
                'max'           => 10,
                'step'          => 1,
            ],

            [

                'key'     => 'field_pc_button_color_variant',

                'label'   => __('🎨 Color del Botón', 'travel-blocks'),

                'name'    => 'button_color_variant',

                'type'    => 'select',

                'required' => 0,

                'choices' => [

                    'primary'     => __('Primario - Rosa (#E78C85)', 'travel-blocks'),

                    'secondary'   => __('Secundario - Morado (#311A42)', 'travel-blocks'),

                    'white'       => __('Blanco con texto negro', 'travel-blocks'),

                    'gold'        => __('Dorado (#CEA02D)', 'travel-blocks'),

                    'dark'        => __('Negro (#1A1A1A)', 'travel-blocks'),

                    'transparent' => __('Transparente con borde blanco', 'travel-blocks'),

                    'read-more'   => __('Texto "Read More" (sin fondo)', 'travel-blocks'),

                    'line-arrow'  => __('Línea superior + Texto Rosa + Flecha', 'travel-blocks'),

                ],

                'default_value' => 'primary',

                'ui'            => 1,

                'instructions'  => __('Color aplicado a todos los botones del bloque', 'travel-blocks'),

            ],

            [

                'key'     => 'field_pc_badge_color_variant',

                'label'   => __('🎨 Color de la Etiqueta', 'travel-blocks'),

                'name'    => 'badge_color_variant',

                'type'    => 'select',

                'required' => 0,

                'choices' => [

                    'primary'     => __('Primario - Rosa (#E78C85)', 'travel-blocks'),

                    'secondary'   => __('Secundario - Morado (#311A42)', 'travel-blocks'),

                    'white'       => __('Blanco con texto negro', 'travel-blocks'),

                    'gold'        => __('Dorado (#CEA02D)', 'travel-blocks'),

                    'dark'        => __('Negro (#1A1A1A)', 'travel-blocks'),

                    'transparent' => __('Transparente con borde blanco', 'travel-blocks'),

                ],

                'default_value' => 'secondary',

                'ui'            => 1,

                'instructions'  => __('Color aplicado a todas las etiquetas/badges del bloque', 'travel-blocks'),

            ],

            [

                'key'     => 'field_pc_text_alignment',

                'label'   => __('📐 Alineación de Texto', 'travel-blocks'),

                'name'    => 'text_alignment',

                'type'    => 'select',

                'required' => 0,

                'choices' => [

                    'left'   => __('Izquierda', 'travel-blocks'),

                    'center' => __('Centro', 'travel-blocks'),

                    'right'  => __('Derecha', 'travel-blocks'),

                ],

                'default_value' => 'left',

                'ui'            => 1,

                'instructions'  => __('Alineación del texto (título, descripción, ubicación, precio)', 'travel-blocks'),

            ],

            [

                'key'     => 'field_pc_button_alignment',

                'label'   => __('📍 Alineación de Botón', 'travel-blocks'),

                'name'    => 'button_alignment',

                'type'    => 'select',

                'required' => 0,

                'choices' => [

                    'left'   => __('Izquierda', 'travel-blocks'),

                    'center' => __('Centro', 'travel-blocks'),

                    'right'  => __('Derecha', 'travel-blocks'),

                ],

                'default_value' => 'left',

                'ui'            => 1,

                'instructions'  => __('Alineación del botón/CTA', 'travel-blocks'),

            ],

            [

                'key'           => 'field_pc_show_favorite',

                'label'         => __('❤️ Mostrar Botón Favoritos', 'travel-blocks'),

                'name'          => 'show_favorite',

                'type'          => 'true_false',

                'required'      => 0,

                'default_value' => 1,

                'ui'            => 1,

                'instructions'  => __('Mostrar botón de corazón en la esquina superior derecha', 'travel-blocks'),

            ],



            // ===== DYNAMIC CONTENT FIELDS =====

            ...ContentQueryHelper::get_dynamic_content_fields('pc_mat'),



            // ===== TAB: CARDS (Manual mode) =====

            [

                'key'       => 'field_pc_tab_cards',

                'label'     => __('🃏 Cards', 'travel-blocks'),

                'type'      => 'tab',

                'placement' => 'top',

                'conditional_logic' => [

                    [

                        [

                            'field'    => 'field_pc_mat_dynamic_source',

                            'operator' => '==',

                            'value'    => 'none',

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

                'key' => 'field_pc_card_height_desktop',

                'label' => __('Altura de Cards (Desktop)', 'travel-blocks'),

                'name' => 'card_height_desktop',

                'type' => 'range',

                'instructions' => __('Altura mínima de las cards en vista desktop/grid', 'travel-blocks'),

                'default_value' => 450,

                'min' => 200,

                'max' => 700,

                'step' => 10,

                'append' => 'px',

            ],

            [

                'key' => 'field_pc_card_height',

                'label' => __('Altura de Cards (Mobile)', 'travel-blocks'),

                'name' => 'card_height',

                'type' => 'range',

                'instructions' => __('Altura mínima de las cards en el slider mobile', 'travel-blocks'),

                'default_value' => 450,

                'min' => 200,

                'max' => 700,

                'step' => 10,

                'append' => 'px',

            ],

        ];

    }



    /**

     * Render block content.

     * This is the shared 70% logic that works for both variants.

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

            // Get cards (shared logic)

            $cards = $this->get_cards_data();



            // Get settings (variant-specific)

            $settings = $this->get_settings_for_variant();



            // Prepare data for template

            $data = array_merge(

                [

                    'block_id'   => $this->get_block_id($block),

                    'align'      => $block['align'] ?? 'wide',

                    'cards'      => $cards,

                    'is_preview' => $is_preview,

                    'block'      => $block,

                    'variant'    => $this->variant,

                ],

                $settings

            );



            // Load variant-specific template

            $this->load_template_for_variant($data);



        } catch (\Exception $e) {

            $this->render_error($e);

        }

    }



    /**

     * Get cards data (shared logic - the 70%)

     *

     * @return array

     */

    protected function get_cards_data(): array

    {

        $dynamic_source = get_field($this->field_prefix . '_dynamic_source');



        if ($dynamic_source === 'package') {

            return ContentQueryHelper::get_content($this->field_prefix, 'package');

        }



        if ($dynamic_source === 'post') {

            return ContentQueryHelper::get_content($this->field_prefix, 'post');

        }



        if ($dynamic_source === 'deal') {

            $deal_id = get_field($this->field_prefix . '_deal_selector');

            if ($deal_id) {

                return ContentQueryHelper::get_deal_packages($deal_id, $this->field_prefix);

            }

            return [];

        }



        // Manual cards

        $cards = get_field('cards') ?: [];



        if (empty($cards)) {

            return $this->get_demo_cards();

        }



        return $cards;

    }



    /**

     * Get settings specific to variant (the 30% difference)

     *

     * @return array

     */

    protected function get_settings_for_variant(): array

    {

        if ($this->variant === 'native') {

            return $this->get_native_settings();

        }



        return $this->get_material_settings();

    }



    /**

     * Get Native variant settings (simple)

     *

     * @return array

     */

    protected function get_native_settings(): array

    {

        return [

            'posts_per_page' => (int)(get_field('pc_posts_per_page') ?: 6),

            'show_arrows'    => (bool)(get_field('pc_show_arrows') ?? true),

            'show_dots'      => (bool)(get_field('pc_show_dots') ?? true),

            'autoplay'       => (bool)(get_field('pc_autoplay') ?? false),

            'autoplay_delay' => (int)(get_field('pc_autoplay_delay') ?: 5000),

        ];

    }



    /**

     * Get Material variant settings (full featured)

     *

     * @return array

     */

    protected function get_material_settings(): array

    {

        // All the Material-specific settings

        return [

            'card_style'            => get_field('card_style') ?: 'overlay',

            'description_lines'     => get_field('description_lines') ?: 3,

            'button_color_variant'  => get_field('button_color_variant') ?: 'primary',

            'badge_color_variant'   => get_field('badge_color_variant') ?: 'secondary',

            'text_alignment'        => get_field('text_alignment') ?: 'left',

            'button_alignment'      => get_field('button_alignment') ?: 'left',

            'show_favorite'         => get_field('show_favorite') ?? true,

            'show_arrows'           => (bool)(get_field('show_arrows') ?? true),

            'arrows_position'       => get_field('arrows_position') ?: 'sides',

            'show_dots'             => (bool)(get_field('show_dots') ?? true),

            'autoplay'              => (bool)(get_field('autoplay') ?? false),

            'autoplay_delay'        => (float)(get_field('autoplay_delay') ?: 5) * 1000,

            'slider_speed'          => (float)(get_field('slider_speed') ?: 0.4),

            'hover_effect'          => get_field('hover_effect') ?: 'squeeze',

            'card_gap'              => (int)(get_field('card_gap') ?: 24),

            'desktop_columns'       => (int)(get_field('desktop_columns') ?: 3),

            'tablet_columns'        => (int)(get_field('tablet_columns') ?: 2),

            'card_height_desktop'   => (int)(get_field('card_height_desktop') ?: 450),

            'card_height'           => (int)(get_field('card_height') ?: 450),

            'display_fields_packages' => get_field('pc_mat_dynamic_visible_fields') ?: [],

            'display_fields_posts'  => get_field('pc_mat_dynamic_visible_fields') ?: [],

        ];

    }



    /**

     * Load template specific to variant

     *

     * @param array $data

     * @return void

     */

    protected function load_template_for_variant(array $data): void

    {

        if ($this->variant === 'native') {

            // Native uses direct include (preserves old behavior)

            $template = TRAVEL_BLOCKS_PATH . 'src/Blocks/PostsCarousel/templates/editorial-carousel.php';

            if (file_exists($template)) {

                // Make block_wrapper_attributes available (Native compatibility)

                $GLOBALS['block_wrapper_attributes'] = get_block_wrapper_attributes([

                    'class' => 'posts-carousel-wrapper'

                ]);



                // Extract data for template

                extract($data);

                include $template;

            }

        } else {

            // Material uses BlockBase's load_template

            $this->load_template('posts-carousel', $data);

        }

    }



    /**

     * Get block ID

     *

     * @param array $block

     * @return string

     */

    protected function get_block_id(array $block): string

    {

        return 'pc-' . ($block['id'] ?? uniqid());

    }



    /**

     * Render error message

     *

     * @param \Exception $e

     * @return void

     */

    protected function render_error(\Exception $e): void

    {

        if (function_exists('travel_error')) {

            travel_error('Error en PostsCarousel render', [

                'variant' => $this->variant,

                'message' => $e->getMessage(),

                'file'    => $e->getFile(),

                'line'    => $e->getLine(),

            ]);

        }



        if (defined('WP_DEBUG') && WP_DEBUG) {

            echo '<div style="padding: 20px; background: #ffebee; border: 2px solid #f44336; border-radius: 4px;">';

            echo '<h3 style="margin: 0 0 10px; color: #c62828;">Error en Posts Carousel (' . esc_html($this->variant) . ')</h3>';

            echo '<p style="margin: 0; font-family: monospace; font-size: 13px;">' . esc_html($e->getMessage()) . '</p>';

            echo '</div>';

        }

    }



    /**

     * Get demo cards from JSON file.

     *

     * @return array

     */

    protected function get_demo_cards(): array

    {

        $json_file = TRAVEL_BLOCKS_PATH . 'data/demo/posts-carousel-cards.json';



        if (!file_exists($json_file)) {

            return [

                [

                    'image'    => ['url' => $this->get_placeholder_image()],

                    'title'    => 'Demo Card',

                    'excerpt'  => 'Demo content',

                    'link'     => '#',

                    'cta_text' => 'Ver más'

                ]

            ];

        }



        $json_content = file_get_contents($json_file);

        $cards = json_decode($json_content, true);



        return is_array($cards) ? $cards : [];

    }

}

