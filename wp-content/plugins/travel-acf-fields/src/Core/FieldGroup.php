<?php
namespace Aurora\ACFKit\Core;

abstract class FieldGroup implements ServiceInterface
{
    protected string $key;
    protected string $title;
    protected array $fields = [];
    protected array $location = [];
    protected array $settings = [];

    public function register(): void
    {
        add_action('acf/init', function () {
            if (!function_exists('acf_add_local_field_group')) return;

            acf_add_local_field_group(array_merge([
                'key'      => $this->key,
                'title'    => $this->title,
                'fields'   => $this->fields,
                'location' => $this->location,
            ], $this->settings));
        });
    }
}
