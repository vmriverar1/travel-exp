<?php
/**
 * Block: Sticky Side Menu
 *
 * Floating sticky side menu with phone, CTA button, and hamburger menu trigger.
 * Appears after configurable scroll offset with dynamic positioning (vh/px/%).
 *
 * Features:
 * - Phone display with optional icon
 * - Customizable CTA button (6 style variants)
 * - Hamburger menu integration with header aside menu
 * - Configurable sticky behavior (offset: vh/px/%)
 * - Shadow intensity control
 * - Responsive with mobile hide option
 *
 * Integration:
 * - Requires Header Aside Menu (#aside-menu)
 * - Uses window.asideMenuToggle() for hamburger functionality
 * - JavaScript provides fallback if global function not available
 *
 * @package Travel\Blocks\ACF
 * @since 1.0.0
 * @version 1.1.1 - Enhanced: critical dependency documentation for header integration
 */

namespace Travel\Blocks\Blocks\ACF;

use Travel\Core\BlockBase;

class StickySideMenu extends BlockBase
{
    public function __construct()
    {
        $this->name        = 'sticky-side-menu';
        $this->title       = __('Sticky Side Menu', 'travel-blocks');
        $this->description = __('Menú lateral pegado a la derecha con teléfono, CTA y hamburguesa. Comportamiento sticky configurable.', 'travel-blocks');
        $this->category    = 'travel';
        $this->icon        = 'menu-alt';
        $this->keywords    = ['sticky', 'side', 'menu', 'hamburger', 'cta', 'phone'];
        $this->mode        = 'preview';

        $this->supports = [
            'align' => false, // No necesita alineación porque flota a la derecha
            'mode'  => true,
            'multiple' => true,
            'anchor' => true,
        ];
    }

    /**
     * Enqueue block-specific assets.
     *
     * Loads CSS for sticky menu styling and JavaScript for:
     * - Sticky behavior with dynamic offset calculation
     * - Hamburger menu integration with header aside menu
     * - Responsive handling and mobile detection
     *
     * @return void
     */
    public function enqueue_assets(): void
    {
        // Enqueue CSS
        wp_enqueue_style(
            'sticky-side-menu-style',
            TRAVEL_BLOCKS_URL . 'assets/blocks/sticky-side-menu.css',
            [],
            TRAVEL_BLOCKS_VERSION
        );

        // Enqueue JS
        wp_enqueue_script(
            'sticky-side-menu-script',
            TRAVEL_BLOCKS_URL . 'assets/blocks/sticky-side-menu.js',
            [],
            TRAVEL_BLOCKS_VERSION,
            true
        );
    }

