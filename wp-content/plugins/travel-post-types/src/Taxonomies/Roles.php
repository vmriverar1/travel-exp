<?php
/**
 * Taxonomy: Roles
 *
 * Roles/Cargos de los colaboradores
 * Jerárquica para permitir organización (ej: Management > CEO, CFO)
 *
 * @package Aurora\ContentKit\Taxonomies
 * @since 1.0.0
 */

namespace Aurora\ContentKit\Taxonomies;

use Aurora\ContentKit\Core\TaxonomyType;

class Roles extends TaxonomyType
{
    public function __construct()
    {
        parent::__construct('Role', 'Roles', 'roles', ['collaborator']);
    }

    public function register(): void
    {
        add_action('init', function () {
            $labels = $this->labels('aurora-content-kit');

            $args = [
                'hierarchical'      => true,
                'labels'            => $labels,
                'show_ui'           => true,
                'show_admin_column' => true,
                'query_var'         => true,
                'rewrite'           => ['slug' => 'role', 'with_front' => false],
                'show_in_rest'      => true,
            ];

            register_taxonomy($this->slug, $this->objectTypes, $args);
        }, 9);
    }
}
