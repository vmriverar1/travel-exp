<?php
namespace Aurora\ACFKit\Integration;

use Aurora\ACFKit\Core\ServiceInterface;

class JsonSync implements ServiceInterface
{
    public function register(): void
    {
        // Save ACF JSON into the plugin to version-control your field groups
        add_filter('acf/settings/save_json', function ($path) {
            return TRAVEL_ACF_FIELDS_PATH . 'acf-json';
        });

        // Load JSON from plugin + theme (so site-specific overrides are possible)
        add_filter('acf/settings/load_json', function ($paths) {
            $paths[] = TRAVEL_ACF_FIELDS_PATH . 'acf-json';
            return $paths;
        });
    }
}