    /**
     * Register block and its ACF fields.
     *
     * Registers ACF block type and defines field groups organized in tabs:
     * - Phone: Toggle, number, and icon display
     * - CTA Button: Text, URL, and 6 style variants
     * - Hamburger: Toggle for aside menu integration
     * - Positioning: Scroll offset (vh/px/%) for sticky appearance
     * - Styles: Shadow intensity and mobile visibility
     *
     * @return void
     */
    public function register(): void
    {
        parent::register();

        // Register ACF fields for this block
        if (function_exists('acf_add_local_field_group')) {
            acf_add_local_field_group([
                'key' => 'group_block_sticky_side_menu',
                'title' => __('Sticky Side Menu - Configuración', 'travel-blocks'),
                'fields' => [

                    // ===== TAB: TELÉFONO =====
                    [
                        'key' => 'field_ssm_tab_phone',
                        'label' => __('📞 Teléfono', 'travel-blocks'),
                        'type' => 'tab',
                        'placement' => 'top',
                    ],

                    [
                        'key' => 'field_ssm_show_phone',
                        'label' => __('Mostrar Teléfono', 'travel-blocks'),
                        'name' => 'show_phone',
                        'type' => 'true_false',
                        'default_value' => 1,
                        'ui' => 1,
                        'instructions' => __('Activar/desactivar la sección de teléfono', 'travel-blocks'),
                    ],

                    [
                        'key' => 'field_ssm_phone_number',
                        'label' => __('Número de Teléfono', 'travel-blocks'),
                        'name' => 'phone_number',
                        'type' => 'text',
                        'default_value' => '+51 999 999 999',
                        'required' => 0,
                        'placeholder' => '+51 999 999 999',
                        'conditional_logic' => [
                            [
                                [
                                    'field' => 'field_ssm_show_phone',
                                    'operator' => '==',
                                    'value' => '1',
                                ],
                            ],
                        ],
                    ],

                    [
                        'key' => 'field_ssm_phone_icon',
                        'label' => __('Mostrar Ícono de Teléfono', 'travel-blocks'),
                        'name' => 'phone_icon',
                        'type' => 'true_false',
                        'default_value' => 1,
                        'ui' => 1,
                        'conditional_logic' => [
                            [
                                [
                                    'field' => 'field_ssm_show_phone',
                                    'operator' => '==',
                                    'value' => '1',
                                ],
                            ],
                        ],
                    ],

                    // ===== TAB: BOTÓN CTA =====
                    [
                        'key' => 'field_ssm_tab_cta',
                        'label' => __('🔘 Botón CTA', 'travel-blocks'),
                        'type' => 'tab',
                        'placement' => 'top',
                    ],

                    [
                        'key' => 'field_ssm_show_cta',
                        'label' => __('Mostrar Botón CTA', 'travel-blocks'),
                        'name' => 'show_cta',
                        'type' => 'true_false',
                        'default_value' => 1,
                        'ui' => 1,
                        'instructions' => __('Activar/desactivar el botón CTA', 'travel-blocks'),
                    ],

                    [
                        'key' => 'field_ssm_cta_text',
                        'label' => __('Texto del Botón', 'travel-blocks'),
                        'name' => 'cta_text',
                        'type' => 'text',
                        'default_value' => 'Contactar',
                        'required' => 0,
                        'maxlength' => 30,
                        'conditional_logic' => [
                            [
                                [
                                    'field' => 'field_ssm_show_cta',
                                    'operator' => '==',
                                    'value' => '1',
                                ],
                            ],
                        ],
                    ],

                    [
                        'key' => 'field_ssm_cta_url',
                        'label' => __('URL del Botón', 'travel-blocks'),
                        'name' => 'cta_url',
                        'type' => 'url',
                        'required' => 0,
                        'placeholder' => 'https://example.com/contact',
                        'conditional_logic' => [
                            [
                                [
                                    'field' => 'field_ssm_show_cta',
                                    'operator' => '==',
                                    'value' => '1',
                                ],
                            ],
                        ],
                    ],

                    [
                        'key' => 'field_ssm_cta_style',
                        'label' => __('Estilo del Botón', 'travel-blocks'),
                        'name' => 'cta_style',
                        'type' => 'select',
                        'choices' => [
                            'primary' => __('Primario - Rosa (#E78C85)', 'travel-blocks'),
                            'secondary' => __('Secundario - Morado (#311A42)', 'travel-blocks'),
                            'white' => __('Blanco con texto negro', 'travel-blocks'),
                            'gold' => __('Dorado (#CEA02D)', 'travel-blocks'),
                            'dark' => __('Negro (#1A1A1A)', 'travel-blocks'),
                            'transparent' => __('Transparente con borde blanco', 'travel-blocks'),
                        ],
                        'default_value' => 'primary',
                        'ui' => 1,
                        'conditional_logic' => [
                            [
                                [
                                    'field' => 'field_ssm_show_cta',
                                    'operator' => '==',
                                    'value' => '1',
                                ],
                            ],
                        ],
                    ],

                    // ===== TAB: MENÚ HAMBURGUESA =====
                    [
                        'key' => 'field_ssm_tab_menu',
                        'label' => __('🍔 Menú Hamburguesa', 'travel-blocks'),
                        'type' => 'tab',
                        'placement' => 'top',
                    ],

                    [
                        'key' => 'field_ssm_show_hamburger',
                        'label' => __('Mostrar Hamburguesa', 'travel-blocks'),
                        'name' => 'show_hamburger',
                        'type' => 'true_false',
                        'default_value' => 1,
                        'ui' => 1,
                        'instructions' => __('Activar/desactivar el botón hamburguesa. Al hacer click, abrirá el menú aside principal del header.', 'travel-blocks'),
                    ],

                    // ===== TAB: POSICIONAMIENTO =====
                    [
                        'key' => 'field_ssm_tab_position',
                        'label' => __('📍 Posicionamiento', 'travel-blocks'),
                        'type' => 'tab',
                        'placement' => 'top',
                    ],

                    [
                        'key' => 'field_ssm_offset_value',
                        'label' => __('Altura de Aparición', 'travel-blocks'),
                        'name' => 'offset_value',
                        'type' => 'number',
                        'default_value' => 20,
                        'min' => 0,
                        'max' => 1000,
                        'step' => 1,
                        'instructions' => __('Altura de scroll donde el menú aparece pegado arriba (top: 0). Ejemplo: 20vh = aparece después de scrollear 20% de la altura de pantalla.', 'travel-blocks'),
                    ],

                    [
                        'key' => 'field_ssm_offset_unit',
                        'label' => __('Unidad de Medida', 'travel-blocks'),
                        'name' => 'offset_unit',
                        'type' => 'select',
                        'choices' => [
                            'vh' => __('vh (viewport height - % de altura de pantalla)', 'travel-blocks'),
                            'px' => __('px (píxeles)', 'travel-blocks'),
                            '%' => __('% (porcentaje del contenedor)', 'travel-blocks'),
                        ],
                        'default_value' => 'vh',
                        'ui' => 1,
                        'instructions' => __('Unidad para altura de aparición', 'travel-blocks'),
                    ],

                    // ===== TAB: ESTILOS =====
                    [
                        'key' => 'field_ssm_tab_styles',
                        'label' => __('🎨 Estilos', 'travel-blocks'),
                        'type' => 'tab',
                        'placement' => 'top',
                    ],

                    [
                        'key' => 'field_ssm_shadow_intensity',
                        'label' => __('Intensidad de Sombra', 'travel-blocks'),
                        'name' => 'shadow_intensity',
                        'type' => 'range',
                        'default_value' => 5,
                        'min' => 1,
                        'max' => 10,
                        'step' => 1,
                        'instructions' => __('Intensidad del sombreado hacia abajo-derecha', 'travel-blocks'),
                    ],

                    [
                        'key' => 'field_ssm_hide_mobile',
                        'label' => __('Ocultar en Mobile', 'travel-blocks'),
                        'name' => 'hide_mobile',
                        'type' => 'true_false',
                        'default_value' => 0,
                        'ui' => 1,
                        'instructions' => __('Ocultar el menú en pantallas menores a 768px', 'travel-blocks'),
                    ],
                ],
                'location' => [
                    [
                        [
                            'param' => 'block',
                            'operator' => '==',
                            'value' => 'acf/sticky-side-menu',
                        ],
                    ],
                ],
            ]);
        }
    }

