<?php
namespace Aurora\ACFKit\Setup;

use Aurora\ACFKit\Core\ServiceInterface;

class Settings implements ServiceInterface
{
    const OPTION_HOME_HERO_PAGES = 'aurora_acf_kit_home_hero_pages';

    public function register(): void
    {
        add_action('admin_init', [$this, 'register_settings']);
        add_action('admin_menu', [$this, 'add_menu']);
    }

    public function register_settings(): void
    {
        register_setting('aurora_acf_kit', self::OPTION_HOME_HERO_PAGES, [
            'type' => 'string',
            'sanitize_callback' => [$this, 'sanitize_ids']
        ]);

        add_settings_section('aurora_acf_kit_section', __('Targeting', 'aurora-acf-kit'), function () {
            echo '<p>' . esc_html__('Configure which pages should display the example field group (Home Hero).', 'aurora-acf-kit') . '</p>';
        }, 'aurora_acf_kit');

        add_settings_field(self::OPTION_HOME_HERO_PAGES, __('Home Hero Page IDs', 'aurora-acf-kit'), function () {
            $value = get_option(self::OPTION_HOME_HERO_PAGES, '');
            echo '<input type="text" class="regular-text" name="' . esc_attr(self::OPTION_HOME_HERO_PAGES) . '" value="' . esc_attr($value) . '" placeholder="e.g. 2,45,101" />';
            echo '<p class="description">' . esc_html__('Comma-separated list of page IDs. Leave empty to show on all pages.', 'aurora-acf-kit') . '</p>';
        }, 'aurora_acf_kit', 'aurora_acf_kit_section');
    }

    public function add_menu(): void
    {
        add_options_page(
            __('Aurora ACF Kit', 'aurora-acf-kit'),
            __('Aurora ACF Kit', 'aurora-acf-kit'),
            'manage_options',
            'aurora_acf_kit',
            [$this, 'render_page']
        );
    }

    public function render_page(): void
    {
        echo '<div class="wrap"><h1>Aurora ACF Kit</h1>';
        echo '<form method="post" action="options.php">';
        settings_fields('aurora_acf_kit');
        do_settings_sections('aurora_acf_kit');
        submit_button();
        echo '</form></div>';
    }

    public function sanitize_ids($value): string
    {
        if (!is_string($value)) return '';
        $ids = array_filter(array_map('absint', explode(',', $value)));
        return implode(',', $ids);
    }

    public static function get_home_hero_ids(): array
    {
        $value = get_option(self::OPTION_HOME_HERO_PAGES, '');
        if (!$value) return [];
        return array_filter(array_map('absint', explode(',', $value)));
    }
}
