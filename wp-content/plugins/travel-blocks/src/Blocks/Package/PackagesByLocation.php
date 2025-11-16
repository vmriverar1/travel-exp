<?php
namespace Travel\Blocks\Blocks\Package;

use Travel\Blocks\Helpers\ContentQueryHelper;

class PackagesByLocation
{
    private string $name = 'packages-by-location';
    private string $title = 'Packages by Location';
    private string $description = 'Display packages filtered by location/destination';

    public function register(): void
    {
        if (!function_exists('acf_register_block_type')) {
            return;
        }

        // Register ACF fields first
        $this->register_acf_fields();

        // Then register the block
        acf_register_block_type([
            'name' => $this->name,
            'title' => __($this->title, 'travel-blocks'),
            'description' => __($this->description, 'travel-blocks'),
            'category' => 'template-blocks',
            'icon' => 'location',
            'keywords' => ['packages', 'location', 'destination', 'filter', 'archive'],
            'supports' => [
                'anchor' => true,
                'html' => false,
                'align' => ['wide', 'full'],
            ],
            'render_callback' => [$this, 'render'],
        ]);
    }

    public function register_acf_fields(): void
    {
        if (!function_exists('acf_add_local_field_group')) {
            return;
        }

        acf_add_local_field_group([
            'key' => 'group_packages_by_location_block',
            'title' => 'Packages by Location Block Settings',
            'fields' => [
                // Filter Mode
                [
                    'key' => 'field_pbl_filter_mode',
                    'label' => 'Filter Mode',
                    'name' => 'filter_mode',
                    'type' => 'select',
                    'instructions' => 'Choose how to filter packages',
                    'choices' => [
                        'auto' => 'Auto (detect current location)',
                        'manual' => 'Manual (select specific location)',
                    ],
                    'default_value' => 'auto',
                    'ui' => 1,
                ],

                // Manual Location Selection
                [
                    'key' => 'field_pbl_location',
                    'label' => 'Select Location',
                    'name' => 'location',
                    'type' => 'post_object',
                    'instructions' => 'Select a location to filter packages',
                    'post_type' => ['location'],
                    'return_format' => 'id',
                    'ui' => 1,
                    'conditional_logic' => [
                        [
                            [
                                'field' => 'field_pbl_filter_mode',
                                'operator' => '==',
                                'value' => 'manual',
                            ],
                        ],
                    ],
                ],

                // Display Settings
                [
                    'key' => 'field_pbl_tab_display',
                    'label' => '🎨 Display Settings',
                    'type' => 'tab',
                    'placement' => 'top',
                ],

                [
                    'key' => 'field_pbl_section_title',
                    'label' => 'Section Title',
                    'name' => 'section_title',
                    'type' => 'text',
                    'instructions' => 'Optional title for the section',
                    'placeholder' => 'Available Packages',
                ],

                [
                    'key' => 'field_pbl_columns',
                    'label' => 'Columns',
                    'name' => 'columns',
                    'type' => 'select',
                    'instructions' => 'Number of columns in grid',
                    'choices' => [
                        '2' => '2 Columns',
                        '3' => '3 Columns',
                        '4' => '4 Columns',
                    ],
                    'default_value' => '3',
                    'ui' => 1,
                ],

                [
                    'key' => 'field_pbl_posts_per_page',
                    'label' => 'Posts Per Page',
                    'name' => 'posts_per_page',
                    'type' => 'number',
                    'instructions' => 'Number of packages to display',
                    'default_value' => 12,
                    'min' => 1,
                    'max' => 50,
                ],

                [
                    'key' => 'field_pbl_show_pagination',
                    'label' => 'Show Pagination',
                    'name' => 'show_pagination',
                    'type' => 'true_false',
                    'instructions' => 'Display pagination if more packages exist',
                    'default_value' => 1,
                    'ui' => 1,
                ],

                // Card Display Options - Use ContentQueryHelper standard checkbox
                [
                    'key' => 'field_pbl_tab_card',
                    'label' => '🎴 Card Options',
                    'type' => 'tab',
                    'placement' => 'top',
                ],

                [
                    'key' => 'field_pbl_visible_fields',
                    'label' => '👁️ Campos Visibles (Package)',
                    'name' => 'pbl_visible_fields',
                    'type' => 'checkbox',
                    'instructions' => 'Selecciona qué campos del package mostrar en cada card. Desmarca los que quieras ocultar.',
                    'choices' => [
                        'image' => '🖼️ Imagen',
                        'category' => '🏷️ Badge/Categoría',
                        'title' => '📝 Título',
                        'description' => '📄 Descripción',
                        'location' => '📍 Ubicación',
                        'price' => '💰 Precio',
                        'duration' => '⏱️ Duración (días)',
                        'rating' => '⭐ Rating',
                        'group_size' => '👥 Tamaño de Grupo',
                    ],
                    'default_value' => ['image', 'category', 'title', 'description', 'location', 'price', 'duration', 'rating'],
                    'layout' => 'vertical',
                    'toggle' => 1,
                ],
            ],
            'location' => [
                [
                    [
                        'param' => 'block',
                        'operator' => '==',
                        'value' => 'acf/' . $this->name,
                    ],
                ],
            ],
        ]);
    }

