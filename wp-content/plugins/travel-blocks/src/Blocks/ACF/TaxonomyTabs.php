<?php

namespace Travel\Blocks\Blocks\ACF;

use Travel\Blocks\Helpers\ContentQueryHelper;

class TaxonomyTabs
{
    private string $name = 'travel-taxonomy-tabs';

    public function __construct()
    {
        // Los métodos se llaman directamente desde Plugin.php

        // Add ACF filters to load checkbox choices dynamically
        add_filter('acf/load_field/name=tt_selected_terms_package_type', [$this, 'load_package_type_choices']);
        add_filter('acf/load_field/name=tt_selected_terms_interest', [$this, 'load_interest_choices']);
        add_filter('acf/load_field/name=tt_selected_locations_cpt', [$this, 'load_locations_cpt_choices']);
        add_filter('acf/load_field/name=tt_selected_terms_category', [$this, 'load_category_choices']);
        add_filter('acf/load_field/name=tt_selected_terms_post_tag', [$this, 'load_post_tag_choices']);

        // Add filter for repeater term_id field to load only selected terms
        add_filter('acf/load_field/key=field_tt_override_term_id', [$this, 'load_selected_terms_for_override']);
    }

    public function register()
    {
        // Enqueue assets for both frontend and editor
        add_action('enqueue_block_assets', [$this, 'enqueue_assets']);
        add_action('enqueue_block_editor_assets', [$this, 'enqueue_assets']);

        $this->register_block();
        $this->register_fields();
    }

    /**
     * Registro del bloque Gutenberg
     */
    public function register_block(): void
    {
        acf_register_block_type([
            'name'            => $this->name,
            'title'           => __('Taxonomy Tabs', 'travel-blocks'),
            'description'     => __('Organiza cards por taxonomías en tabs. Soporta Packages, Blog Posts y Deals.', 'travel-blocks'),
            'category'        => 'travel',
            'icon'            => 'tagcloud',
            'keywords'        => ['tabs', 'taxonomy', 'categories', 'packages', 'cards'],
            'render_callback' => [$this, 'render'],
            'enqueue_assets'  => [$this, 'enqueue_assets'],
            'mode'            => 'preview',
            'api_version'     => 2,
            'supports'        => [
                'align' => ['wide', 'full'],
                'mode' => true,
                'jsx' => true,
                'spacing' => [
                    'margin' => true,
                    'padding' => true,
                ],
                'color' => [
                    'background' => true,
                    'text' => true,
                ],
                'anchor' => true,
                'customClassName' => true,
            ],
        ]);
    }

