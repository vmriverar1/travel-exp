<?php
/**
 * Custom Post Type: Location
 *
 * Ubicaciones de etiquetas - Lugares específicos donde se realizan tours
 * Ejemplos: Machu Picchu, Valle Sagrado, Lago Titicaca, etc.
 *
 * @package Aurora\ContentKit\PostTypes
 * @since 1.0.0
 */

namespace Aurora\ContentKit\PostTypes;

use Aurora\ContentKit\Core\CustomPostTypeBase;

class Location extends CustomPostTypeBase
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->post_type = 'location';
        $this->singular = 'Location';
        $this->plural = 'Locations';
    }

    /**
     * Registra el Custom Post Type
     *
     * @return void
     */
    public function register(): void
    {
        $args = array_merge($this->get_args(), [
            'rewrite' => [
                'slug'       => 'locations',
                'with_front' => false,
            ],
            'menu_icon'     => 'dashicons-location-alt',
            'menu_position' => 21,
            'supports'      => ['title', 'editor', 'thumbnail', 'custom-fields', 'revisions'],
            'has_archive'   => true,
            'show_in_rest'  => true,
        ]);

        register_post_type($this->post_type, $args);
    }
}
