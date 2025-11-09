<?php
namespace Aurora\ContentKit\Core;

abstract class TaxonomyType implements ServiceInterface
{
    protected string $singular;
    protected string $plural;
    protected string $slug;
    protected array $objectTypes;

    public function __construct(string $singular, string $plural, string $slug, array $objectTypes)
    {
        $this->singular    = $singular;
        $this->plural      = $plural;
        $this->slug        = $slug;
        $this->objectTypes = $objectTypes;
    }

    public function labels(string $text_domain): array
    {
        return [
            'name'              => _x($this->plural, 'taxonomy general name', $text_domain),
            'singular_name'     => _x($this->singular, 'taxonomy singular name', $text_domain),
            'search_items'      => sprintf(__('Search %s', $text_domain), $this->plural),
            'all_items'         => sprintf(__('All %s', $text_domain), $this->plural),
            'parent_item'       => sprintf(__('Parent %s', $text_domain), $this->singular),
            'parent_item_colon' => sprintf(__('Parent %s:', $text_domain), $this->singular),
            'edit_item'         => sprintf(__('Edit %s', $text_domain), $this->singular),
            'update_item'       => sprintf(__('Update %s', $text_domain), $this->singular),
            'add_new_item'      => sprintf(__('Add New %s', $text_domain), $this->singular),
            'new_item_name'     => sprintf(__('New %s Name', $text_domain), $this->singular),
            'menu_name'         => _x($this->plural, 'taxonomy menu name', $text_domain),
        ];
    }
}
