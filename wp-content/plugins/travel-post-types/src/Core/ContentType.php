<?php
namespace Aurora\ContentKit\Core;

abstract class ContentType implements ServiceInterface
{
    /**
     * Human readable singular & plural name.
     */
    protected string $singular;
    protected string $plural;
    protected string $slug;

    public function __construct(string $singular, string $plural, string $slug)
    {
        $this->singular = $singular;
        $this->plural   = $plural;
        $this->slug     = $slug;
    }

    public function labels(string $text_domain): array
    {
        return [
            'name'                  => _x($this->plural, 'Post Type General Name', $text_domain),
            'singular_name'         => _x($this->singular, 'Post Type Singular Name', $text_domain),
            'menu_name'             => _x($this->plural, 'Admin Menu text', $text_domain),
            'name_admin_bar'        => _x($this->singular, 'Add New on Toolbar', $text_domain),
            'add_new'               => __('Add New', $text_domain),
            'add_new_item'          => sprintf(__('Add New %s', $text_domain), $this->singular),
            'new_item'              => sprintf(__('New %s', $text_domain), $this->singular),
            'edit_item'             => sprintf(__('Edit %s', $text_domain), $this->singular),
            'view_item'             => sprintf(__('View %s', $text_domain), $this->singular),
            'all_items'             => sprintf(__('All %s', $text_domain), $this->plural),
            'search_items'          => sprintf(__('Search %s', $text_domain), $this->plural),
            'not_found'             => __('Not found.', $text_domain),
            'not_found_in_trash'    => __('Not found in Trash.', $text_domain),
        ];
    }
}
