<?php
/**
 * Block: Taxonomy Tabs
 *
 * Advanced tabs system for organizing dynamic content by taxonomy terms.
 * Supports packages, posts, and deals with flexible taxonomy/term selection.
 *
 * Features:
 * - Dynamic content from packages/posts/deals via ContentQueryHelper
 * - Complete taxonomies OR individual terms as tabs
 * - Tab customization (names, icons) via repeater
 * - 4 tab styles: pills, underline, buttons, hero-overlap
 * - Desktop: responsive grid layout
 * - Mobile: slider with arrows (3 positions) + dots + autoplay
 * - ACF filters for dynamic choices loading
 * - Gutenberg block data reconstruction
 *
 * @package Travel\Blocks\ACF
 * @since 1.0.0
 * @version 1.2.0 - DEEP REFACTOR: Split 467-line and 314-line methods into focused methods
 *
 * @see ContentQueryHelper For dynamic content queries
 */

namespace Travel\Blocks\Blocks\ACF;

use Travel\Helpers\ContentQueryHelper;

class TaxonomyTabs
{
    /**
     * Block name identifier.
     *
     * @var string
     */
    private string $name = 'travel-taxonomy-tabs';

    /**
     * Constructor - Register ACF filter hooks.
     *
     * Hooks into ACF field loading to populate dynamic choices for:
     * - Taxonomy term checkboxes (package_type, interest, locations, category, post_tag)
     * - Override repeater dropdown (filtered by selected terms)
     *
     * @return void
     */
    public function __construct()
    {
        // Add ACF filters to load checkbox choices dynamically
        add_filter('acf/load_field/name=tt_selected_terms_package_type', [$this, 'load_package_type_choices']);
        add_filter('acf/load_field/name=tt_selected_terms_interest', [$this, 'load_interest_choices']);
        add_filter('acf/load_field/name=tt_selected_locations_cpt', [$this, 'load_locations_cpt_choices']);
        add_filter('acf/load_field/name=tt_selected_terms_category', [$this, 'load_category_choices']);
        add_filter('acf/load_field/name=tt_selected_terms_post_tag', [$this, 'load_post_tag_choices']);

        // Add filter for repeater term_id field to load only selected terms
        add_filter('acf/load_field/key=field_tt_override_term_id', [$this, 'load_selected_terms_for_override']);
    }

    /**
     * Register block, ACF fields, and enqueue hooks.
     *
     * @return void
     */
    public function register(): void
    {
        // Enqueue assets for both frontend and editor
        add_action('enqueue_block_assets', [$this, 'enqueue_assets']);
        add_action('enqueue_block_editor_assets', [$this, 'enqueue_assets']);

        $this->register_block();
        $this->register_fields();
    }