    public function render($block, $content = '', $is_preview = false, $post_id = 0): void
    {
        // Get block settings
        $filter_mode = get_field('filter_mode') ?: 'auto';
        $location_id = null;

        // Determine location ID
        if ($filter_mode === 'auto') {
            // Auto-detect: check if we're on a single location page
            if (is_singular('location')) {
                $location_id = get_the_ID();
            }
        } else {
            // Manual: use selected location
            $location_id = get_field('location');
        }

        // If no location found, show message
        if (!$location_id) {
            if ($is_preview) {
                echo '<div style="padding:2rem;background:#f0f0f0;text-align:center">';
                echo '<p>📍 <strong>Packages by Location Block</strong></p>';
                echo '<p>Select a location or use this block on a single location page.</p>';
                echo '</div>';
            }
            return;
        }

        // Get display settings
        $section_title = get_field('section_title');
        $columns = get_field('columns') ?: '3';
        $posts_per_page = get_field('posts_per_page') ?: 12;
        $show_pagination = get_field('show_pagination');

        // Card options - Use checkbox field
        $visible_fields = get_field('pbl_visible_fields') ?: ['image', 'category', 'title', 'description', 'location', 'price', 'duration', 'rating'];

        // Convert to boolean flags for template compatibility
        $show_image = in_array('image', $visible_fields);
        $show_price = in_array('price', $visible_fields);
        $show_duration = in_array('duration', $visible_fields);
        $show_rating = in_array('rating', $visible_fields);
        $show_excerpt = in_array('description', $visible_fields);
        $show_category = in_array('category', $visible_fields);
        $show_location = in_array('location', $visible_fields);
        $excerpt_length = 20; // Default excerpt length

        // Get current page for pagination
        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

        // Query packages by location
        $packages_query = new \WP_Query([
            'post_type' => 'package',
            'posts_per_page' => $posts_per_page,
            'paged' => $paged,
            'post_status' => 'publish',
            'meta_query' => [
                [
                    'key' => 'destination',
                    'value' => $location_id,
                    'compare' => '='
                ]
            ]
        ]);

        // Get location name for display
        $location_name = get_the_title($location_id);

        // Render block
        $block_classes = isset($block['className']) ? $block['className'] : '';
        $block_id = isset($block['anchor']) ? 'id="' . esc_attr($block['anchor']) . '"' : '';
        ?>

        <div <?php echo $block_id; ?> class="packages-by-location-block <?php echo esc_attr($block_classes); ?>" style="padding:3rem 0">

            <?php if ($section_title || $is_preview): ?>
                <div style="max-width:1200px;margin:0 auto 2rem;padding:0 1rem">
                    <h2 style="font-size:2rem;font-weight:700;margin-bottom:0.5rem">
                        <?php echo $section_title ? esc_html($section_title) : 'Packages in ' . esc_html($location_name); ?>
                    </h2>
                    <p style="color:#666">
                        <?php echo $packages_query->found_posts; ?> packages available
                    </p>
                </div>
            <?php endif; ?>

            <div style="max-width:1200px;margin:0 auto;padding:0 1rem">

                <?php if ($packages_query->have_posts()): ?>

                    <div class="packages-grid" style="display:grid;grid-template-columns:repeat(<?php echo esc_attr($columns); ?>,1fr);gap:2rem;margin-bottom:2rem">

                        <?php while ($packages_query->have_posts()): $packages_query->the_post(); ?>

                            <div class="package-card" style="background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.1);transition:transform 0.3s">

                                <?php if ($show_image && has_post_thumbnail()): ?>
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_post_thumbnail('large', ['style' => 'width:100%;height:250px;object-fit:cover']); ?>
                                    </a>
                                <?php endif; ?>

                                <div style="padding:1.5rem">
                                    <h3 style="margin-bottom:0.75rem;font-size:1.25rem;line-height:1.3">
                                        <a href="<?php the_permalink(); ?>" style="text-decoration:none;color:#333">
                                            <?php the_title(); ?>
                                        </a>
                                    </h3>

                                    <?php
                                    $duration = get_field('duration');
                                    $price_from = get_field('price_from');
                                    $rating = get_field('rating');
                                    ?>

                                    <div style="display:flex;gap:1rem;flex-wrap:wrap;margin-bottom:0.75rem;font-size:0.875rem;color:#666">
                                        <?php if ($show_duration && $duration): ?>
                                            <span>📅 <?php echo esc_html($duration); ?></span>
                                        <?php endif; ?>

                                        <?php if ($show_rating && $rating): ?>
                                            <span>⭐ <?php echo esc_html($rating); ?>/5</span>
                                        <?php endif; ?>
                                    </div>

                                    <?php if ($show_excerpt && has_excerpt()): ?>
                                        <div style="margin-bottom:1rem;font-size:0.9rem;color:#555;line-height:1.5">
                                            <?php echo wp_trim_words(get_the_excerpt(), $excerpt_length); ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($show_price && $price_from): ?>
                                        <p style="font-size:1.5rem;font-weight:700;color:#0073aa;margin-bottom:1rem">
                                            From $<?php echo number_format($price_from); ?>
                                        </p>
                                    <?php endif; ?>

                                    <a href="<?php the_permalink(); ?>" class="wp-block-button__link wp-element-button" style="display:block;text-align:center;padding:0.75rem 1.5rem;background:#0073aa;color:#fff;text-decoration:none;border-radius:4px">
                                        View Details
                                    </a>
                                </div>

                            </div>

                        <?php endwhile; ?>

                    </div>

                    <?php if ($show_pagination && $packages_query->max_num_pages > 1): ?>
                        <div style="text-align:center;margin-top:2rem">
                            <?php
                            echo paginate_links([
                                'total' => $packages_query->max_num_pages,
                                'current' => $paged,
                                'prev_text' => '← Previous',
                                'next_text' => 'Next →',
                            ]);
                            ?>
                        </div>
                    <?php endif; ?>

                <?php else: ?>

                    <div style="text-align:center;padding:3rem;background:#f9f9f9;border-radius:8px">
                        <p style="font-size:1.125rem;color:#666">
                            No packages found for <?php echo esc_html($location_name); ?>. Check back soon!
                        </p>
                    </div>

                <?php endif; ?>

                <?php wp_reset_postdata(); ?>

            </div>

        </div>

        <?php
    }
}