    /**
     * Registro de ACF Fields
     */
    public function register_fields(): void
    {
        if (!function_exists('acf_add_local_field_group')) {
            return;
        }

        // Get dynamic content fields from ContentQueryHelper (without source selector, we'll add it manually)
        $dynamic_fields = ContentQueryHelper::get_dynamic_content_fields('tt');
        $filter_fields = ContentQueryHelper::get_filter_fields('tt');

        // Build complete fields array
        $fields = array_merge(
            [
                // ===== TAB: GENERAL =====
                [
                    'key' => 'field_tt_tab_general',
                    'label' => '⚙️ General',
                    'type' => 'tab',
                    'placement' => 'top',
                ],
                [
                    'key' => 'field_tt_dynamic_source',
                    'label' => '📦 Fuente de Contenido',
                    'name' => 'tt_dynamic_source',
                    'type' => 'select',
                    'instructions' => 'Selecciona el tipo de contenido para organizar en tabs',
                    'required' => 1,
                    'choices' => [
                        'package' => 'Packages',
                        'post' => 'Blog Posts',
                        'deal' => 'Deals',
                    ],
                    'default_value' => 'package',
                    'ui' => 1,
                    'return_format' => 'value',
                ],
            ],
            [
                // ===== TAB: TAXONOMÍAS =====
                [
                    'key' => 'field_tt_tab_taxonomies',
                    'label' => '🏷️ Taxonomías',
                    'type' => 'tab',
                    'placement' => 'top',
                ],
                [
                    'key' => 'field_tt_instructions',
                    'label' => '',
                    'type' => 'message',
                    'message' => 'Selecciona taxonomías completas (crean un tab con TODOS los términos) o términos individuales. Puedes mezclar ambos.',
                ],
                // Checkbox para seleccionar taxonomías completas (Package source)
                [
                    'key' => 'field_tt_selected_taxonomies_package',
                    'label' => 'Taxonomías Completas (Package)',
                    'name' => 'tt_selected_taxonomies_package',
                    'type' => 'checkbox',
                    'instructions' => 'Marca las taxonomías completas que aparecerán como UN tab cada una',
                    'choices' => [
                        'package_type' => 'Package Types (todos)',
                        'interest' => 'Interests (todos)',
                        'locations_cpt' => 'Locations (todas)',
                    ],
                    'default_value' => [],
                    'layout' => 'vertical',
                    'return_format' => 'value',
                    'conditional_logic' => [
                        [
                            [
                                'field' => 'field_tt_dynamic_source',
                                'operator' => '==',
                                'value' => 'package',
                            ],
                        ],
                    ],
                ],
                // Checkbox para términos individuales de Package Type
                [
                    'key' => 'field_tt_selected_terms_package_type',
                    'label' => 'Package Types (individuales)',
                    'name' => 'tt_selected_terms_package_type',
                    'type' => 'checkbox',
                    'instructions' => 'Marca términos individuales que aparecerán como tabs separados',
                    'choices' => [],
                    'default_value' => [],
                    'layout' => 'vertical',
                    'return_format' => 'value',
                    'conditional_logic' => [
                        [
                            [
                                'field' => 'field_tt_dynamic_source',
                                'operator' => '==',
                                'value' => 'package',
                            ],
                        ],
                    ],
                ],
                // Checkbox para términos individuales de Interests
                [
                    'key' => 'field_tt_selected_terms_interest',
                    'label' => 'Interests (individuales)',
                    'name' => 'tt_selected_terms_interest',
                    'type' => 'checkbox',
                    'instructions' => 'Marca términos individuales que aparecerán como tabs separados',
                    'choices' => [],
                    'default_value' => [],
                    'layout' => 'vertical',
                    'return_format' => 'value',
                    'conditional_logic' => [
                        [
                            [
                                'field' => 'field_tt_dynamic_source',
                                'operator' => '==',
                                'value' => 'package',
                            ],
                        ],
                    ],
                ],
                // Checkbox para Locations individuales (CPT)
                [
                    'key' => 'field_tt_selected_locations_cpt',
                    'label' => 'Locations (individuales)',
                    'name' => 'tt_selected_locations_cpt',
                    'type' => 'checkbox',
                    'instructions' => 'Marca locations individuales que aparecerán como tabs separados',
                    'choices' => [],
                    'default_value' => [],
                    'layout' => 'vertical',
                    'return_format' => 'value',
                    'conditional_logic' => [
                        [
                            [
                                'field' => 'field_tt_dynamic_source',
                                'operator' => '==',
                                'value' => 'package',
                            ],
                        ],
                    ],
                ],
                // Checkbox para seleccionar taxonomías completas (Post source)
                [
                    'key' => 'field_tt_selected_taxonomies_post',
                    'label' => 'Taxonomías Completas (Post)',
                    'name' => 'tt_selected_taxonomies_post',
                    'type' => 'checkbox',
                    'instructions' => 'Marca las taxonomías completas que aparecerán como UN tab cada una',
                    'choices' => [
                        'category' => 'Categories (todas)',
                        'post_tag' => 'Tags (todos)',
                    ],
                    'default_value' => [],
                    'layout' => 'vertical',
                    'return_format' => 'value',
                    'conditional_logic' => [
                        [
                            [
                                'field' => 'field_tt_dynamic_source',
                                'operator' => '==',
                                'value' => 'post',
                            ],
                        ],
                    ],
                ],
                // Checkbox para términos individuales de Categories
                [
                    'key' => 'field_tt_selected_terms_category',
                    'label' => 'Categories (individuales)',
                    'name' => 'tt_selected_terms_category',
                    'type' => 'checkbox',
                    'instructions' => 'Marca términos individuales que aparecerán como tabs separados',
                    'choices' => [],
                    'default_value' => [],
                    'layout' => 'vertical',
                    'return_format' => 'value',
                    'conditional_logic' => [
                        [
                            [
                                'field' => 'field_tt_dynamic_source',
                                'operator' => '==',
                                'value' => 'post',
                            ],
                        ],
                    ],
                ],
                // Checkbox para términos individuales de Post Tags
                [
                    'key' => 'field_tt_selected_terms_post_tag',
                    'label' => 'Post Tags (individuales)',
                    'name' => 'tt_selected_terms_post_tag',
                    'type' => 'checkbox',
                    'instructions' => 'Marca términos individuales que aparecerán como tabs separados',
                    'choices' => [],
                    'default_value' => [],
                    'layout' => 'vertical',
                    'return_format' => 'value',
                    'conditional_logic' => [
                        [
                            [
                                'field' => 'field_tt_dynamic_source',
                                'operator' => '==',
                                'value' => 'post',
                            ],
                        ],
                    ],
                ],
                [
                    'key' => 'field_tt_tab_overrides',
                    'label' => 'Personalizar Tabs',
                    'name' => 'tt_tab_overrides',
                    'type' => 'repeater',
                    'instructions' => 'Opcional: Personaliza el nombre y/o ícono de los tabs. Deja vacío para usar los valores por defecto.',
                    'layout' => 'row',
                    'button_label' => 'Agregar Personalización',
                    'sub_fields' => [
                        [
                            'key' => 'field_tt_override_term_id',
                            'label' => 'Término',
                            'name' => 'term_id',
                            'type' => 'select',
                            'instructions' => 'Selecciona el término/location que quieres personalizar (solo muestra los que seleccionaste arriba)',
                            'choices' => [],
                            'default_value' => '',
                            'allow_null' => 0,
                            'ui' => 1,
                            'return_format' => 'value',
                        ],
                        [
                            'key' => 'field_tt_override_custom_name',
                            'label' => 'Nombre Personalizado',
                            'name' => 'custom_name',
                            'type' => 'text',
                            'placeholder' => 'Ej: Aventuras',
                        ],
                        [
                            'key' => 'field_tt_override_icon',
                            'label' => 'Ícono',
                            'name' => 'icon',
                            'type' => 'image',
                            'instructions' => 'Sube un ícono (SVG, PNG, etc.) para mostrar junto al nombre del tab',
                            'return_format' => 'array',
                            'preview_size' => 'thumbnail',
                            'library' => 'all',
                            'mime_types' => 'svg,png,jpg,jpeg,gif',
                        ],
                    ],
                ],
                [
                    'key' => 'field_tt_preview_mode',
                    'label' => 'Modo Vista Previa',
                    'name' => 'tt_preview_mode',
                    'type' => 'true_false',
                    'instructions' => 'Activar para mostrar datos de ejemplo en el editor',
                    'default_value' => 0,
                    'ui' => 1,
                ],
            ],
            // Dynamic content fields (filters and visible fields)
            $filter_fields,
            [
                // ===== TAB: APARIENCIA =====
                [
                    'key' => 'field_tt_tab_appearance',
                    'label' => '🎨 Apariencia',
                    'type' => 'tab',
                    'placement' => 'top',
                ],
                [
                    'key' => 'field_tt_tabs_style',
                    'label' => 'Estilo de Tabs',
                    'name' => 'tt_tabs_style',
                    'type' => 'select',
                    'choices' => [
                        'pills' => 'Pills',
                        'underline' => 'Underline',
                        'buttons' => 'Buttons',
                        'hero-overlap' => 'Hero Overlap',
                    ],
                    'default_value' => 'pills',
                    'ui' => 1,
                ],
                [
                    'key' => 'field_tt_tabs_alignment',
                    'label' => 'Alineación de Tabs',
                    'name' => 'tt_tabs_alignment',
                    'type' => 'select',
                    'choices' => [
                        'left' => 'Izquierda',
                        'center' => 'Centro',
                        'right' => 'Derecha',
                    ],
                    'default_value' => 'center',
                    'ui' => 1,
                ],
                [
                    'key' => 'field_tt_cards_per_row',
                    'label' => 'Cards por Fila (Desktop)',
                    'name' => 'tt_cards_per_row',
                    'type' => 'range',
                    'default_value' => 3,
                    'min' => 2,
                    'max' => 4,
                    'step' => 1,
                    'append' => 'cards',
                ],
                [
                    'key' => 'field_tt_card_gap',
                    'label' => 'Espacio entre Cards',
                    'name' => 'tt_card_gap',
                    'type' => 'range',
                    'default_value' => 24,
                    'min' => 0,
                    'max' => 64,
                    'step' => 4,
                    'append' => 'px',
                ],
                [
                    'key' => 'field_tt_button_color_variant',
                    'label' => 'Color de Botones',
                    'name' => 'tt_button_color_variant',
                    'type' => 'select',
                    'choices' => [
                        'primary' => 'Primary',
                        'secondary' => 'Secondary',
                        'accent' => 'Accent',
                    ],
                    'default_value' => 'primary',
                    'ui' => 1,
                ],
                [
                    'key' => 'field_tt_badge_color_variant',
                    'label' => 'Color de Badges',
                    'name' => 'tt_badge_color_variant',
                    'type' => 'select',
                    'choices' => [
                        'primary' => 'Primary',
                        'secondary' => 'Secondary',
                        'accent' => 'Accent',
                    ],
                    'default_value' => 'secondary',
                    'ui' => 1,
                ],

                // ===== SLIDER MOBILE SETTINGS =====
                [
                    'key' => 'field_tt_slider_settings',
                    'label' => __('⚙️ Configuración del Slider (Mobile)', 'travel-blocks'),
                    'type' => 'tab',
                    'placement' => 'top',
                ],
                [
                    'key' => 'field_tt_card_height',
                    'label' => __('Altura de Cards (Mobile)', 'travel-blocks'),
                    'name' => 'tt_card_height',
                    'type' => 'range',
                    'instructions' => __('Altura de las cards en el slider mobile', 'travel-blocks'),
                    'default_value' => 450,
                    'min' => 300,
                    'max' => 700,
                    'step' => 10,
                    'append' => 'px',
                ],
                [
                    'key' => 'field_tt_show_arrows',
                    'label' => __('Mostrar Flechas de Navegación', 'travel-blocks'),
                    'name' => 'tt_show_arrows',
                    'type' => 'true_false',
                    'instructions' => __('Flechas prev/next en slider mobile', 'travel-blocks'),
                    'default_value' => 1,
                    'ui' => 1,
                ],
                [
                    'key' => 'field_tt_arrows_position',
                    'label' => __('Posición de las Flechas (Mobile)', 'travel-blocks'),
                    'name' => 'tt_arrows_position',
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
                                'field' => 'field_tt_show_arrows',
                                'operator' => '==',
                                'value' => '1',
                            ],
                        ],
                    ],
                ],
                [
                    'key' => 'field_tt_show_dots',
                    'label' => __('Mostrar Dots de Paginación', 'travel-blocks'),
                    'name' => 'tt_show_dots',
                    'type' => 'true_false',
                    'instructions' => __('Dots debajo del slider en mobile', 'travel-blocks'),
                    'default_value' => 1,
                    'ui' => 1,
                ],
                [
                    'key' => 'field_tt_autoplay',
                    'label' => __('Autoplay', 'travel-blocks'),
                    'name' => 'tt_autoplay',
                    'type' => 'true_false',
                    'instructions' => __('Reproducción automática del slider en mobile', 'travel-blocks'),
                    'default_value' => 0,
                    'ui' => 1,
                ],
                [
                    'key' => 'field_tt_autoplay_delay',
                    'label' => __('Delay de Autoplay', 'travel-blocks'),
                    'name' => 'tt_autoplay_delay',
                    'type' => 'number',
                    'instructions' => __('Segundos entre cada slide', 'travel-blocks'),
                    'default_value' => 5,
                    'min' => 2,
                    'max' => 10,
                    'step' => 0.5,
                    'append' => 'segundos',
                    'conditional_logic' => [
                        [
                            [
                                'field' => 'field_tt_autoplay',
                                'operator' => '==',
                                'value' => '1',
                            ],
                        ],
                    ],
                ],
                [
                    'key' => 'field_tt_slider_speed',
                    'label' => __('Velocidad de Transición', 'travel-blocks'),
                    'name' => 'tt_slider_speed',
                    'type' => 'number',
                    'instructions' => __('Duración de la animación entre slides', 'travel-blocks'),
                    'default_value' => 0.4,
                    'min' => 0.2,
                    'max' => 2,
                    'step' => 0.1,
                    'append' => 'segundos',
                ],
            ]
        );

        acf_add_local_field_group([
            'key' => 'group_taxonomy_tabs',
            'title' => __('Taxonomy Tabs - Settings', 'travel-blocks'),
            'fields' => $fields,
            'location' => [
                [
                    [
                        'param' => 'block',
                        'operator' => '==',
                        'value' => 'acf/' . $this->name,
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

    /**
     * Enqueue assets
     */
    public function enqueue_assets(): void
    {
        // CSS
        wp_enqueue_style(
            "{$this->name}-style",
            TRAVEL_BLOCKS_URL . 'assets/blocks/taxonomy-tabs.css',
            [],
            TRAVEL_BLOCKS_VERSION
        );

        // JavaScript (frontend)
        wp_enqueue_script(
            "{$this->name}-script",
            TRAVEL_BLOCKS_URL . 'assets/blocks/taxonomy-tabs.js',
            [],
            TRAVEL_BLOCKS_VERSION,
            true
        );

        // JavaScript (editor only) - Filter repeater select
        if (is_admin()) {
            wp_enqueue_script(
                "{$this->name}-editor-script",
                TRAVEL_BLOCKS_URL . 'assets/blocks/taxonomy-tabs-editor.js',
                ['jquery', 'acf-input'],
                TRAVEL_BLOCKS_VERSION,
                true
            );
        }
    }

    /**
     * Render callback
     */
    public function render($block, $content = '', $is_preview = false, $post_id = 0): void
    {
        // IMPORTANTE: En Gutenberg, ACF pasa los datos del bloque en $block['data']
        // Intentamos obtener de ahí primero, si no existe, usamos get_field()
        $block_data = $block['data'] ?? [];

        // Get tabs style early to add conditional class to wrapper
        $tabs_style = $block_data['tt_tabs_style'] ?? get_field('tt_tabs_style') ?: 'pills';

        // Build wrapper classes
        $wrapper_classes = ['taxonomy-tabs-wrapper'];
        if ($tabs_style === 'hero-overlap') {
            $wrapper_classes[] = 'taxonomy-tabs-wrapper--hero-overlap';
        }

        // Get block attributes
        $block_wrapper_attributes = get_block_wrapper_attributes([
            'class' => implode(' ', $wrapper_classes)
        ]);

        // ACF fields - try block data first, then get_field as fallback
        $dynamic_source = $block_data['tt_dynamic_source'] ?? get_field('tt_dynamic_source') ?: 'package';

        // Collect selected taxonomies (complete) and individual terms
        $selected_taxonomies = [];  // Full taxonomies: ['package_type', 'interest']
        $selected_terms = [];        // Individual term IDs
        $selected_locations = [];    // Individual location IDs

        // For Package source
        if ($dynamic_source === 'package') {
            // Get complete taxonomies
            $tax_package = $block_data['tt_selected_taxonomies_package'] ?? get_field('tt_selected_taxonomies_package') ?: [];
            if (!empty($tax_package) && is_array($tax_package)) {
                $selected_taxonomies = array_merge($selected_taxonomies, $tax_package);
            }

            // Get individual terms
            $terms_package_type = $block_data['tt_selected_terms_package_type'] ?? get_field('tt_selected_terms_package_type') ?: [];
            if (!empty($terms_package_type) && is_array($terms_package_type)) {
                $selected_terms = array_merge($selected_terms, $terms_package_type);
            }

            $terms_interest = $block_data['tt_selected_terms_interest'] ?? get_field('tt_selected_terms_interest') ?: [];
            if (!empty($terms_interest) && is_array($terms_interest)) {
                $selected_terms = array_merge($selected_terms, $terms_interest);
            }

            // Get individual locations
            $selected_locations = $block_data['tt_selected_locations_cpt'] ?? get_field('tt_selected_locations_cpt') ?: [];
        }

        // For Post source
        if ($dynamic_source === 'post') {
            // Get complete taxonomies
            $tax_post = $block_data['tt_selected_taxonomies_post'] ?? get_field('tt_selected_taxonomies_post') ?: [];
            if (!empty($tax_post) && is_array($tax_post)) {
                $selected_taxonomies = array_merge($selected_taxonomies, $tax_post);
            }

            // Get individual terms
            $terms_category = $block_data['tt_selected_terms_category'] ?? get_field('tt_selected_terms_category') ?: [];
            if (!empty($terms_category) && is_array($terms_category)) {
                $selected_terms = array_merge($selected_terms, $terms_category);
            }

            $terms_post_tag = $block_data['tt_selected_terms_post_tag'] ?? get_field('tt_selected_terms_post_tag') ?: [];
            if (!empty($terms_post_tag) && is_array($terms_post_tag)) {
                $selected_terms = array_merge($selected_terms, $terms_post_tag);
            }
        }

        // Reconstruct repeater data from flattened block data
        $tab_overrides = [];
        if (!empty($block_data) && isset($block_data['tt_tab_overrides'])) {
            // Block data is present - reconstruct from flattened format
            $tab_overrides = $this->reconstruct_repeater_from_block_data(
                $block_data,
                'tt_tab_overrides',
                ['term_id', 'custom_name', 'icon']
            );
        } else {
            // Fallback to get_field (for non-Gutenberg contexts)
            $tab_overrides = get_field('tt_tab_overrides') ?: [];
        }

        $preview_mode = $block_data['tt_preview_mode'] ?? get_field('tt_preview_mode') ?: false;

        // Appearance - use block data first ($tabs_style already obtained above for wrapper class)
        $tabs_alignment = $block_data['tt_tabs_alignment'] ?? get_field('tt_tabs_alignment') ?: 'center';
        $cards_per_row = $block_data['tt_cards_per_row'] ?? get_field('tt_cards_per_row') ?: 3;
        $card_gap = $block_data['tt_card_gap'] ?? get_field('tt_card_gap') ?: 24;
        $button_color_variant = $block_data['tt_button_color_variant'] ?? get_field('tt_button_color_variant') ?: 'primary';
        $badge_color_variant = $block_data['tt_badge_color_variant'] ?? get_field('tt_badge_color_variant') ?: 'secondary';

        // Slider settings (mobile)
        $card_height = $block_data['tt_card_height'] ?? get_field('tt_card_height') ?: 450;
        $show_arrows = (bool)($block_data['tt_show_arrows'] ?? get_field('tt_show_arrows') ?? true);
        $arrows_position = $block_data['tt_arrows_position'] ?? get_field('tt_arrows_position') ?: 'sides';
        $show_dots = (bool)($block_data['tt_show_dots'] ?? get_field('tt_show_dots') ?? true);
        $autoplay = (bool)($block_data['tt_autoplay'] ?? get_field('tt_autoplay') ?? false);
        $autoplay_delay = (float)($block_data['tt_autoplay_delay'] ?? get_field('tt_autoplay_delay') ?: 5) * 1000; // convert to ms
        $slider_speed = (float)($block_data['tt_slider_speed'] ?? get_field('tt_slider_speed') ?: 0.4);

        // Block ID
        $block_id = 'tt-' . ($block['id'] ?? uniqid());
        $align = $block['align'] ?? 'wide';

        // Build tabs data
        $tabs = [];

        if ($preview_mode) {
            // Preview mode: Generate sample data (only when preview_mode field is enabled)
            $tabs = $this->get_preview_tabs($dynamic_source, 'package_type');
        } else {
            // Real mode: Process complete taxonomies first, then individual terms

            // 1. Process complete taxonomies (create ONE tab per taxonomy)
            foreach ($selected_taxonomies as $taxonomy_slug) {
                // Special handling for locations_cpt (it's not a taxonomy)
                if ($taxonomy_slug === 'locations_cpt') {
                    $tab_name = 'Locations';
                    $tab_icon = null;
                    $tab_slug = 'locations';

                    // Check for overrides
                    if (!empty($tab_overrides)) {
                        foreach ($tab_overrides as $override) {
                            if (isset($override['term_id']) && $override['term_id'] == 'locations_cpt') {
                                if (!empty($override['custom_name'])) {
                                    $tab_name = $override['custom_name'];
                                }
                                if (!empty($override['icon'])) {
                                    $tab_icon = $this->prepare_icon_data($override['icon']);
                                }
                                break;
                            }
                        }
                    }

                    // Get cards for ALL locations
                    $cards = $this->get_cards_for_taxonomy('locations_cpt', $dynamic_source);

                    $tabs[] = [
                        'id' => 'locations_cpt',
                        'name' => $tab_name,
                        'slug' => $tab_slug,
                        'icon' => $tab_icon,
                        'cards' => $cards,
                    ];
                    continue;
                }

                // Get taxonomy object to get the label
                $taxonomy_obj = get_taxonomy($taxonomy_slug);
                if (!$taxonomy_obj) {
                    continue;
                }

                $tab_name = $taxonomy_obj->label; // e.g., "Package Types", "Interests"
                $tab_icon = null;
                $tab_slug = $taxonomy_slug;

                // Check for overrides using taxonomy slug as identifier
                if (!empty($tab_overrides)) {
                    foreach ($tab_overrides as $override) {
                        if (isset($override['term_id']) && $override['term_id'] == $taxonomy_slug) {
                            if (!empty($override['custom_name'])) {
                                $tab_name = $override['custom_name'];
                            }
                            if (!empty($override['icon'])) {
                                $tab_icon = $this->prepare_icon_data($override['icon']);
                            }
                            break;
                        }
                    }
                }

                // Get cards for ALL terms in this taxonomy
                $cards = $this->get_cards_for_taxonomy($taxonomy_slug, $dynamic_source);

                $tabs[] = [
                    'id' => $taxonomy_slug,
                    'name' => $tab_name,
                    'slug' => $tab_slug,
                    'icon' => $tab_icon,
                    'cards' => $cards,
                ];
            }

            // 2. Process individual terms
            foreach ($selected_terms as $term_id) {
                // Get term without specifying taxonomy - WordPress will find it
                $term = get_term($term_id);

                if (!$term || is_wp_error($term)) {
                    continue;
                }

                // Get the taxonomy from the term object
                $term_taxonomy = $term->taxonomy;

                // Check for custom name and icon override
                $tab_name = $term->name;
                $tab_icon = null;

                if (!empty($tab_overrides)) {
                    foreach ($tab_overrides as $override) {
                        if (isset($override['term_id']) && $override['term_id'] == $term_id) {
                            // Apply custom name if provided
                            if (!empty($override['custom_name'])) {
                                $tab_name = $override['custom_name'];
                            }
                            // Apply icon if provided (convert attachment ID to array format)
                            if (!empty($override['icon'])) {
                                $tab_icon = $this->prepare_icon_data($override['icon']);
                            }
                            break;
                        }
                    }
                }

                // Get cards for this term
                $cards = $this->get_cards_for_term($term_id, $term_taxonomy, $dynamic_source);

                // Always add the tab, even if there are no cards
                $tabs[] = [
                    'id' => $term_id,
                    'name' => $tab_name,
                    'slug' => $term->slug,
                    'icon' => $tab_icon,
                    'cards' => $cards, // Can be empty array
                ];
            }

            // Process selected locations (CPT)
            if (!empty($selected_locations) && is_array($selected_locations)) {
                foreach ($selected_locations as $location_id) {
                    // Get location post
                    $location = get_post($location_id);

                    if (!$location || $location->post_type !== 'location') {
                        continue;
                    }

                    // Check for custom name and icon override
                    $tab_name = $location->post_title;
                    $tab_icon = null;

                    if (!empty($tab_overrides)) {
                        foreach ($tab_overrides as $override) {
                            if (isset($override['term_id']) && $override['term_id'] == $location_id) {
                                // Apply custom name if provided
                                if (!empty($override['custom_name'])) {
                                    $tab_name = $override['custom_name'];
                                }
                                // Apply icon if provided (convert attachment ID to array format)
                                if (!empty($override['icon'])) {
                                    $tab_icon = $this->prepare_icon_data($override['icon']);
                                }
                                break;
                            }
                        }
                    }

                    // Get cards for this location (uses meta_query)
                    $cards = $this->get_cards_for_location_cpt($location_id, $dynamic_source);

                    // Always add the tab, even if there are no cards
                    $tabs[] = [
                        'id' => $location_id,
                        'name' => $tab_name,
                        'slug' => $location->post_name,
                        'icon' => $tab_icon,
                        'cards' => $cards, // Can be empty array
                    ];
                }
            }
        }

        // Get Display Fields (control what to show in each card)
        $display_fields_packages = get_field('tt_mat_dynamic_visible_fields') ?: [];
        $display_fields_posts = get_field('tt_mat_dynamic_visible_fields') ?: [];

        // Pass data to template
        $data = [
            'block_wrapper_attributes' => $block_wrapper_attributes,
            'block_id' => $block_id,
            'align' => $align,
            'tabs' => $tabs,
            'tabs_style' => $tabs_style,
            'tabs_alignment' => $tabs_alignment,
            'cards_per_row' => $cards_per_row,
            'card_gap' => $card_gap,
            'button_color_variant' => $button_color_variant,
            'badge_color_variant' => $badge_color_variant,
            'display_fields_packages' => $display_fields_packages,
            'display_fields_posts' => $display_fields_posts,
            'is_preview' => $is_preview || $preview_mode,
            // Slider settings (mobile)
            'card_height' => $card_height,
            'show_arrows' => $show_arrows,
            'arrows_position' => $arrows_position,
            'show_dots' => $show_dots,
            'autoplay' => $autoplay,
            'autoplay_delay' => $autoplay_delay,
            'slider_speed' => $slider_speed,
        ];

        // Load template
        $template = TRAVEL_BLOCKS_PATH . 'templates/taxonomy-tabs.php';
        if (file_exists($template)) {
            include $template;
        }
    }

    /**
     * Get cards for a specific term
     */
    private function get_cards_for_term($term_id, $taxonomy, $source)
    {
        // Use ContentQueryHelper with taxonomy filter
        // We'll add the taxonomy filter to the get_content method temporarily

        $args = [
            'post_type' => $source === 'deal' ? 'deal' : ($source === 'post' ? 'post' : 'package'),
            'posts_per_page' => get_field('tt_posts_per_page') ?: 6,
            'post_status' => 'publish',
            'tax_query' => [
                [
                    'taxonomy' => $taxonomy,
                    'field' => 'term_id',
                    'terms' => $term_id,
                ],
            ],
        ];

        // Add filters from ContentQueryHelper
        $meta_query = [];

        // Apply filters similar to ContentQueryHelper
        $filter_active_promo = get_field('tt_filter_active_promo');
        if ($filter_active_promo && $source === 'package') {
            $meta_query[] = [
                'key' => 'active_promotion',
                'value' => '1',
                'compare' => '=',
            ];
        }

        if (!empty($meta_query)) {
            $args['meta_query'] = $meta_query;
        }

        $query = new \WP_Query($args);
        $cards = [];

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $post = get_post();

                // Use ContentQueryHelper to prepare card data
                if ($source === 'package') {
                    $cards[] = ContentQueryHelper::prepare_package_card_data($post, 'tt');
                } elseif ($source === 'post') {
                    $cards[] = ContentQueryHelper::prepare_post_card_data($post, 'tt');
                }
            }
            wp_reset_postdata();
        }

        return $cards;
    }

    /**
     * Get cards for location CPT (uses meta_query on tag_locations field)
     *
     * @param int $location_id The location post ID
     * @param string $source The source type (package, post, deal)
     * @return array Array of card data
     */
    private function get_cards_for_location_cpt($location_id, $source)
    {
        // Build the serialized pattern to search in the array
        // ACF stores arrays as serialized PHP: a:2:{i:0;s:4:"4010";i:1;s:4:"4015";}
        // We need to search for the ID as a string in serialized format: s:X:"ID"
        $serialized_id = serialize(strval($location_id));
        $meta_value = trim($serialized_id, 's:');

        $args = [
            'post_type' => $source === 'deal' ? 'deal' : ($source === 'post' ? 'post' : 'package'),
            'posts_per_page' => get_field('tt_posts_per_page') ?: 6,
            'post_status' => 'publish',
            'meta_query' => [
                [
                    'key' => 'tag_locations',
                    'value' => $serialized_id,
                    'compare' => 'LIKE',
                ],
            ],
        ];

        // Apply filters similar to ContentQueryHelper
        $filter_active_promo = get_field('tt_filter_active_promo');
        if ($filter_active_promo && $source === 'package') {
            $args['meta_query'][] = [
                'key' => 'active_promotion',
                'value' => '1',
                'compare' => '=',
            ];
        }

        $query = new \WP_Query($args);
        $cards = [];

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $post = get_post();

                // Use ContentQueryHelper to prepare card data
                if ($source === 'package') {
                    $cards[] = ContentQueryHelper::prepare_package_card_data($post, 'tt');
                } elseif ($source === 'post') {
                    $cards[] = ContentQueryHelper::prepare_post_card_data($post, 'tt');
                }
            }
            wp_reset_postdata();
        }

        return $cards;
    }

    /**
     * Get preview/sample tabs for editor
     */
    private function get_preview_tabs($source, $taxonomy)
    {
        $preview_tabs = [];

        if ($source === 'package') {
            $sample_terms = [
                ['id' => 1, 'name' => 'Trekking', 'slug' => 'trekking'],
                ['id' => 2, 'name' => 'Cultural Tours', 'slug' => 'cultural'],
                ['id' => 3, 'name' => 'Adventure', 'slug' => 'adventure'],
            ];
        } else {
            $sample_terms = [
                ['id' => 1, 'name' => 'Travel Tips', 'slug' => 'tips'],
                ['id' => 2, 'name' => 'Destinations', 'slug' => 'destinations'],
                ['id' => 3, 'name' => 'Culture', 'slug' => 'culture'],
            ];
        }

        foreach ($sample_terms as $term) {
            $preview_tabs[] = [
                'id' => $term['id'],
                'name' => $term['name'],
                'slug' => $term['slug'],
                'icon' => null, // Icons are optional, null by default
                'cards' => $this->get_sample_cards($source, 3),
            ];
        }

        return $preview_tabs;
    }

    /**
     * Get sample cards for preview
     */
    private function get_sample_cards($source, $count = 3)
    {
        $cards = [];

        for ($i = 1; $i <= $count; $i++) {
            if ($source === 'package') {
                $cards[] = [
                    'acf_fc_layout' => 'card',
                    'image' => [
                        'url' => 'https://picsum.photos/800/600?random=' . $i,
                        'alt' => 'Sample Package ' . $i,
                    ],
                    'category' => 'POPULAR',
                    'badge_color_variant' => 'primary',
                    'title' => 'Sample Package ' . $i,
                    'description' => 'This is a sample package description for preview.',
                    'location' => 'Cusco, Peru',
                    'duration' => '5 days',
                    'price' => 'From $' . (299 + ($i * 100)) . ' USD',
                    'duration_price' => '5 days | From $' . (299 + ($i * 100)) . ' USD',
                    'is_package' => true,
                    'link' => ['url' => '#'],
                    'cta_text' => 'View Package',
                ];
            } else {
                $cards[] = [
                    'acf_fc_layout' => 'card',
                    'image' => [
                        'url' => 'https://picsum.photos/800/600?random=' . ($i + 10),
                        'alt' => 'Sample Post ' . $i,
                    ],
                    'category' => 'ARTICLE',
                    'badge_color_variant' => 'secondary',
                    'title' => 'Sample Blog Post ' . $i,
                    'description' => 'This is a sample blog post description for preview.',
                    'location' => 'Peru',
                    'date' => date('F j, Y'),
                    'link' => ['url' => '#'],
                    'cta_text' => 'Read More',
                ];
            }
        }

        return $cards;
    }

    /**
     * Load Package Type choices
     */
    public function load_package_type_choices($field)
    {
        $field['choices'] = $this->get_taxonomy_choices('package_type');
        return $field;
    }

    /**
     * Load Interest choices
     */
    public function load_interest_choices($field)
    {
        $field['choices'] = $this->get_taxonomy_choices('interest');
        return $field;
    }

    /**
     * Load Locations CPT choices
     */
    public function load_locations_cpt_choices($field)
    {
        $field['choices'] = $this->get_locations_cpt_choices();
        return $field;
    }

    /**
     * Load Category choices
     */
    public function load_category_choices($field)
    {
        $field['choices'] = $this->get_taxonomy_choices('category');
        return $field;
    }

    /**
     * Load Post Tag choices
     */
    public function load_post_tag_choices($field)
    {
        $field['choices'] = $this->get_taxonomy_choices('post_tag');
        return $field;
    }

    /**
     * Get taxonomy choices for checkbox field
     *
     * @param string $taxonomy The taxonomy name to get terms from
     * @return array Array of term_id => term_name choices
     */
    private function get_taxonomy_choices($taxonomy)
    {
        $choices = [];

        $terms = get_terms([
            'taxonomy' => $taxonomy,
            'hide_empty' => false,
            'orderby' => 'name',
            'order' => 'ASC',
        ]);

        if (!empty($terms) && !is_wp_error($terms)) {
            foreach ($terms as $term) {
                $choices[$term->term_id] = $term->name;
            }
        }

        return $choices;
    }

    /**
     * Get locations CPT choices for checkbox field
     *
     * @return array Array of post_id => post_title choices
     */
    private function get_locations_cpt_choices()
    {
        $choices = [];

        $locations = get_posts([
            'post_type' => 'location',
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC',
            'post_status' => 'publish',
        ]);

        if (!empty($locations)) {
            foreach ($locations as $location) {
                $choices[$location->ID] = $location->post_title;
            }
        }

        return $choices;
    }

    /**
     * Load all available terms for override repeater
     * JavaScript will filter these based on selected checkboxes
     */
    public function load_selected_terms_for_override($field)
    {
        $choices = [];

        // Add complete taxonomies as options (these are global options)
        $choices['package_type'] = '📦 Package Types (taxonomía completa)';
        $choices['interest'] = '⭐ Interests (taxonomía completa)';
        $choices['locations_cpt'] = '📍 Locations (taxonomía completa)';
        $choices['category'] = '📁 Categories (taxonomía completa)';
        $choices['post_tag'] = '🏷️ Tags (taxonomía completa)';

        // Package Type terms
        $package_types = get_terms([
            'taxonomy' => 'package_type',
            'hide_empty' => false,
            'orderby' => 'name',
        ]);
        if (!empty($package_types) && !is_wp_error($package_types)) {
            foreach ($package_types as $term) {
                $choices[$term->term_id] = $term->name . ' (Package Type)';
            }
        }

        // Interest terms
        $interests = get_terms([
            'taxonomy' => 'interest',
            'hide_empty' => false,
            'orderby' => 'name',
        ]);
        if (!empty($interests) && !is_wp_error($interests)) {
            foreach ($interests as $term) {
                $choices[$term->term_id] = $term->name . ' (Interest)';
            }
        }

        // Category terms
        $categories = get_terms([
            'taxonomy' => 'category',
            'hide_empty' => false,
            'orderby' => 'name',
        ]);
        if (!empty($categories) && !is_wp_error($categories)) {
            foreach ($categories as $term) {
                $choices[$term->term_id] = $term->name . ' (Category)';
            }
        }

        // Tag terms
        $tags = get_terms([
            'taxonomy' => 'post_tag',
            'hide_empty' => false,
            'orderby' => 'name',
        ]);
        if (!empty($tags) && !is_wp_error($tags)) {
            foreach ($tags as $term) {
                $choices[$term->term_id] = $term->name . ' (Tag)';
            }
        }

        // Location posts
        $locations = get_posts([
            'post_type' => 'location',
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC',
            'post_status' => 'publish',
        ]);
        if (!empty($locations)) {
            foreach ($locations as $location) {
                $choices[$location->ID] = $location->post_title . ' (Location)';
            }
        }

        $field['choices'] = $choices;
        return $field;
    }

    /**
     * Reconstruct repeater array from flattened block data
     * ACF stores repeater data in a flattened format:
     * - tt_tab_overrides = count of rows
     * - tt_tab_overrides_0_term_id, tt_tab_overrides_0_custom_name, etc.
     */
    private function reconstruct_repeater_from_block_data($block_data, $repeater_name, $subfields)
    {
        // Check if we have a count
        if (!isset($block_data[$repeater_name]) || !is_numeric($block_data[$repeater_name])) {
            return [];
        }

        $count = intval($block_data[$repeater_name]);
        if ($count === 0) {
            return [];
        }

        $result = [];

        // Loop through each row
        for ($i = 0; $i < $count; $i++) {
            $row = [];

            // Get each subfield value
            foreach ($subfields as $subfield) {
                $key = "{$repeater_name}_{$i}_{$subfield}";
                if (isset($block_data[$key])) {
                    $row[$subfield] = $block_data[$key];
                }
            }

            // Only add row if it has data
            if (!empty($row)) {
                $result[] = $row;
            }
        }

        return $result;
    }

    /**
     * Prepare icon data from attachment ID
     * Converts attachment ID to array format expected by template
     *
     * @param int|string $icon_id The attachment ID
     * @return array|null Icon data with url, alt, mime_type and path, or null if invalid
     */
    private function prepare_icon_data($icon_id)
    {
        if (empty($icon_id) || !is_numeric($icon_id)) {
            return null;
        }

        $image_data = wp_get_attachment_image_src($icon_id, 'full');
        $image_alt = get_post_meta($icon_id, '_wp_attachment_image_alt', true);
        $mime_type = get_post_mime_type($icon_id);
        $file_path = get_attached_file($icon_id);

        if (!$image_data) {
            return null;
        }

        return [
            'url' => $image_data[0],
            'alt' => $image_alt ?: '',
            'id' => $icon_id,
            'mime_type' => $mime_type,
            'path' => $file_path,
        ];
    }

    /**
     * Get cards for a complete taxonomy (all terms combined)
     * Creates ONE tab that shows packages/posts with ANY term from this taxonomy
     *
     * @param string $taxonomy Taxonomy slug (e.g., 'package_type', 'interest') or 'locations_cpt'
     * @param string $source Source type (package, post, deal)
     * @return array Array of card data
     */
    private function get_cards_for_taxonomy($taxonomy, $source)
    {
        // Special handling for locations_cpt (uses meta_query instead of tax_query)
        if ($taxonomy === 'locations_cpt') {
            $args = [
                'post_type' => 'package',
                'posts_per_page' => get_field('tt_posts_per_page') ?: 6,
                'post_status' => 'publish',
                'meta_query' => [
                    [
                        'key' => 'tag_locations',
                        'compare' => 'EXISTS', // Has any location assigned
                    ],
                ],
            ];

            // Add promo filter if needed
            $filter_active_promo = get_field('tt_filter_active_promo');
            if ($filter_active_promo) {
                $args['meta_query'][] = [
                    'key' => 'active_promotion',
                    'value' => '1',
                    'compare' => '=',
                ];
                $args['meta_query']['relation'] = 'AND';
            }

            $query = new \WP_Query($args);
            $cards = [];

            if ($query->have_posts()) {
                while ($query->have_posts()) {
                    $query->the_post();
                    $post = get_post();
                    $cards[] = ContentQueryHelper::prepare_package_card_data($post, 'tt');
                }
                wp_reset_postdata();
            }

            return $cards;
        }

        // Standard taxonomy handling
        $args = [
            'post_type' => $source === 'deal' ? 'deal' : ($source === 'post' ? 'post' : 'package'),
            'posts_per_page' => get_field('tt_posts_per_page') ?: 6,
            'post_status' => 'publish',
            'tax_query' => [
                [
                    'taxonomy' => $taxonomy,
                    'operator' => 'EXISTS', // Any term from this taxonomy
                ],
            ],
        ];

        // Add filters from ContentQueryHelper
        $meta_query = [];

        // Apply filters similar to ContentQueryHelper
        $filter_active_promo = get_field('tt_filter_active_promo');
        if ($filter_active_promo && $source === 'package') {
            $meta_query[] = [
                'key' => 'active_promotion',
                'value' => '1',
                'compare' => '=',
            ];
        }

        if (!empty($meta_query)) {
            $args['meta_query'] = $meta_query;
        }

        $query = new \WP_Query($args);
        $cards = [];

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $post = get_post();

                // Use ContentQueryHelper to prepare card data
                if ($source === 'package') {
                    $cards[] = ContentQueryHelper::prepare_package_card_data($post, 'tt');
                } elseif ($source === 'post') {
                    $cards[] = ContentQueryHelper::prepare_post_card_data($post, 'tt');
                }
            }
            wp_reset_postdata();
        }

        return $cards;
    }
}
