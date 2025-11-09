<?php
/**
 * Custom Post Type: Guide
 *
 * Guías y equipo de trabajo
 *
 * @package Aurora\ContentKit\PostTypes
 * @since 1.0.0
 */

namespace Aurora\ContentKit\PostTypes;

use Aurora\ContentKit\Core\CustomPostTypeBase;

class Guide extends CustomPostTypeBase
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->post_type = 'guide';
        $this->singular = 'Guide';
        $this->plural = 'Guides';
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
                'slug'       => 'our-team',
                'with_front' => false,
            ],
            'menu_icon'     => 'dashicons-groups',
            'menu_position' => 24,
            'supports'      => ['title', 'editor', 'thumbnail'],
            'has_archive'   => false, // No necesita archive público
            'public'        => true,
            'show_in_menu'  => true,
        ]);

        register_post_type($this->post_type, $args);
    }
}
