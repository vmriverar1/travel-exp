<?php
namespace AcfSearchBar;

class Plugin {
    public function init() : void {
        add_action('acf/init', [$this, 'registerBlock']);
        add_action('wp_enqueue_scripts', [$this, 'registerAssets']);
        add_action('enqueue_block_assets', [$this, 'registerAssets']); // editor y front

        // ACF JSON: cargar desde este plugin
        add_filter('acf/settings/load_json', function($paths){
            $paths[] = plugin_dir_path(__FILE__) . '../acf-json';
            return $paths;
        });
    }

    public function registerAssets() : void {
        $url = plugin_dir_url(__FILE__) . '../assets/';
        $ver = '1.0.0';

        // Estilos propios
        wp_register_style('asb-style', $url . 'css/search-bar.css', [], $ver);

        // Select2 (CDN liviano con fallback simple)
        wp_register_style('select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css', [], '4.1.0-rc.0');
        wp_register_script('select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.full.min.js', ['jquery'], '4.1.0-rc.0', true);

        // Flatpickr
        wp_register_style('flatpickr', 'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css', [], '4.6.13');
        wp_register_script('flatpickr', 'https://cdn.jsdelivr.net/npm/flatpickr', [], '4.6.13', true);

        // JS propio
        wp_register_script('asb-script', $url . 'js/search-bar.js', ['jquery', 'select2', 'flatpickr'], $ver, true);
    }

    public function registerBlock() : void {
        acf_register_block_type([
            'name'              => 'acf-search-bar',
            'title'             => __('Search Bar (Destinations)', 'acf-search-bar'),
            'description'       => __('Barra de búsqueda para taxonomy "destinations".', 'acf-search-bar'),
            'category'          => 'widgets',
            'icon'              => 'search',
            'keywords'          => ['search', 'destinations', 'finder', 'travel'],
            'mode'              => 'preview',
            'render_callback'   => [$this, 'renderBlock'],
            'enqueue_assets'    => function() {
                wp_enqueue_style('select2');
                wp_enqueue_style('flatpickr');
                wp_enqueue_style('asb-style');
                wp_enqueue_script('select2');
                wp_enqueue_script('flatpickr');
                wp_enqueue_script('asb-script');
            },
            'supports'          => [
                'align' => ['wide', 'full'],
                'anchor' => true,
                'customClassName' => true
            ]
        ]);
    }

    public function renderBlock(array $block, $content = '', $is_preview = false, $post_id = 0) : void {
        // Valores de ACF
        $ph_where  = get_field('placeholder_where') ?: 'Where...';
        $ph_when   = get_field('placeholder_when') ?: 'When...';
        $btn_color = get_field('color_button') ?: '#e58b84';
        $y_start   = intval(get_field('year_start') ?: date('Y')-1);
        $y_end     = intval(get_field('year_end') ?: date('Y')+3);

        // Obtener términos de taxonomy destinations
        $terms = get_terms([
            'taxonomy' => 'destinations',
            'hide_empty' => false,
            'number' => 500,
        ]);

        $form_id = 'asb_' . uniqid();
        $action  = esc_url(home_url('/'));
        ?>
        <form id="<?php echo esc_attr($form_id); ?>" class="asb-search-bar" action="<?php echo $action; ?>" method="get" data-year-start="<?php echo esc_attr($y_start); ?>" data-year-end="<?php echo esc_attr($y_end); ?>">
            <input type="hidden" name="s" value="" />

            <div class="asb-inner">
                <div class="asb-field asb-where">
                    <span class="asb-icon" aria-hidden="true"><?php echo self::iconPin(); ?></span>
                    <select class="asb-select2" name="destination" data-placeholder="<?php echo esc_attr($ph_where); ?>">
                        <option></option>
                        <?php if (!is_wp_error($terms)) :
                            foreach ($terms as $t): ?>
                                <option value="<?php echo esc_attr($t->slug); ?>"><?php echo esc_html($t->name); ?></option>
                            <?php endforeach;
                        endif; ?>
                    </select>
                </div>

                <span class="asb-divider" aria-hidden="true"></span>

                <div class="asb-field asb-when">
                    <span class="asb-icon" aria-hidden="true"><?php echo self::iconCalendar(); ?></span>
                    <input type="text" class="asb-date" name="date" placeholder="<?php echo esc_attr($ph_when); ?>" />
                </div>

                <button class="asb-button" type="submit" style="--asb-btn: <?php echo esc_attr($btn_color); ?>">
                    <?php esc_html_e('Search', 'acf-search-bar'); ?>
                </button>
            </div>
        </form>
        <?php

        // Pasar datos a JS (rango de años)
        wp_localize_script('asb-script', 'ASB_DATA', [
            'formId' => $form_id,
        ]);
    }

    private static function iconPin() : string {
        return '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" width="24" height="24" aria-hidden="true"><path d="M12 2C8.686 2 6 4.686 6 8c0 4.5 6 12 6 12s6-7.5 6-12c0-3.314-2.686-6-6-6zm0 8.5a2.5 2.5 0 110-5 2.5 2.5 0 010 5z" fill="currentColor"/></svg>';
    }
    private static function iconCalendar() : string {
        return '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" width="24" height="24" aria-hidden="true"><path d="M7 2a1 1 0 00-1 1v1H5a2 2 0 00-2 2v2h18V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H8V3a1 1 0 00-1-1zM21 10H3v9a2 2 0 002 2h14a2 2 0 002-2v-9z" fill="currentColor"/></svg>';
    }
}