    /**
     * Render the block output.
     *
     * Generates floating sticky side menu with:
     * - Phone section: Displays phone number with optional icon
     * - CTA Button: Customizable button with 6 style variants
     * - Hamburger Menu: Trigger for header aside menu integration
     *
     * Sticky Behavior:
     * - Menu appears when scroll offset reached (configurable in vh/px/%)
     * - JavaScript controls visibility with .is-visible class
     * - Shadow intensity and mobile visibility configurable
     *
     * ⚠️ Integration Requirements (CRITICAL DEPENDENCY):
     * - Requires #aside-menu element in header (DOM dependency)
     * - Hamburger triggers window.asideMenuToggle() function
     * - JavaScript provides fallback if global function not available
     * - See sticky-side-menu.js lines 85-120 for integration logic
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
        // Enqueue assets when block is rendered
        $this->enqueue_assets();

        // Get all settings
        $show_phone = get_field('show_phone') ?? true;
        $phone_number = get_field('phone_number') ?: '+51 999 999 999';
        $phone_icon = get_field('phone_icon') ?? true;

        $show_cta = get_field('show_cta') ?? true;
        $cta_text = get_field('cta_text') ?: 'Contactar';
        $cta_url = get_field('cta_url') ?: '#';
        $cta_style = get_field('cta_style') ?: 'primary';

        $show_hamburger = get_field('show_hamburger') ?? true;

        $offset_value = get_field('offset_value') ?: 20;
        $offset_unit = get_field('offset_unit') ?: 'vh';
        $shadow_intensity = get_field('shadow_intensity') ?: 5;
        $hide_mobile = get_field('hide_mobile') ?? false;

        // Block attributes
        $block_id = 'ssm-' . ($block['id'] ?? uniqid());

        // Pass data to template
        $data = [
            'block_id' => $block_id,
            'show_phone' => $show_phone,
            'phone_number' => $phone_number,
            'phone_icon' => $phone_icon,
            'show_cta' => $show_cta,
            'cta_text' => $cta_text,
            'cta_url' => $cta_url,
            'cta_style' => $cta_style,
            'show_hamburger' => $show_hamburger,
            'offset_value' => $offset_value,
            'offset_unit' => $offset_unit,
            'shadow_intensity' => $shadow_intensity,
            'hide_mobile' => $hide_mobile,
            'is_preview' => $is_preview,
        ];

        $this->load_template('sticky-side-menu', $data);
    }
}