    /**
     * Register ACF block type.
     *
     * Registers Gutenberg block with full configuration:
     * - Wide/full alignment support
     * - Spacing and color controls
     * - Preview mode for editor
     *
     * @return void
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
     * Register ACF fields for this block.
     *
     * ✅ REFACTORED: Previously 467 lines - now split into focused methods.
     *
     * Builds complete field array by merging:
     * - General tab fields (content source)
     * - Taxonomies tab fields (taxonomy/term selection)
     * - Dynamic content fields (from ContentQueryHelper)
     * - Appearance tab fields (styling options)
     * - Slider settings fields (mobile configuration)
     *
     * @return void
     */
    public function register_fields(): void
    {
        if (!function_exists('acf_add_local_field_group')) {
            return;
        }

        // Get dynamic content fields from ContentQueryHelper
        $dynamic_fields = ContentQueryHelper::get_dynamic_content_fields('tt');
        $filter_fields = ContentQueryHelper::get_filter_fields('tt');

        // Build complete fields array by merging all field groups
        $fields = array_merge(
            $this->get_general_tab_fields(),
            $this->get_taxonomies_tab_fields(),
            $filter_fields,
            $this->get_appearance_tab_fields(),
            $this->get_slider_settings_fields()
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
     * Get General tab ACF fields.
     *
     * Returns fields for:
     * - Content source selector (packages/posts/deals)
     * - Preview mode toggle
     *
     * @return array ACF fields configuration
     */
    private function get_general_tab_fields(): array
    {
        return [
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
            [
                'key' => 'field_tt_preview_mode',
                'label' => 'Modo Vista Previa',
                'name' => 'tt_preview_mode',
                'type' => 'true_false',
                'instructions' => 'Activar para mostrar datos de ejemplo en el editor',
                'default_value' => 0,
                'ui' => 1,
            ],
        ];
    }

    /**
     * Get Taxonomies tab ACF fields.
     *
     * ✅ REFACTORED: Previously embedded in 467-line method.
     *
     * Returns fields for:
     * - Complete taxonomy selection (checkbox)
     * - Individual term selection (checkbox per taxonomy)
     * - Individual location selection (CPT)
     * - Tab customization repeater (custom names/icons)
     *
     * Conditional logic shows relevant fields based on selected content source.
     *
     * @return array ACF fields configuration
     */
    private function get_taxonomies_tab_fields(): array
    {
        return [
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

            // === PACKAGE SOURCE FIELDS ===
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

            // === POST SOURCE FIELDS ===
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

            // === TAB CUSTOMIZATION REPEATER ===
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
        ];
    }

    /**
     * Get Appearance tab ACF fields.
     *
     * ✅ REFACTORED: Previously embedded in 467-line method.
     *
     * Returns fields for:
     * - Tab style selection (pills/underline/buttons/hero-overlap)
     * - Tab alignment (left/center/right)
     * - Cards per row (2-4)
     * - Card gap spacing
     * - Button and badge color variants
     *
     * @return array ACF fields configuration
     */
    private function get_appearance_tab_fields(): array
    {
        return [
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
        ];
    }

    /**
     * Get Slider Settings tab ACF fields.
     *
     * ✅ REFACTORED: Previously embedded in 467-line method.
     *
     * Returns fields for mobile slider configuration:
     * - Card height
     * - Arrow navigation (show/hide, position)
     * - Dot pagination
     * - Autoplay settings (enable, delay, speed)
     *
     * @return array ACF fields configuration
     */
    private function get_slider_settings_fields(): array
    {
        return [
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
        ];
    }

    /**
     * Enqueue block assets (CSS and JavaScript).
     *
     * Loads:
     * - taxonomy-tabs.css (frontend styles)
     * - taxonomy-tabs.js (frontend tab switching and mobile slider)
     * - taxonomy-tabs-editor.js (admin only - filters repeater dropdown)
     *
     * @return void
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
     * Render block output.
     *
     * ✅ REFACTORED: Previously 314 lines - now split into focused methods.
     *
     * Workflow:
     * 1. Extract block data and ACF fields
     * 2. Collect selected taxonomies/terms/locations
     * 3. Build tabs array with cards
     * 4. Get appearance and slider settings
     * 5. Prepare template data and load template
     *
     * @param array  $block      Block settings and attributes
     * @param string $content    Block content (unused)
     * @param bool   $is_preview Whether block is being previewed in editor
     * @param int    $post_id    Current post ID
     *
     * @return void
     */
    public function render($block, $content = '', $is_preview = false, $post_id = 0): void
    {
        // 1. Extract block data (from Gutenberg or ACF)
        $block_data = $this->extract_block_data($block);

        // 2. Collect selected items (taxonomies, terms, locations)
        $selected_items = $this->collect_selected_items($block_data);

        // 3. Build tabs array
        $tabs = $this->build_tabs_array($selected_items, $block_data);

        // 4. Get appearance settings
        $appearance = $this->get_appearance_settings($block_data);

        // 5. Get slider settings (mobile)
        $slider = $this->get_slider_settings($block_data);

        // 6. Prepare template data
        $data = $this->prepare_template_data($block, $tabs, $appearance, $slider, $is_preview, $block_data);

        // 7. Load template
        $template = TRAVEL_BLOCKS_PATH . 'templates/taxonomy-tabs.php';
        if (file_exists($template)) {
            include $template;
        }
    }

    /**
     * Extract block data from Gutenberg or ACF.
     *
     * ✅ REFACTORED: Extracted from 314-line render() method.
     *
     * Gutenberg passes block data in $block['data'], but for backwards compatibility
     * we also support get_field() fallback.
     *
     * @param array $block Block settings from Gutenberg
     *
     * @return array Block data with all ACF field values
     */
    private function extract_block_data(array $block): array
    {
        // IMPORTANTE: En Gutenberg, ACF pasa los datos del bloque en $block['data']
        // Intentamos obtener de ahí primero, si no existe, usamos get_field()
        return $block['data'] ?? [];
    }

    /**
     * Collect selected taxonomies, terms, and locations from block data.
     *
     * ✅ REFACTORED: Extracted from 314-line render() method.
     *
     * Returns array with:
     * - dynamic_source: Content type (package/post/deal)
     * - selected_taxonomies: Complete taxonomies to show as single tabs
     * - selected_terms: Individual term IDs to show as separate tabs
     * - selected_locations: Individual location post IDs
     * - tab_overrides: Repeater data for custom tab names/icons
     * - preview_mode: Whether to show sample data
     *
     * @param array $block_data Block data from Gutenberg or get_field()
     *
     * @return array Array with selected items structure
     */
    private function collect_selected_items(array $block_data): array
    {
        $dynamic_source = $block_data['tt_dynamic_source'] ?? get_field('tt_dynamic_source') ?: 'package';

        $selected_taxonomies = [];
        $selected_terms = [];
        $selected_locations = [];

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
            $tab_overrides = $this->reconstruct_repeater_from_block_data(
                $block_data,
                'tt_tab_overrides',
                ['term_id', 'custom_name', 'icon']
            );
        } else {
            $tab_overrides = get_field('tt_tab_overrides') ?: [];
        }

        $preview_mode = $block_data['tt_preview_mode'] ?? get_field('tt_preview_mode') ?: false;

        return [
            'dynamic_source' => $dynamic_source,
            'selected_taxonomies' => $selected_taxonomies,
            'selected_terms' => $selected_terms,
            'selected_locations' => $selected_locations,
            'tab_overrides' => $tab_overrides,
            'preview_mode' => $preview_mode,
        ];
    }

    /**
     * Build tabs array with cards data.
     *
     * ✅ REFACTORED: Extracted from 314-line render() method.
     *
     * Creates tabs from:
     * 1. Complete taxonomies (one tab per taxonomy showing all terms)
     * 2. Individual terms (one tab per term)
     * 3. Individual locations (one tab per location)
     *
     * Each tab contains:
     * - id: Unique identifier (taxonomy slug, term ID, or location ID)
     * - name: Display name (can be overridden)
     * - slug: URL-friendly slug
     * - icon: Optional icon data
     * - cards: Array of card data (packages, posts, or deals)
     *
     * @param array $selected_items Selected taxonomies/terms/locations
     * @param array $block_data     Block data for field access
     *
     * @return array Array of tab data
     */
    private function build_tabs_array(array $selected_items, array $block_data): array
    {
        $tabs = [];
        $dynamic_source = $selected_items['dynamic_source'];
        $preview_mode = $selected_items['preview_mode'];

        if ($preview_mode) {
            // Preview mode: Generate sample data
            return $this->get_preview_tabs($dynamic_source, 'package_type');
        }

        // Real mode: Process complete taxonomies first, then individual terms

        // 1. Process complete taxonomies (create ONE tab per taxonomy)
        foreach ($selected_items['selected_taxonomies'] as $taxonomy_slug) {
            $tabs[] = $this->build_taxonomy_tab($taxonomy_slug, $selected_items['tab_overrides'], $dynamic_source);
        }

        // 2. Process individual terms
        foreach ($selected_items['selected_terms'] as $term_id) {
            $tab = $this->build_term_tab($term_id, $selected_items['tab_overrides'], $dynamic_source);
            if ($tab) {
                $tabs[] = $tab;
            }
        }

        // 3. Process selected locations (CPT)
        if (!empty($selected_items['selected_locations']) && is_array($selected_items['selected_locations'])) {
            foreach ($selected_items['selected_locations'] as $location_id) {
                $tab = $this->build_location_tab($location_id, $selected_items['tab_overrides'], $dynamic_source);
                if ($tab) {
                    $tabs[] = $tab;
                }
            }
        }

        return $tabs;
    }

    /**
     * Build single taxonomy tab (shows all terms from taxonomy).
     *
     * @param string $taxonomy_slug Taxonomy slug
     * @param array  $tab_overrides Tab customization data
     * @param string $dynamic_source Content source type
     *
     * @return array Tab data
     */
    private function build_taxonomy_tab(string $taxonomy_slug, array $tab_overrides, string $dynamic_source): array
    {
        // Special handling for locations_cpt (it's not a taxonomy)
        if ($taxonomy_slug === 'locations_cpt') {
            $tab_name = 'Locations';
            $tab_icon = null;
            $tab_slug = 'locations';

            // Check for overrides
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

            $cards = $this->get_cards_for_taxonomy('locations_cpt', $dynamic_source);

            return [
                'id' => 'locations_cpt',
                'name' => $tab_name,
                'slug' => $tab_slug,
                'icon' => $tab_icon,
                'cards' => $cards,
            ];
        }

        // Get taxonomy object to get the label
        $taxonomy_obj = get_taxonomy($taxonomy_slug);
        if (!$taxonomy_obj) {
            return [];
        }

        $tab_name = $taxonomy_obj->label;
        $tab_icon = null;
        $tab_slug = $taxonomy_slug;

        // Check for overrides using taxonomy slug as identifier
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

        // Get cards for ALL terms in this taxonomy
        $cards = $this->get_cards_for_taxonomy($taxonomy_slug, $dynamic_source);

        return [
            'id' => $taxonomy_slug,
            'name' => $tab_name,
            'slug' => $tab_slug,
            'icon' => $tab_icon,
            'cards' => $cards,
        ];
    }

    /**
     * Build single term tab.
     *
     * @param int    $term_id        Term ID
     * @param array  $tab_overrides  Tab customization data
     * @param string $dynamic_source Content source type
     *
     * @return array|null Tab data or null if term not found
     */
    private function build_term_tab(int $term_id, array $tab_overrides, string $dynamic_source): ?array
    {
        $term = get_term($term_id);

        if (!$term || is_wp_error($term)) {
            return null;
        }

        $tab_name = $term->name;
        $tab_icon = null;

        // Check for custom name and icon override
        foreach ($tab_overrides as $override) {
            if (isset($override['term_id']) && $override['term_id'] == $term_id) {
                if (!empty($override['custom_name'])) {
                    $tab_name = $override['custom_name'];
                }
                if (!empty($override['icon'])) {
                    $tab_icon = $this->prepare_icon_data($override['icon']);
                }
                break;
            }
        }

        // Get cards for this term
        $cards = $this->get_cards_for_term($term_id, $term->taxonomy, $dynamic_source);

        return [
            'id' => $term_id,
            'name' => $tab_name,
            'slug' => $term->slug,
            'icon' => $tab_icon,
            'cards' => $cards,
        ];
    }

    /**
     * Build single location tab.
     *
     * @param int    $location_id    Location post ID
     * @param array  $tab_overrides  Tab customization data
     * @param string $dynamic_source Content source type
     *
     * @return array|null Tab data or null if location not found
     */
    private function build_location_tab(int $location_id, array $tab_overrides, string $dynamic_source): ?array
    {
        $location = get_post($location_id);

        if (!$location || $location->post_type !== 'location') {
            return null;
        }

        $tab_name = $location->post_title;
        $tab_icon = null;

        // Check for custom name and icon override
        foreach ($tab_overrides as $override) {
            if (isset($override['term_id']) && $override['term_id'] == $location_id) {
                if (!empty($override['custom_name'])) {
                    $tab_name = $override['custom_name'];
                }
                if (!empty($override['icon'])) {
                    $tab_icon = $this->prepare_icon_data($override['icon']);
                }
                break;
            }
        }

        // Get cards for this location
        $cards = $this->get_cards_for_location_cpt($location_id, $dynamic_source);

        return [
            'id' => $location_id,
            'name' => $tab_name,
            'slug' => $location->post_name,
            'icon' => $tab_icon,
            'cards' => $cards,
        ];
    }

    /**
     * Get appearance settings from block data.
     *
     * ✅ REFACTORED: Extracted from 314-line render() method.
     *
     * Returns appearance configuration:
     * - tabs_style: Visual style (pills/underline/buttons/hero-overlap)
     * - tabs_alignment: Left/center/right
     * - cards_per_row: Number of cards per row on desktop (2-4)
     * - card_gap: Spacing between cards in pixels
     * - button_color_variant: Primary/secondary/accent
     * - badge_color_variant: Primary/secondary/accent
     *
     * @param array $block_data Block data from Gutenberg or get_field()
     *
     * @return array Appearance settings
     */
    private function get_appearance_settings(array $block_data): array
    {
        return [
            'tabs_style' => $block_data['tt_tabs_style'] ?? get_field('tt_tabs_style') ?: 'pills',
            'tabs_alignment' => $block_data['tt_tabs_alignment'] ?? get_field('tt_tabs_alignment') ?: 'center',
            'cards_per_row' => $block_data['tt_cards_per_row'] ?? get_field('tt_cards_per_row') ?: 3,
            'card_gap' => $block_data['tt_card_gap'] ?? get_field('tt_card_gap') ?: 24,
            'button_color_variant' => $block_data['tt_button_color_variant'] ?? get_field('tt_button_color_variant') ?: 'primary',
            'badge_color_variant' => $block_data['tt_badge_color_variant'] ?? get_field('tt_badge_color_variant') ?: 'secondary',
        ];
    }

    /**
     * Get slider settings (mobile) from block data.
     *
     * ✅ REFACTORED: Extracted from 314-line render() method.
     *
     * Returns mobile slider configuration:
     * - card_height: Height of cards in pixels
     * - show_arrows: Whether to show navigation arrows
     * - arrows_position: Position of arrows (sides/overlay/bottom)
     * - show_dots: Whether to show pagination dots
     * - autoplay: Whether to enable autoplay
     * - autoplay_delay: Delay between slides in milliseconds
     * - slider_speed: Transition speed in seconds
     *
     * @param array $block_data Block data from Gutenberg or get_field()
     *
     * @return array Slider settings
     */
    private function get_slider_settings(array $block_data): array
    {
        return [
            'card_height' => $block_data['tt_card_height'] ?? get_field('tt_card_height') ?: 450,
            'show_arrows' => (bool)($block_data['tt_show_arrows'] ?? get_field('tt_show_arrows') ?? true),
            'arrows_position' => $block_data['tt_arrows_position'] ?? get_field('tt_arrows_position') ?: 'sides',
            'show_dots' => (bool)($block_data['tt_show_dots'] ?? get_field('tt_show_dots') ?? true),
            'autoplay' => (bool)($block_data['tt_autoplay'] ?? get_field('tt_autoplay') ?? false),
            'autoplay_delay' => (float)($block_data['tt_autoplay_delay'] ?? get_field('tt_autoplay_delay') ?: 5) * 1000,
            'slider_speed' => (float)($block_data['tt_slider_speed'] ?? get_field('tt_slider_speed') ?: 0.4),
        ];
    }

    /**
     * Prepare template data array.
     *
     * ✅ REFACTORED: Extracted from 314-line render() method.
     *
     * Combines all block data into final array passed to template:
     * - Block wrapper attributes (classes, ID)
     * - Tabs array with cards
     * - Appearance settings
     * - Slider settings (mobile)
     * - Display fields configuration
     * - Preview mode status
     *
     * @param array $block      Block settings from Gutenberg
     * @param array $tabs       Tabs array with cards data
     * @param array $appearance Appearance settings
     * @param array $slider     Slider settings
     * @param bool  $is_preview Whether in editor preview mode
     * @param array $block_data Block data for additional fields
     *
     * @return array Complete template data
     */
    private function prepare_template_data(array $block, array $tabs, array $appearance, array $slider, bool $is_preview, array $block_data): array
    {
        // Get tabs style for wrapper class
        $tabs_style = $appearance['tabs_style'];

        // Build wrapper classes
        $wrapper_classes = ['taxonomy-tabs-wrapper'];
        if ($tabs_style === 'hero-overlap') {
            $wrapper_classes[] = 'taxonomy-tabs-wrapper--hero-overlap';
        }

        $block_wrapper_attributes = get_block_wrapper_attributes([
            'class' => implode(' ', $wrapper_classes)
        ]);

        $block_id = 'tt-' . ($block['id'] ?? uniqid());
        $align = $block['align'] ?? 'wide';

        // Get Display Fields
        $display_fields_packages = get_field('tt_mat_dynamic_visible_fields') ?: [];
        $display_fields_posts = get_field('tt_mat_dynamic_visible_fields') ?: [];

        $preview_mode = $block_data['tt_preview_mode'] ?? get_field('tt_preview_mode') ?: false;

        return array_merge(
            [
                'block_wrapper_attributes' => $block_wrapper_attributes,
                'block_id' => $block_id,
                'align' => $align,
                'tabs' => $tabs,
                'display_fields_packages' => $display_fields_packages,
                'display_fields_posts' => $display_fields_posts,
                'is_preview' => $is_preview || $preview_mode,
            ],
            $appearance,
            $slider
        );
    }

    /**
     * Get cards for a specific term.
     *
     * Queries posts/packages/deals with:
     * - Taxonomy filter (term_id)
     * - Active promotion filter (optional, packages only)
     * - Prepares card data via ContentQueryHelper
     *
     * @param int    $term_id  Term ID to filter by
     * @param string $taxonomy Taxonomy name
     * @param string $source   Content source (package/post/deal)
     *
     * @return array Array of card data
     */
    private function get_cards_for_term($term_id, $taxonomy, $source): array
    {
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
     * Get cards for location CPT (uses meta_query on tag_locations field).
     *
     * Queries packages/posts/deals that have specific location assigned via
     * tag_locations ACF field (stored as serialized array).
     *
     * @param int    $location_id The location post ID
     * @param string $source      The source type (package, post, deal)
     *
     * @return array Array of card data
     */
    private function get_cards_for_location_cpt($location_id, $source): array
    {
        // Build the serialized pattern to search in the array
        $serialized_id = serialize(strval($location_id));

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

        // Apply filters
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
     * Get cards for a complete taxonomy (all terms combined).
     *
     * ✅ REFACTORED: Previously 92 lines - now uses helper methods.
     *
     * Creates ONE tab that shows packages/posts with ANY term from this taxonomy.
     * Special handling for locations_cpt which uses meta_query instead of tax_query.
     *
     * @param string $taxonomy Taxonomy slug (e.g., 'package_type', 'interest') or 'locations_cpt'
     * @param string $source   Source type (package, post, deal)
     *
     * @return array Array of card data
     */
    private function get_cards_for_taxonomy($taxonomy, $source): array
    {
        // Special handling for locations_cpt (uses meta_query instead of tax_query)
        if ($taxonomy === 'locations_cpt') {
            return $this->get_cards_for_all_locations($source);
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

        // Add filters
        $meta_query = [];
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

        return $this->execute_cards_query($args, $source);
    }

    /**
     * Get cards for all locations (locations_cpt).
     *
     * ✅ REFACTORED: Extracted from get_cards_for_taxonomy().
     *
     * @param string $source Content source type
     *
     * @return array Array of card data
     */
    private function get_cards_for_all_locations(string $source): array
    {
        $args = [
            'post_type' => 'package',
            'posts_per_page' => get_field('tt_posts_per_page') ?: 6,
            'post_status' => 'publish',
            'meta_query' => [
                [
                    'key' => 'tag_locations',
                    'compare' => 'EXISTS',
                ],
            ],
        ];

        $filter_active_promo = get_field('tt_filter_active_promo');
        if ($filter_active_promo) {
            $args['meta_query'][] = [
                'key' => 'active_promotion',
                'value' => '1',
                'compare' => '=',
            ];
            $args['meta_query']['relation'] = 'AND';
        }

        return $this->execute_cards_query($args, $source);
    }

    /**
     * Execute WP_Query and prepare card data.
     *
     * ✅ REFACTORED: Extracted from get_cards_for_taxonomy().
     *
     * @param array  $args   WP_Query arguments
     * @param string $source Content source type
     *
     * @return array Array of card data
     */
    private function execute_cards_query(array $args, string $source): array
    {
        $query = new \WP_Query($args);
        $cards = [];

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $post = get_post();

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
     * Get preview/sample tabs for editor.
     *
     * Generates 3 sample tabs with dummy data for preview mode.
     *
     * @param string $source   Content source (package/post/deal)
     * @param string $taxonomy Taxonomy slug (unused currently)
     *
     * @return array Array of preview tabs
     */
    private function get_preview_tabs($source, $taxonomy): array
    {
        $sample_terms = $source === 'package'
            ? [
                ['id' => 1, 'name' => 'Trekking', 'slug' => 'trekking'],
                ['id' => 2, 'name' => 'Cultural Tours', 'slug' => 'cultural'],
                ['id' => 3, 'name' => 'Adventure', 'slug' => 'adventure'],
            ]
            : [
                ['id' => 1, 'name' => 'Travel Tips', 'slug' => 'tips'],
                ['id' => 2, 'name' => 'Destinations', 'slug' => 'destinations'],
                ['id' => 3, 'name' => 'Culture', 'slug' => 'culture'],
            ];

        $preview_tabs = [];
        foreach ($sample_terms as $term) {
            $preview_tabs[] = [
                'id' => $term['id'],
                'name' => $term['name'],
                'slug' => $term['slug'],
                'icon' => null,
                'cards' => $this->get_sample_cards($source, 3),
            ];
        }

        return $preview_tabs;
    }

    /**
     * Get sample cards for preview.
     *
     * @param string $source Content source (package/post/deal)
     * @param int    $count  Number of sample cards to generate
     *
     * @return array Array of sample card data
     */
    private function get_sample_cards($source, $count = 3): array
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

    // ===== ACF FILTER CALLBACKS =====

    /**
     * Load Package Type choices for ACF checkbox field.
     *
     * @param array $field ACF field array
     *
     * @return array Modified field array with choices populated
     */
    public function load_package_type_choices($field): array
    {
        $field['choices'] = $this->get_taxonomy_choices('package_type');
        return $field;
    }

    /**
     * Load Interest choices for ACF checkbox field.
     *
     * @param array $field ACF field array
     *
     * @return array Modified field array with choices populated
     */
    public function load_interest_choices($field): array
    {
        $field['choices'] = $this->get_taxonomy_choices('interest');
        return $field;
    }

    /**
     * Load Locations CPT choices for ACF checkbox field.
     *
     * @param array $field ACF field array
     *
     * @return array Modified field array with choices populated
     */
    public function load_locations_cpt_choices($field): array
    {
        $field['choices'] = $this->get_locations_cpt_choices();
        return $field;
    }

    /**
     * Load Category choices for ACF checkbox field.
     *
     * @param array $field ACF field array
     *
     * @return array Modified field array with choices populated
     */
    public function load_category_choices($field): array
    {
        $field['choices'] = $this->get_taxonomy_choices('category');
        return $field;
    }

    /**
     * Load Post Tag choices for ACF checkbox field.
     *
     * @param array $field ACF field array
     *
     * @return array Modified field array with choices populated
     */
    public function load_post_tag_choices($field): array
    {
        $field['choices'] = $this->get_taxonomy_choices('post_tag');
        return $field;
    }

    /**
     * Get taxonomy choices for checkbox field.
     *
     * @param string $taxonomy The taxonomy name to get terms from
     *
     * @return array Array of term_id => term_name choices
     */
    private function get_taxonomy_choices($taxonomy): array
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
     * Get locations CPT choices for checkbox field.
     *
     * @return array Array of post_id => post_title choices
     */
    private function get_locations_cpt_choices(): array
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
     * Load all available terms for override repeater.
     *
     * Populates dropdown with all taxonomies and terms that can be customized.
     * JavaScript filters these based on selected checkboxes.
     *
     * @param array $field ACF field array
     *
     * @return array Modified field array with choices populated
     */
    public function load_selected_terms_for_override($field): array
    {
        $choices = [];

        // Add complete taxonomies as options
        $choices['package_type'] = '📦 Package Types (taxonomía completa)';
        $choices['interest'] = '⭐ Interests (taxonomía completa)';
        $choices['locations_cpt'] = '📍 Locations (taxonomía completa)';
        $choices['category'] = '📁 Categories (taxonomía completa)';
        $choices['post_tag'] = '🏷️ Tags (taxonomía completa)';

        // Add individual terms from each taxonomy
        $choices = array_merge($choices, $this->get_taxonomy_term_choices('package_type', 'Package Type'));
        $choices = array_merge($choices, $this->get_taxonomy_term_choices('interest', 'Interest'));
        $choices = array_merge($choices, $this->get_taxonomy_term_choices('category', 'Category'));
        $choices = array_merge($choices, $this->get_taxonomy_term_choices('post_tag', 'Tag'));

        // Add location posts
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
     * Get taxonomy term choices with label suffix.
     *
     * Helper for load_selected_terms_for_override().
     *
     * @param string $taxonomy Taxonomy slug
     * @param string $label    Label suffix for display
     *
     * @return array Array of term_id => "term_name (label)"
     */
    private function get_taxonomy_term_choices(string $taxonomy, string $label): array
    {
        $choices = [];

        $terms = get_terms([
            'taxonomy' => $taxonomy,
            'hide_empty' => false,
            'orderby' => 'name',
        ]);

        if (!empty($terms) && !is_wp_error($terms)) {
            foreach ($terms as $term) {
                $choices[$term->term_id] = $term->name . ' (' . $label . ')';
            }
        }

        return $choices;
    }

    /**
     * Reconstruct repeater array from flattened block data.
     *
     * ACF stores repeater data in a flattened format:
     * - tt_tab_overrides = count of rows
     * - tt_tab_overrides_0_term_id, tt_tab_overrides_0_custom_name, etc.
     *
     * This method rebuilds the nested array structure.
     *
     * @param array $block_data    Block data from Gutenberg
     * @param string $repeater_name Repeater field name
     * @param array $subfields      Array of subfield names
     *
     * @return array Reconstructed repeater array
     */
    private function reconstruct_repeater_from_block_data($block_data, $repeater_name, $subfields): array
    {
        if (!isset($block_data[$repeater_name]) || !is_numeric($block_data[$repeater_name])) {
            return [];
        }

        $count = intval($block_data[$repeater_name]);
        if ($count === 0) {
            return [];
        }

        $result = [];

        for ($i = 0; $i < $count; $i++) {
            $row = [];

            foreach ($subfields as $subfield) {
                $key = "{$repeater_name}_{$i}_{$subfield}";
                if (isset($block_data[$key])) {
                    $row[$subfield] = $block_data[$key];
                }
            }

            if (!empty($row)) {
                $result[] = $row;
            }
        }

        return $result;
    }

    /**
     * Prepare icon data from attachment ID.
     *
     * Converts attachment ID to array format expected by template.
     *
     * @param int|string $icon_id The attachment ID
     *
     * @return array|null Icon data with url, alt, mime_type and path, or null if invalid
     */
    private function prepare_icon_data($icon_id): ?array
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
}
