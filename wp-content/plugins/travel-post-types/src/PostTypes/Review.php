<?php
/**
 * Custom Post Type: Review
 *
 * Reseñas y testimonios de clientes
 *
 * @package Aurora\ContentKit\PostTypes
 * @since 1.0.0
 */

namespace Aurora\ContentKit\PostTypes;

use Aurora\ContentKit\Core\CustomPostTypeBase;

class Review extends CustomPostTypeBase
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->post_type = 'review';
        $this->singular = 'Review';
        $this->plural = 'Reviews';
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
                'slug'       => 'reviews',
                'with_front' => false,
            ],
            'menu_icon'     => 'dashicons-star-filled',
            'menu_position' => 23,
            'supports'      => ['title', 'editor', 'thumbnail'],
            'has_archive'   => false, // No necesita archive público
            'public'        => true,
            'show_in_menu'  => true,
        ]);

        register_post_type($this->post_type, $args);
    }
}
