<?php
/**
 * Clase base abstracta para Taxonomías
 *
 * @package Aurora\ContentKit\Core
 * @since 1.0.0
 */

namespace Aurora\ContentKit\Core;

abstract class TaxonomyBase implements ServiceInterface
{
    /**
     * Slug de la taxonomía
     *
     * @var string
     */
    protected string $taxonomy;

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
     * Post types asociados
     *
     * @var array
     */
    protected array $post_types = [];

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
            'name'                       => _x($this->plural, 'Taxonomy General Name', $this->text_domain),
            'singular_name'              => _x($this->singular, 'Taxonomy Singular Name', $this->text_domain),
            'menu_name'                  => __($this->plural, $this->text_domain),
            'all_items'                  => sprintf(__('All %s', $this->text_domain), $this->plural),
            'parent_item'                => sprintf(__('Parent %s', $this->text_domain), $this->singular),
            'parent_item_colon'          => sprintf(__('Parent %s:', $this->text_domain), $this->singular),
            'new_item_name'              => sprintf(__('New %s Name', $this->text_domain), $this->singular),
            'add_new_item'               => sprintf(__('Add New %s', $this->text_domain), $this->singular),
            'edit_item'                  => sprintf(__('Edit %s', $this->text_domain), $this->singular),
            'update_item'                => sprintf(__('Update %s', $this->text_domain), $this->singular),
            'view_item'                  => sprintf(__('View %s', $this->text_domain), $this->singular),
            'separate_items_with_commas' => sprintf(__('Separate %s with commas', $this->text_domain), strtolower($this->plural)),
            'add_or_remove_items'        => sprintf(__('Add or remove %s', $this->text_domain), strtolower($this->plural)),
            'choose_from_most_used'      => sprintf(__('Choose from the most used %s', $this->text_domain), strtolower($this->plural)),
            'popular_items'              => sprintf(__('Popular %s', $this->text_domain), $this->plural),
            'search_items'               => sprintf(__('Search %s', $this->text_domain), $this->plural),
            'not_found'                  => __('Not Found', $this->text_domain),
            'no_terms'                   => sprintf(__('No %s', $this->text_domain), strtolower($this->plural)),
            'items_list'                 => sprintf(__('%s list', $this->text_domain), $this->plural),
            'items_list_navigation'      => sprintf(__('%s list navigation', $this->text_domain), $this->plural),
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
            'show_in_nav_menus'  => true,
            'show_tagcloud'      => false,
            'show_in_quick_edit' => true,
            'show_admin_column'  => true,
            'show_in_rest'       => true, // Gutenberg + REST API
            'hierarchical'       => false, // false = tags, true = categories
        ];
    }

    /**
     * Método abstracto que deben implementar las clases hijas
     *
     * @return void
     */
    abstract public function register(): void;
}
