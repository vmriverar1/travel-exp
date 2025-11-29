<?php
/**
 * Custom Post Type: Deal
 *
 * Ofertas y descuentos en tours
 *
 * @package Aurora\ContentKit\PostTypes
 * @since 1.0.0
 */

namespace Aurora\ContentKit\PostTypes;

use Aurora\ContentKit\Core\CustomPostTypeBase;

class Deal extends CustomPostTypeBase
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->post_type = 'deal';
        $this->singular = 'Deal';
        $this->plural = 'Deals';
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
                'slug'       => 'deals',
                'with_front' => false,
            ],
            'menu_icon'     => 'dashicons-tag',
            'menu_position' => 22,
            'supports'      => ['title', 'editor', 'thumbnail'],
            'has_archive'   => true,
        ]);

        register_post_type($this->post_type, $args);
    }
}
