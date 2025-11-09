<?php
/**
 * Clase base abstracta para Custom Post Types
 *
 * @package Aurora\ContentKit\Core
 * @since 1.0.0
 */

namespace Aurora\ContentKit\Core;

abstract class CustomPostTypeBase implements ServiceInterface
{
    /**
     * Slug del Custom Post Type
     *
     * @var string
     */
    protected string $post_type;

    /**
     * Nombre singular
     *
     * @var string
     */
    protected string $singular;

    /**
     * Nombre plural
     *
     * @var string
     */
    protected string $plural;

    /**
     * Text domain para traducciones
     *
     * @var string
     */
    protected string $text_domain = 'travel';

    /**
     * Genera labels automáticamente
     *
     * @return array
     */
    protected function get_labels(): array
    {
        return [
            'name'                  => _x($this->plural, 'Post Type General Name', $this->text_domain),
            'singular_name'         => _x($this->singular, 'Post Type Singular Name', $this->text_domain),
            'menu_name'             => _x($this->plural, 'Admin Menu text', $this->text_domain),
            'name_admin_bar'        => _x($this->singular, 'Add New on Toolbar', $this->text_domain),
            'add_new'               => __('Add New', $this->text_domain),
            'add_new_item'          => sprintf(__('Add New %s', $this->text_domain), $this->singular),
            'new_item'              => sprintf(__('New %s', $this->text_domain), $this->singular),
            'edit_item'             => sprintf(__('Edit %s', $this->text_domain), $this->singular),
            'view_item'             => sprintf(__('View %s', $this->text_domain), $this->singular),
            'all_items'             => sprintf(__('All %s', $this->text_domain), $this->plural),
            'search_items'          => sprintf(__('Search %s', $this->text_domain), $this->plural),
            'parent_item_colon'     => sprintf(__('Parent %s:', $this->text_domain), $this->singular),
            'not_found'             => __('Not found.', $this->text_domain),
            'not_found_in_trash'    => __('Not found in Trash.', $this->text_domain),
            'uploaded_to_this_item' => sprintf(__('Uploaded to this %s', $this->text_domain), strtolower($this->singular)),
            'insert_into_item'      => sprintf(__('Insert into %s', $this->text_domain), strtolower($this->singular)),
            'featured_image'        => __('Featured Image', $this->text_domain),
            'set_featured_image'    => __('Set featured image', $this->text_domain),
            'remove_featured_image' => __('Remove featured image', $this->text_domain),
            'use_featured_image'    => __('Use as featured image', $this->text_domain),
        ];
    }

    /**
     * Obtiene los argumentos por defecto
     * Las clases hijas pueden sobrescribir este método
     *
     * @return array
     */
    protected function get_args(): array
    {
        return [
            'labels'             => $this->get_labels(),
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 20,
            'show_in_rest'       => true, // Gutenberg + REST API
            'supports'           => ['title', 'editor', 'thumbnail', 'excerpt', 'revisions', 'author'],
        ];
    }

    /**
     * Método abstracto que deben implementar las clases hijas
     *
     * @return void
     */
    abstract public function register(): void;
}
