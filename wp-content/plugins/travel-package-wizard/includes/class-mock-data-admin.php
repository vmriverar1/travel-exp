<?php
/**
 * Admin page for generating mock data
 */

if (!defined('ABSPATH')) {
    exit;
}

class Aurora_Mock_Data_Admin
{
    private static $instance = null;

    public static function get_instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
        add_action('admin_menu', [$this, 'add_admin_page']);

        // Taxonomy generation
        add_action('admin_post_aurora_generate_taxonomies', [$this, 'handle_generate_taxonomies']);

        // Location CPT actions
        add_action('admin_post_aurora_generate_locations', [$this, 'handle_generate_locations']);

        // Collaborator CPT actions
        add_action('admin_post_aurora_generate_collaborators', [$this, 'handle_generate_collaborators']);

        // Guide CPT actions
        add_action('admin_post_aurora_generate_guides', [$this, 'handle_generate_guides']);

        // Review CPT actions
        add_action('admin_post_aurora_generate_reviews', [$this, 'handle_generate_reviews']);

        // Deal CPT actions
        add_action('admin_post_aurora_generate_deals', [$this, 'handle_generate_deals']);

        // Package actions
        add_action('admin_post_aurora_generate_mock', [$this, 'handle_generate']);
        add_action('admin_post_aurora_add_images', [$this, 'handle_add_images']);
        add_action('admin_post_aurora_delete_all', [$this, 'handle_delete']);

        // FASE 8D: Image actions for other CPTs
        add_action('admin_post_aurora_add_images_deals', [$this, 'handle_add_images_deals']);
        add_action('admin_post_aurora_add_images_locations', [$this, 'handle_add_images_locations']);
        add_action('admin_post_aurora_add_images_guides', [$this, 'handle_add_images_guides']);
        add_action('admin_post_aurora_add_images_reviews', [$this, 'handle_add_images_reviews']);
        add_action('admin_post_aurora_add_images_collaborators', [$this, 'handle_add_images_collaborators']);
        add_action('admin_post_aurora_add_images_destinations', [$this, 'handle_add_images_destinations']);
        add_action('admin_post_aurora_add_images_taxonomies', [$this, 'handle_add_images_taxonomies']);
        add_action('admin_post_aurora_add_all_images', [$this, 'handle_add_all_images']);

        // Header/footer actions
        add_action('admin_post_aurora_generate_header', [$this, 'handle_generate_header']);
        add_action('admin_post_aurora_generate_menus', [$this, 'handle_generate_menus']);
        add_action('admin_post_aurora_generate_footer', [$this, 'handle_generate_footer']);
        add_action('admin_post_aurora_generate_footer_menus', [$this, 'handle_generate_footer_menus']);

        // Blog posts actions
        add_action('admin_post_aurora_generate_blog_posts', [$this, 'handle_generate_blog_posts']);
        add_action('admin_post_aurora_add_blog_images', [$this, 'handle_add_blog_images']);
        add_action('admin_post_aurora_delete_blog_posts', [$this, 'handle_delete_blog_posts']);

        // FASE 9A: Wizard AJAX actions
        add_action('wp_ajax_aurora_wizard_process_batch', [$this, 'handle_wizard_process_batch']);
        add_action('wp_ajax_aurora_wizard_get_stats', [$this, 'handle_wizard_get_stats']);
        add_action('wp_ajax_aurora_wizard_cleanup', [$this, 'handle_wizard_cleanup']);

        // FASE 9A: Enqueue wizard assets
        add_action('admin_enqueue_scripts', [$this, 'enqueue_wizard_assets']);
    }

    /**
     * Add admin menu page
     */
    public function add_admin_page()
    {
        add_submenu_page(
            'edit.php?post_type=package',
            'Mock Data Generator',
            '🎲 Mock Data',
            'manage_options',
            'package-mock-data',
            [$this, 'render_page']
        );
    }

    /**
     * Render admin page
     */
    public function render_page()
    {
        // Show error messages
        if (isset($_GET['error'])) {
            $error_message = urldecode($_GET['error']);
            echo '<div class="notice notice-error is-dismissible"><p>';
            echo '❌ ' . esc_html($error_message);
            echo '</p></div>';
        }

        // Show success messages
        if (isset($_GET['generated'])) {
            $created = intval($_GET['generated']);
            $total = intval($_GET['total']);
            echo '<div class="notice notice-success is-dismissible"><p>';
            echo sprintf('✅ Successfully created %d of %d packages (without images - use "Add Images" button below)!', $created, $total);
            echo '</p></div>';
        }

        if (isset($_GET['images_added'])) {
            $updated = intval($_GET['images_added']);
            $total = intval($_GET['images_total']);
            $fixed = isset($_GET['images_fixed']) ? intval($_GET['images_fixed']) : 0;
            echo '<div class="notice notice-success is-dismissible"><p>';
            if ($fixed > 0) {
                echo sprintf('🖼️ Successfully processed %d packages! Fixed %d broken images (404s) and added missing images.', $updated, $fixed);
            } else {
                echo sprintf('🖼️ Successfully added images to %d packages!', $updated);
            }
            echo '</p></div>';
        }

        // FASE 8D: Success messages for other CPTs
        if (isset($_GET['deals_images_added'])) {
            echo '<div class="notice notice-success is-dismissible"><p>';
            echo sprintf('💰 Successfully added images to %d deals!', intval($_GET['deals_images_added']));
            echo '</p></div>';
        }

        if (isset($_GET['locations_images_added'])) {
            echo '<div class="notice notice-success is-dismissible"><p>';
            echo sprintf('📍 Successfully added images to %d locations!', intval($_GET['locations_images_added']));
            echo '</p></div>';
        }

        if (isset($_GET['guides_images_added'])) {
            echo '<div class="notice notice-success is-dismissible"><p>';
            echo sprintf('👨‍🏫 Successfully added images to %d guides!', intval($_GET['guides_images_added']));
            echo '</p></div>';
        }

        if (isset($_GET['reviews_images_added'])) {
            echo '<div class="notice notice-success is-dismissible"><p>';
            echo sprintf('⭐ Successfully added images to %d reviews!', intval($_GET['reviews_images_added']));
            echo '</p></div>';
        }

        if (isset($_GET['collaborators_images_added'])) {
            echo '<div class="notice notice-success is-dismissible"><p>';
            echo sprintf('👥 Successfully added images to %d collaborators!', intval($_GET['collaborators_images_added']));
            echo '</p></div>';
        }

        if (isset($_GET['destinations_images_added'])) {
            echo '<div class="notice notice-success is-dismissible"><p>';
            echo sprintf('🗺️ Successfully added images to %d destinations!', intval($_GET['destinations_images_added']));
            echo '</p></div>';
        }

        if (isset($_GET['taxonomies_images_added'])) {
            echo '<div class="notice notice-success is-dismissible"><p>';
            echo sprintf('🏷️ Successfully added images to %d taxonomy terms!', intval($_GET['taxonomies_images_added']));
            echo '</p></div>';
        }

        if (isset($_GET['all_images_added'])) {
            $updated = intval($_GET['all_images_added']);
            $total = intval($_GET['all_images_total']);
            echo '<div class="notice notice-success is-dismissible"><p>';
            echo sprintf('🎨 Successfully added images to %d items across ALL CPTs and Taxonomies! (Total processed: %d)', $updated, $total);
            echo '</p></div>';
        }

        if (isset($_GET['deleted'])) {
            $deleted = intval($_GET['deleted']);
            echo '<div class="notice notice-warning is-dismissible"><p>';
            echo sprintf('🗑️ Deleted %d packages.', $deleted);
            echo '</p></div>';
        }

        if (isset($_GET['header_generated'])) {
            $success = intval($_GET['header_generated']);
            if ($success) {
                echo '<div class="notice notice-success is-dismissible"><p>';
                echo '✅ Header data generated successfully! Check your header in the frontend.';
                echo '</p></div>';
            } else {
                echo '<div class="notice notice-error is-dismissible"><p>';
                echo '❌ Failed to generate header data. Make sure ACF is active.';
                echo '</p></div>';
            }
        }

        if (isset($_GET['menus_generated'])) {
            $success = intval($_GET['menus_generated']);
            if ($success) {
                echo '<div class="notice notice-success is-dismissible"><p>';
                echo '✅ Navigation menus created successfully! Go to <a href="' . admin_url('nav-menus.php') . '">Appearance → Menus</a> to view them.';
                echo '</p></div>';
            } else {
                echo '<div class="notice notice-error is-dismissible"><p>';
                echo '❌ Failed to generate menus.';
                echo '</p></div>';
            }
        }

        if (isset($_GET['footer_generated'])) {
            $success = intval($_GET['footer_generated']);
            if ($success) {
                echo '<div class="notice notice-success is-dismissible"><p>';
                echo '✅ Footer data generated successfully! Check your footer in the frontend.';
                echo '</p></div>';
            } else {
                echo '<div class="notice notice-error is-dismissible"><p>';
                echo '❌ Failed to generate footer data. Make sure ACF is active.';
                echo '</p></div>';
            }
        }

        if (isset($_GET['footer_menus_generated'])) {
            $success = intval($_GET['footer_menus_generated']);
            if ($success) {
                echo '<div class="notice notice-success is-dismissible"><p>';
                echo '✅ Footer menus created successfully! 6 menus created: Top Experiences, Treks, Culture, Destinations, About, Extra Info.';
                echo '</p></div>';
            } else {
                echo '<div class="notice notice-error is-dismissible"><p>';
                echo '❌ Failed to generate footer menus.';
                echo '</p></div>';
            }
        }

        // Get current packages count
        $packages_count = wp_count_posts('package');
        $total_packages = $packages_count->publish + $packages_count->draft + $packages_count->pending;

        // Get current blog posts count
        $posts_count = wp_count_posts('post');
        $total_posts = $posts_count->publish + $posts_count->draft + $posts_count->pending;

        // Show blog posts success messages
        if (isset($_GET['blog_generated'])) {
            $created = intval($_GET['blog_generated']);
            $total = intval($_GET['blog_total']);
            echo '<div class="notice notice-success is-dismissible"><p>';
            echo sprintf('✅ Successfully created %d of %d blog posts (without images - use "Add Images" button below)!', $created, $total);
            echo '</p></div>';
        }

        if (isset($_GET['blog_images_added'])) {
            $updated = intval($_GET['blog_images_added']);
            echo '<div class="notice notice-success is-dismissible"><p>';
            echo sprintf('🖼️ Successfully added images to %d blog posts!', $updated);
            echo '</p></div>';
        }

        if (isset($_GET['blog_deleted'])) {
            $deleted = intval($_GET['blog_deleted']);
            echo '<div class="notice notice-warning is-dismissible"><p>';
            echo sprintf('🗑️ Deleted %d blog posts.', $deleted);
            echo '</p></div>';
        }

        if (isset($_GET['taxonomies_created'])) {
            $created = intval($_GET['taxonomies_created']);
            echo '<div class="notice notice-success is-dismissible"><p>';
            echo sprintf('✅ Successfully created %d taxonomy terms across all taxonomies!', $created);
            echo '</p></div>';
        }

        if (isset($_GET['locations_created'])) {
            $created = intval($_GET['locations_created']);
            $total = intval($_GET['locations_total']);
            echo '<div class="notice notice-success is-dismissible"><p>';
            echo sprintf('✅ Successfully created %d of %d locations!', $created, $total);
            echo '</p></div>';
        }

        if (isset($_GET['collaborators_created'])) {
            $created = intval($_GET['collaborators_created']);
            $total = intval($_GET['collaborators_total']);
            echo '<div class="notice notice-success is-dismissible"><p>';
            echo sprintf('✅ Successfully created %d of %d team members!', $created, $total);
            echo '</p></div>';
        }

        if (isset($_GET['guides_created'])) {
            $created = intval($_GET['guides_created']);
            $total = intval($_GET['guides_total']);
            echo '<div class="notice notice-success is-dismissible"><p>';
            echo sprintf('✅ Successfully created %d of %d specialized guides!', $created, $total);
            echo '</p></div>';
        }

        if (isset($_GET['reviews_created'])) {
            $created = intval($_GET['reviews_created']);
            $total = intval($_GET['reviews_total']);
            echo '<div class="notice notice-success is-dismissible"><p>';
            echo sprintf('✅ Successfully created %d of %d customer reviews!', $created, $total);
            echo '</p></div>';
        }

        if (isset($_GET['deals_created'])) {
            $created = intval($_GET['deals_created']);
            $total = intval($_GET['deals_total']);
            echo '<div class="notice notice-success is-dismissible"><p>';
            echo sprintf('✅ Successfully created %d of %d promotional deals!', $created, $total);
            echo '</p></div>';
        }

        ?>
        <div class="wrap">
            <h1>🎲 Mock Data Generator</h1>

            <!-- FASE 9A: Interactive Wizard -->
            <div class="card wizard-launch-container" style="max-width: 800px; margin-top: 20px;">
                <h2 style="text-align: center; margin-bottom: 20px;">🚀 Interactive Mock Data Wizard</h2>
                <p style="text-align: center; margin-bottom: 30px; color: #666;">
                    Generate complete mock data for your travel website in one streamlined process with real-time progress tracking!
                </p>

                <div style="text-align: center;">
                    <button id="launch-wizard-btn" class="button button-primary button-hero">
                        🚀 Launch Mock Data Wizard
                    </button>
                </div>

                <div class="description" style="margin-top: 25px; text-align: center;">
                    <p style="margin-bottom: 10px;"><strong>This wizard will generate:</strong></p>
                    <ul style="list-style: none; padding: 0; margin: 15px 0;">
                        <li>📦 <strong>30</strong> Travel Packages with content & images</li>
                        <li>💰 <strong>10</strong> Promotional Deals</li>
                        <li>📍 <strong>20</strong> Locations</li>
                        <li>👨‍🏫 <strong>15</strong> Tour Guides</li>
                        <li>⭐ <strong>50</strong> Customer Reviews</li>
                        <li>👥 <strong>5</strong> Collaborators</li>
                        <li>🖼️ <strong>450+</strong> Images across all content</li>
                        <li>🏷️ <strong>Taxonomy Terms</strong> including destinations, countries, activities, etc.</li>
                    </ul>
                    <p style="margin-top: 15px; font-size: 13px; color: #999;">
                        ⏱️ <strong>Estimated time:</strong> 3-5 minutes<br>
                        💾 <strong>Progress is saved</strong> - you can pause and resume anytime<br>
                        🔄 <strong>Automatic retry</strong> on errors for reliability
                    </p>
                </div>
            </div>

            <div class="card" style="max-width: 800px; margin-top: 20px;">
                <h2>Current Status</h2>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <h3 style="margin-top: 0;">📦 Packages</h3>
                        <p>
                            <strong>Total:</strong> <?php echo esc_html($total_packages); ?><br>
                            <strong>Published:</strong> <?php echo esc_html($packages_count->publish); ?><br>
                            <strong>Draft:</strong> <?php echo esc_html($packages_count->draft); ?>
                        </p>
                    </div>
                    <div>
                        <h3 style="margin-top: 0;">📝 Blog Posts</h3>
                        <p>
                            <strong>Total:</strong> <?php echo esc_html($total_posts); ?><br>
                            <strong>Published:</strong> <?php echo esc_html($posts_count->publish); ?><br>
                            <strong>Draft:</strong> <?php echo esc_html($posts_count->draft); ?>
                        </p>
                    </div>
                </div>
            </div>

            <div class="card" style="max-width: 800px; margin-top: 20px;">
                <h2>🏷️ Taxonomies - Phase 1</h2>
                <p>Generate all taxonomies required for the system. This is the <strong>first step</strong> before creating any content.</p>
                <p style="color: #666; font-size: 13px; margin-top: 10px;">
                    ⚠️ <strong>Important:</strong> Run this before creating packages, locations, or other content. Existing terms will not be duplicated.
                </p>

                <div style="margin-top: 15px; padding: 12px; background: #f0f6fc; border-left: 4px solid #2271b1;">
                    <strong>Will create ~155 taxonomy terms:</strong>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-top: 10px;">
                        <div>
                            <strong>Location Taxonomies (4):</strong>
                            <ul style="margin: 8px 0 0 20px; list-style: disc; font-size: 13px;">
                                <li>Countries (hierarchical) - 17 terms</li>
                                <li>Destinations - 14 terms</li>
                                <li>Locations - 10 terms</li>
                                <li>Flights - 8 terms</li>
                            </ul>
                        </div>
                        <div>
                            <strong>Package Taxonomies (11):</strong>
                            <ul style="margin: 8px 0 0 20px; list-style: disc; font-size: 13px;">
                                <li>Package Type (hierarchical) - 11 terms</li>
                                <li>Interest - 10 terms</li>
                                <li>Optional Renting - 5 terms</li>
                                <li>Included Services - 8 terms</li>
                                <li>Additional Info - 5 terms</li>
                                <li>Landing Packages - 4 terms</li>
                                <li>Activity - 8 terms</li>
                                <li>Type Service - 4 terms</li>
                                <li>Hotel - 6 terms</li>
                                <li>Specialists - 6 terms</li>
                                <li>Spot Calendar - 4 terms</li>
                            </ul>
                        </div>
                    </div>
                    <div style="margin-top: 10px;">
                        <strong>Collaborator Taxonomies (1):</strong>
                        <ul style="margin: 8px 0 0 20px; list-style: disc; font-size: 13px;">
                            <li>Roles (hierarchical) - 16 terms</li>
                        </ul>
                    </div>
                </div>

                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="margin-top: 20px;">
                    <input type="hidden" name="action" value="aurora_generate_taxonomies">
                    <?php wp_nonce_field('aurora_mock_taxonomies', 'aurora_nonce'); ?>
                    <button type="submit" class="button button-primary button-hero">
                        🏷️ Generate All Taxonomies (~155 terms)
                    </button>
                    <p style="margin-top: 10px; font-size: 13px; color: #666;">
                        Fast execution - takes ~2-3 seconds
                    </p>
                </form>
            </div>

            <div class="card" style="max-width: 800px; margin-top: 20px;">
                <h2>📍 Locations - Phase 2</h2>
                <p>Generate 30 location posts that will be referenced by packages.</p>
                <p style="color: #666; font-size: 13px; margin-top: 10px;">
                    ⚠️ <strong>Prerequisites:</strong> Run "Generate All Taxonomies" first. Locations require taxonomies to be created.
                </p>

                <div style="margin-top: 15px; padding: 12px; background: #f0f6fc; border-left: 4px solid #2271b1;">
                    <strong>Will create 30 locations with complete data:</strong>
                    <ul style="margin: 8px 0 0 20px; list-style: disc; font-size: 13px;">
                        <li><strong>Major sites:</strong> Machu Picchu, Sacred Valley, Rainbow Mountain, Humantay Lake, etc.</li>
                        <li><strong>Cities:</strong> Cusco, Lima, Arequipa, Huaraz</li>
                        <li><strong>Natural wonders:</strong> Lake Titicaca, Colca Canyon, Amazon Rainforest, Nazca Lines</li>
                        <li><strong>ACF fields:</strong> Subtitle, SEO fields, active status</li>
                        <li><strong>Taxonomies:</strong> Countries, destinations, locations, flights</li>
                    </ul>
                </div>

                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="margin-top: 20px;">
                    <input type="hidden" name="action" value="aurora_generate_locations">
                    <?php wp_nonce_field('aurora_mock_locations', 'aurora_nonce'); ?>
                    <button type="submit" class="button button-primary button-hero">
                        📍 Generate 30 Locations
                    </button>
                    <p style="margin-top: 10px; font-size: 13px; color: #666;">
                        Fast execution - takes ~5 seconds
                    </p>
                </form>
            </div>

            <div class="card" style="max-width: 800px; margin-top: 20px;">
                <h2>👥 Team Members (Collaborators) - Phase 3</h2>
                <p>Generate 20 team members with detailed profiles, roles, and experience.</p>
                <p style="color: #666; font-size: 13px; margin-top: 10px;">
                    ⚠️ <strong>Prerequisites:</strong> Run "Generate All Taxonomies" first. Collaborators require the roles taxonomy.
                </p>

                <div style="margin-top: 15px; padding: 12px; background: #f0f6fc; border-left: 4px solid #2271b1;">
                    <strong>Will create 20 team members with complete data:</strong>
                    <ul style="margin: 8px 0 0 20px; list-style: disc; font-size: 13px;">
                        <li><strong>Management:</strong> CEO, Operations Manager, Marketing Manager (3)</li>
                        <li><strong>Guides:</strong> Lead Guides, Senior Guides, Tour Guides (8)</li>
                        <li><strong>Support Staff:</strong> Customer Service, Reservations, Accounting (4)</li>
                        <li><strong>Field Staff:</strong> Drivers, Chefs, Porters (5)</li>
                        <li><strong>ACF fields:</strong> Last name, job title, description, hobbies</li>
                        <li><strong>Taxonomy:</strong> Roles (hierarchical assignments)</li>
                    </ul>
                </div>

                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="margin-top: 20px;">
                    <input type="hidden" name="action" value="aurora_generate_collaborators">
                    <?php wp_nonce_field('aurora_mock_collaborators', 'aurora_nonce'); ?>
                    <button type="submit" class="button button-primary button-hero">
                        👥 Generate 20 Team Members
                    </button>
                    <p style="margin-top: 10px; font-size: 13px; color: #666;">
                        Fast execution - takes ~3 seconds
                    </p>
                </form>
            </div>

            <div class="card" style="max-width: 800px; margin-top: 20px;">
                <h2>🧭 Specialized Guides - Phase 4</h2>
                <p>Generate 15 specialized tour guides with detailed expertise profiles.</p>
                <p style="color: #666; font-size: 13px; margin-top: 10px;">
                    ℹ️ <strong>Note:</strong> Guides use only native WordPress fields (title, content, excerpt, featured image).
                </p>

                <div style="margin-top: 15px; padding: 12px; background: #f0f6fc; border-left: 4px solid #2271b1;">
                    <strong>Will create 15 specialized guides:</strong>
                    <ul style="margin: 8px 0 0 20px; list-style: disc; font-size: 13px;">
                        <li><strong>Inca Trail Specialist:</strong> Marco Quispe - 12+ years experience</li>
                        <li><strong>Amazon Expert:</strong> Rosa Ccahuana - Biologist and wildlife specialist</li>
                        <li><strong>Culinary Guide:</strong> Pablo Huamán - Professional chef</li>
                        <li><strong>Photography Guide:</strong> Lucia Puma - Professional photographer</li>
                        <li><strong>Archaeology Expert:</strong> Jorge Apaza - PhD archaeologist</li>
                        <li><strong>Wildlife Specialist:</strong> Carmen Quispe - Conservation expert</li>
                        <li><strong>Adventure Sports:</strong> Miguel Soncco - Certified mountain guide</li>
                        <li><strong>Cultural Heritage:</strong> Diana Ccosi - Anthropologist</li>
                        <li><strong>Mountain Guide:</strong> Raúl Huanca - High-altitude specialist</li>
                        <li><strong>Historical Sites:</strong> Silvia Mamani - Colonial history expert</li>
                        <li><strong>Birdwatching:</strong> Carlos Condori - 1,500+ species identified</li>
                        <li><strong>Textile Arts:</strong> Beatriz Yupanqui - Master weaver</li>
                        <li><strong>Trek Leader:</strong> Fernando Ttito - 14 years experience</li>
                        <li><strong>Family Tours:</strong> Angela Soto - Education specialist</li>
                        <li><strong>Senior Lead:</strong> Roberto Ccapa - 18+ years, mentor</li>
                    </ul>
                </div>

                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="margin-top: 20px;">
                    <input type="hidden" name="action" value="aurora_generate_guides">
                    <?php wp_nonce_field('aurora_mock_guides', 'aurora_nonce'); ?>
                    <button type="submit" class="button button-primary button-hero">
                        🧭 Generate 15 Specialized Guides
                    </button>
                    <p style="margin-top: 10px; font-size: 13px; color: #666;">
                        Fast execution - takes ~2 seconds
                    </p>
                </form>
            </div>

            <div class="card" style="max-width: 800px; margin-top: 20px;">
                <h2>⭐ Customer Reviews - Phase 5</h2>
                <p>Generate 30 authentic customer reviews from travelers around the world.</p>
                <p style="color: #666; font-size: 13px; margin-top: 10px;">
                    ℹ️ <strong>Note:</strong> Reviews use only native WordPress fields (title, content, excerpt) plus country meta field.
                </p>

                <div style="margin-top: 15px; padding: 12px; background: #f0f6fc; border-left: 4px solid #2271b1;">
                    <strong>Will create 30 customer reviews:</strong>
                    <ul style="margin: 8px 0 0 20px; list-style: disc; font-size: 13px;">
                        <li><strong>USA:</strong> 10 detailed reviews from American travelers</li>
                        <li><strong>UK:</strong> 5 reviews from British tourists</li>
                        <li><strong>Canada:</strong> 5 reviews including French-speaking travelers</li>
                        <li><strong>Australia:</strong> 5 reviews from Down Under</li>
                        <li><strong>Europe:</strong> 5 reviews from Germany, France, Spain, Italy, Sweden</li>
                    </ul>
                    <div style="margin-top: 10px;">
                        <strong>Content includes:</strong>
                        <ul style="margin: 8px 0 0 20px; list-style: disc; font-size: 13px;">
                            <li>Detailed testimonials about specific tours (Inca Trail, Amazon, culinary, etc.)</li>
                            <li>Guide mentions (Marco, Pablo, Rosa, etc.)</li>
                            <li>Solo travelers, families, couples, seniors perspectives</li>
                            <li>Special interests: photography, birdwatching, adventure, culture</li>
                            <li>Country metadata for filtering and display</li>
                        </ul>
                    </div>
                </div>

                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="margin-top: 20px;">
                    <input type="hidden" name="action" value="aurora_generate_reviews">
                    <?php wp_nonce_field('aurora_mock_reviews', 'aurora_nonce'); ?>
                    <button type="submit" class="button button-primary button-hero">
                        ⭐ Generate 30 Customer Reviews
                    </button>
                    <p style="margin-top: 10px; font-size: 13px; color: #666;">
                        Fast execution - takes ~3 seconds
                    </p>
                </form>
            </div>

            <div class="card" style="max-width: 800px; margin-top: 20px;">
                <h2>🏷️ Promotional Deals - Phase 6</h2>
                <p>Generate 10 promotional deals with various discount types and date ranges.</p>
                <p style="color: #666; font-size: 13px; margin-top: 10px;">
                    ℹ️ <strong>Note:</strong> Deals include ACF fields for active status, dates, discounts, descriptions, and terms.
                </p>

                <div style="margin-top: 15px; padding: 12px; background: #f0f6fc; border-left: 4px solid #2271b1;">
                    <strong>Will create 10 promotional deals:</strong>
                    <div style="margin-top: 10px;">
                        <strong>Active Deals (6):</strong>
                        <ul style="margin: 8px 0 0 20px; list-style: disc; font-size: 13px;">
                            <li>Early Bird Special - 20% off 2025 Inca Trail</li>
                            <li>Summer Adventure - 15% off multi-day treks</li>
                            <li>Family Vacation - Kids travel free</li>
                            <li>Romantic Getaway - 25% honeymoon special</li>
                            <li>Eco-Warrior - 10% sustainable tourism discount</li>
                            <li>Senior Traveler - 15% for ages 65+</li>
                            <li>Group Adventures - 20% for groups of 6+</li>
                        </ul>
                    </div>
                    <div style="margin-top: 10px;">
                        <strong>Inactive Deals (3):</strong>
                        <ul style="margin: 8px 0 0 20px; list-style: disc; font-size: 13px;">
                            <li>Black Friday Sale - 40% off (expired)</li>
                            <li>Winter Escape - 30% off (seasonal, past)</li>
                            <li>Last Minute - 35% off (summer only)</li>
                        </ul>
                    </div>
                    <div style="margin-top: 10px;">
                        <strong>ACF Fields included:</strong>
                        <ul style="margin: 8px 0 0 20px; list-style: disc; font-size: 13px;">
                            <li>Active status (true/false)</li>
                            <li>Start and end dates with time</li>
                            <li>Discount percentage (10-40%)</li>
                            <li>Full HTML descriptions with benefits</li>
                            <li>Terms & conditions</li>
                        </ul>
                    </div>
                </div>

                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="margin-top: 20px;">
                    <input type="hidden" name="action" value="aurora_generate_deals">
                    <?php wp_nonce_field('aurora_mock_deals', 'aurora_nonce'); ?>
                    <button type="submit" class="button button-primary button-hero">
                        🏷️ Generate 10 Promotional Deals
                    </button>
                    <p style="margin-top: 10px; font-size: 13px; color: #666;">
                        Fast execution - takes ~2 seconds
                    </p>
                </form>
            </div>

            <div class="card" style="max-width: 800px; margin-top: 20px;">
                <h2>🎯 Header Mock Data</h2>
                <p>Generate header data (ACF Options) and navigation menus with the exact content from the design.</p>
                <p style="color: #666; font-size: 13px; margin-top: 10px;">
                    ⚠️ <strong>Note:</strong> Existing data will be replaced. Menus with the same name will be deleted and recreated.
                </p>

                <div style="display: flex; gap: 15px; margin-top: 20px;">
                    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                        <input type="hidden" name="action" value="aurora_generate_header">
                        <?php wp_nonce_field('aurora_mock_header', 'aurora_nonce'); ?>
                        <button type="submit" class="button button-primary">
                            📱 Generate Header Data
                        </button>
                    </form>

                    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                        <input type="hidden" name="action" value="aurora_generate_menus">
                        <?php wp_nonce_field('aurora_mock_menus', 'aurora_nonce'); ?>
                        <button type="submit" class="button button-primary">
                            🧭 Generate Navigation Menus
                        </button>
                    </form>
                </div>

                <div style="margin-top: 15px; padding: 12px; background: #f0f6fc; border-left: 4px solid #2271b1;">
                    <strong>Includes:</strong>
                    <ul style="margin: 8px 0 0 20px; list-style: disc;">
                        <li>Phone: +1-(888)-803-8004</li>
                        <li>Primary Menu (header top): About Us, Blog, Contact</li>
                        <li>Secondary Menu (below header): Top Experiences, Destinations, Treks & Adventure, Culture & History, Deals</li>
                        <li>Aside Menu sections: Tour Packages, Reviews, FAQs, Tailor Made, Favorites</li>
                        <li>Social Media links: Facebook, Instagram, Pinterest, YouTube, TikTok</li>
                    </ul>
                </div>
            </div>

            <div class="card" style="max-width: 800px; margin-top: 20px;">
                <h2>🦶 Footer Mock Data</h2>
                <p>Generate footer data (ACF Options) and footer navigation menus with the exact content from the design.</p>
                <p style="color: #666; font-size: 13px; margin-top: 10px;">
                    ⚠️ <strong>Note:</strong> Existing data will be replaced. Menus with the same name will be deleted and recreated.
                </p>

                <div style="display: flex; gap: 15px; margin-top: 20px;">
                    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                        <input type="hidden" name="action" value="aurora_generate_footer">
                        <?php wp_nonce_field('aurora_mock_footer', 'aurora_nonce'); ?>
                        <button type="submit" class="button button-primary">
                            📄 Generate Footer Data
                        </button>
                    </form>

                    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                        <input type="hidden" name="action" value="aurora_generate_footer_menus">
                        <?php wp_nonce_field('aurora_mock_footer_menus', 'aurora_nonce'); ?>
                        <button type="submit" class="button button-primary">
                            📋 Generate Footer Menus
                        </button>
                    </form>
                </div>

                <div style="margin-top: 15px; padding: 12px; background: #f0f6fc; border-left: 4px solid #2271b1;">
                    <strong>Includes:</strong>
                    <ul style="margin: 8px 0 0 20px; list-style: disc;">
                        <li>Taglines: "I am guide, I am guardian, I am bridge" & "Fly straight to the heart of the Inca land"</li>
                        <li>Contact: Toll Free, Peru office, 24/7 phones, email</li>
                        <li>Office Hours: Sales & Operations schedule</li>
                        <li>6 Footer Menus: Top Experiences, Treks, Culture, Destinations, About, Extra Info</li>
                        <li>Legal: Copyright, RUC, Address, Privacy, Terms, Cookies, Sitemap</li>
                        <li>Social: Facebook, Instagram, Pinterest, LinkedIn, YouTube</li>
                    </ul>
                </div>
            </div>

            <div class="card" style="max-width: 800px; margin-top: 20px;">
                <h2>📦 Package Mock Data</h2>
                <p>Generate packages in two steps to avoid timeout issues:</p>
                <p style="color: #666; font-size: 13px; margin-top: 10px;">
                    ⚠️ <strong>Important:</strong> ALL existing packages will be deleted before creating new ones.
                </p>
                <p style="margin-top: 15px;"><strong>Includes:</strong></p>
                <ul style="list-style: disc; margin-left: 20px;">
                    <li>8 Day Tours (Machu Picchu, Sacred Valley, Rainbow Mountain, etc.)</li>
                    <li>6 Multi-day Treks (Inca Trail, Salkantay, Lares, etc.)</li>
                    <li>3 Adventure Tours (Rafting, Biking, Combined activities)</li>
                    <li>3 Combined Tours (Multi-destination packages)</li>
                    <li>Complete ACF field data (itinerary, pricing, availability, etc.)</li>
                </ul>

                <div style="display: flex; gap: 15px; margin-top: 20px; align-items: flex-start;">
                    <div style="flex: 1;">
                        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                            <input type="hidden" name="action" value="aurora_generate_mock">
                            <?php wp_nonce_field('aurora_mock_generate', 'aurora_nonce'); ?>
                            <button type="submit" class="button button-primary button-hero">
                                ✨ Step 1: Generate Packages
                            </button>
                            <p style="margin-top: 10px; font-size: 13px; color: #666;">
                                Creates 20 packages WITHOUT images<br>
                                (Fast - takes ~10 seconds)
                            </p>
                        </form>
                    </div>

                    <div style="flex: 1;">
                        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                            <input type="hidden" name="action" value="aurora_add_images">
                            <?php wp_nonce_field('aurora_add_images', 'aurora_nonce'); ?>
                            <button type="submit" class="button button-primary button-hero">
                                🖼️ Step 2: Add Images
                            </button>
                            <p style="margin-top: 10px; font-size: 13px; color: #666;">
                                Adds featured images to packages<br>
                                (Slower - takes ~1-2 minutes)
                            </p>
                        </form>
                    </div>
                </div>
            </div>

            <div class="card" style="max-width: 800px; margin-top: 20px; border-left-color: #00a32a;">
                <h2>📝 Blog Post Mock Data</h2>
                <p>Generate blog posts in two steps to avoid timeout issues:</p>
                <p style="margin-top: 15px;"><strong>Includes:</strong></p>
                <ul style="list-style: disc; margin-left: 20px;">
                    <li>10 Travel Blog Posts about Peru</li>
                    <li>Topics: Machu Picchu Tips, Cusco Guide, Inca Trail, Food, etc.</li>
                    <li>Full HTML content with headings and lists</li>
                    <li>Automatic categories and tags</li>
                    <li>SEO-friendly excerpts</li>
                </ul>

                <div style="display: flex; gap: 15px; margin-top: 20px; align-items: flex-start;">
                    <div style="flex: 1;">
                        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                            <input type="hidden" name="action" value="aurora_generate_blog_posts">
                            <?php wp_nonce_field('aurora_blog_generate', 'aurora_nonce'); ?>
                            <button type="submit" class="button button-primary button-hero">
                                ✨ Step 1: Generate Blog Posts
                            </button>
                            <p style="margin-top: 10px; font-size: 13px; color: #666;">
                                Creates 10 blog posts WITHOUT images<br>
                                (Fast - takes ~5 seconds)
                            </p>
                        </form>
                    </div>

                    <div style="flex: 1;">
                        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                            <input type="hidden" name="action" value="aurora_add_blog_images">
                            <?php wp_nonce_field('aurora_blog_images', 'aurora_nonce'); ?>
                            <button type="submit" class="button button-primary button-hero">
                                🖼️ Step 2: Add Images
                            </button>
                            <p style="margin-top: 10px; font-size: 13px; color: #666;">
                                Adds featured images to blog posts<br>
                                (Slower - takes ~30-60 seconds)
                            </p>
                        </form>
                    </div>
                </div>
            </div>

            <!-- FASE 8D: Additional Image Management -->
            <div class="card" style="max-width: 800px; margin-top: 20px; border-left-color: #8440ea;">
                <h2>🖼️ FASE 8: Advanced Image Management</h2>
                <p>Add images to all CPTs and Taxonomies. Use individual buttons or the master "ADD ALL" button.</p>

                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; margin-top: 20px;">
                    <!-- Deals -->
                    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                        <input type="hidden" name="action" value="aurora_add_images_deals">
                        <?php wp_nonce_field('aurora_add_images_deals', 'aurora_nonce'); ?>
                        <button type="submit" class="button button-secondary" style="width: 100%;">
                            💰 Add Images to Deals
                        </button>
                    </form>

                    <!-- Locations -->
                    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                        <input type="hidden" name="action" value="aurora_add_images_locations">
                        <?php wp_nonce_field('aurora_add_images_locations', 'aurora_nonce'); ?>
                        <button type="submit" class="button button-secondary" style="width: 100%;">
                            📍 Add Images to Locations
                        </button>
                    </form>

                    <!-- Guides -->
                    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                        <input type="hidden" name="action" value="aurora_add_images_guides">
                        <?php wp_nonce_field('aurora_add_images_guides', 'aurora_nonce'); ?>
                        <button type="submit" class="button button-secondary" style="width: 100%;">
                            👨‍🏫 Add Images to Guides
                        </button>
                    </form>

                    <!-- Reviews -->
                    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                        <input type="hidden" name="action" value="aurora_add_images_reviews">
                        <?php wp_nonce_field('aurora_add_images_reviews', 'aurora_nonce'); ?>
                        <button type="submit" class="button button-secondary" style="width: 100%;">
                            ⭐ Add Images to Reviews
                        </button>
                    </form>

                    <!-- Collaborators -->
                    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                        <input type="hidden" name="action" value="aurora_add_images_collaborators">
                        <?php wp_nonce_field('aurora_add_images_collaborators', 'aurora_nonce'); ?>
                        <button type="submit" class="button button-secondary" style="width: 100%;">
                            👥 Add Images to Collaborators
                        </button>
                    </form>

                    <!-- Destinations -->
                    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                        <input type="hidden" name="action" value="aurora_add_images_destinations">
                        <?php wp_nonce_field('aurora_add_images_destinations', 'aurora_nonce'); ?>
                        <button type="submit" class="button button-secondary" style="width: 100%;">
                            🗺️ Add Images to Destinations
                        </button>
                    </form>

                    <!-- Taxonomies -->
                    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                        <input type="hidden" name="action" value="aurora_add_images_taxonomies">
                        <?php wp_nonce_field('aurora_add_images_taxonomies', 'aurora_nonce'); ?>
                        <button type="submit" class="button button-secondary" style="width: 100%;">
                            🏷️ Add Images to Taxonomies
                        </button>
                    </form>
                </div>

                <!-- Master Button -->
                <div style="margin-top: 25px; padding-top: 20px; border-top: 2px solid #ddd;">
                    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                        <input type="hidden" name="action" value="aurora_add_all_images">
                        <?php wp_nonce_field('aurora_add_all_images', 'aurora_nonce'); ?>
                        <button type="submit" class="button button-primary button-hero" style="width: 100%;">
                            🎨 ADD ALL IMAGES (Everything)
                        </button>
                        <p style="margin-top: 10px; font-size: 13px; color: #666; text-align: center;">
                            Adds images to: Packages, Deals, Locations, Guides, Reviews, Collaborators, Destinations & Taxonomies<br>
                            <strong>(May take 2-5 minutes)</strong>
                        </p>
                    </form>
                </div>
            </div>

            <div class="card" style="max-width: 800px; margin-top: 20px; border-left-color: #dc3232;">
                <h2 style="color: #dc3232;">⚠️ Danger Zone</h2>

                <div style="margin-bottom: 20px;">
                    <p><strong>Delete ALL packages</strong> - This action cannot be undone!</p>
                    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>"
                          onsubmit="return confirm('Are you SURE you want to delete ALL packages? This cannot be undone!');"
                          style="margin-top: 10px;">
                        <input type="hidden" name="action" value="aurora_delete_all">
                        <?php wp_nonce_field('aurora_mock_delete', 'aurora_nonce'); ?>
                        <button type="submit" class="button button-secondary">
                            🗑️ Delete All Packages
                        </button>
                    </form>
                </div>

                <div>
                    <p><strong>Delete ALL blog posts</strong> - This action cannot be undone!</p>
                    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>"
                          onsubmit="return confirm('Are you SURE you want to delete ALL blog posts? This cannot be undone!');"
                          style="margin-top: 10px;">
                        <input type="hidden" name="action" value="aurora_delete_blog_posts">
                        <?php wp_nonce_field('aurora_blog_delete', 'aurora_nonce'); ?>
                        <button type="submit" class="button button-secondary">
                            🗑️ Delete All Blog Posts
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <style>
            .wrap .card {
                padding: 20px;
                background: white;
                border: 1px solid #ccd0d4;
                border-left: 4px solid #2271b1;
                box-shadow: 0 1px 1px rgba(0,0,0,.04);
            }
            .wrap .card h2 {
                margin-top: 0;
            }
        </style>
        <?php
    }

    /**
     * Handle taxonomy generation
     */
    public function handle_generate_taxonomies()
    {
        // Verify nonce
        if (!isset($_POST['aurora_nonce']) || !wp_verify_nonce($_POST['aurora_nonce'], 'aurora_mock_taxonomies')) {
            wp_die('Security check failed');
        }

        // Check permissions
        if (!current_user_can('manage_options')) {
            wp_die('Insufficient permissions');
        }

        // Generate all taxonomies
        $generator = Aurora_Mock_Data_Generator::get_instance();
        $result = $generator->generate_all_taxonomies();

        // Redirect with message
        $redirect_url = add_query_arg([
            'page' => 'package-mock-data',
            'taxonomies_created' => $result['created'],
        ], admin_url('edit.php?post_type=package'));

        wp_redirect($redirect_url);
        exit;
    }

    /**
     * Handle location generation
     */
    public function handle_generate_locations()
    {
        // Verify nonce
        if (!isset($_POST['aurora_nonce']) || !wp_verify_nonce($_POST['aurora_nonce'], 'aurora_mock_locations')) {
            wp_die('Security check failed');
        }

        // Check permissions
        if (!current_user_can('manage_options')) {
            wp_die('Insufficient permissions');
        }

        // Generate locations
        $generator = Aurora_Mock_Data_Generator::get_instance();
        $result = $generator->generate_locations();

        // Redirect with message
        $redirect_url = add_query_arg([
            'page' => 'package-mock-data',
            'locations_created' => $result['created'],
            'locations_total' => $result['total'],
        ], admin_url('edit.php?post_type=package'));

        wp_redirect($redirect_url);
        exit;
    }

    /**
     * Handle collaborator generation
     */
    public function handle_generate_collaborators()
    {
        // Verify nonce
        if (!isset($_POST['aurora_nonce']) || !wp_verify_nonce($_POST['aurora_nonce'], 'aurora_mock_collaborators')) {
            wp_die('Security check failed');
        }

        // Check permissions
        if (!current_user_can('manage_options')) {
            wp_die('Insufficient permissions');
        }

        // Generate collaborators
        $generator = Aurora_Mock_Data_Generator::get_instance();
        $result = $generator->generate_collaborators();

        // Redirect with message
        $redirect_url = add_query_arg([
            'page' => 'package-mock-data',
            'collaborators_created' => $result['created'],
            'collaborators_total' => $result['total'],
        ], admin_url('edit.php?post_type=package'));

        wp_redirect($redirect_url);
        exit;
    }

    /**
     * Handle guide generation
     */
    public function handle_generate_guides()
    {
        // Verify nonce
        if (!isset($_POST['aurora_nonce']) || !wp_verify_nonce($_POST['aurora_nonce'], 'aurora_mock_guides')) {
            wp_die('Security check failed');
        }

        // Check permissions
        if (!current_user_can('manage_options')) {
            wp_die('Insufficient permissions');
        }

        // Generate guides
        $generator = Aurora_Mock_Data_Generator::get_instance();
        $result = $generator->generate_guides();

        // Redirect with message
        $redirect_url = add_query_arg([
            'page' => 'package-mock-data',
            'guides_created' => $result['created'],
            'guides_total' => $result['total'],
        ], admin_url('edit.php?post_type=package'));

        wp_redirect($redirect_url);
        exit;
    }

    /**
     * Handle review generation
     */
    public function handle_generate_reviews()
    {
        // Verify nonce
        if (!isset($_POST['aurora_nonce']) || !wp_verify_nonce($_POST['aurora_nonce'], 'aurora_mock_reviews')) {
            wp_die('Security check failed');
        }

        // Check permissions
        if (!current_user_can('manage_options')) {
            wp_die('Insufficient permissions');
        }

        // Generate reviews
        $generator = Aurora_Mock_Data_Generator::get_instance();
        $result = $generator->generate_reviews();

        // Redirect with message
        $redirect_url = add_query_arg([
            'page' => 'package-mock-data',
            'reviews_created' => $result['created'],
            'reviews_total' => $result['total'],
        ], admin_url('edit.php?post_type=package'));

        wp_redirect($redirect_url);
        exit;
    }

    /**
     * Handle deal generation
     */
    public function handle_generate_deals()
    {
        // Verify nonce
        if (!isset($_POST['aurora_nonce']) || !wp_verify_nonce($_POST['aurora_nonce'], 'aurora_mock_deals')) {
            wp_die('Security check failed');
        }

        // Check permissions
        if (!current_user_can('manage_options')) {
            wp_die('Insufficient permissions');
        }

        // Generate deals
        $generator = Aurora_Mock_Data_Generator::get_instance();
        $result = $generator->generate_deals();

        // Redirect with message
        $redirect_url = add_query_arg([
            'page' => 'package-mock-data',
            'deals_created' => $result['created'],
            'deals_total' => $result['total'],
        ], admin_url('edit.php?post_type=package'));

        wp_redirect($redirect_url);
        exit;
    }

    /**
     * Handle generate action
     */
    public function handle_generate()
    {
        // Verify nonce
        if (!isset($_POST['aurora_nonce']) || !wp_verify_nonce($_POST['aurora_nonce'], 'aurora_mock_generate')) {
            wp_die('Security check failed');
        }

        // Check permissions
        if (!current_user_can('manage_options')) {
            wp_die('Insufficient permissions');
        }

        // Generate packages
        $generator = Aurora_Mock_Data_Generator::get_instance();
        $result = $generator->generate_packages();

        // Redirect with message
        if (!$result['success']) {
            // Generation was disabled
            $error_message = isset($result['errors'][0]) ? $result['errors'][0] : 'Package generation failed';
            $redirect_url = add_query_arg([
                'page' => 'package-mock-data',
                'error' => urlencode($error_message),
            ], admin_url('edit.php?post_type=package'));
        } else {
            $redirect_url = add_query_arg([
                'page' => 'package-mock-data',
                'generated' => $result['created'],
                'total' => $result['total'],
            ], admin_url('edit.php?post_type=package'));
        }

        wp_redirect($redirect_url);
        exit;
    }

    /**
     * Handle delete action
     */
    public function handle_delete()
    {
        // Verify nonce
        if (!isset($_POST['aurora_nonce']) || !wp_verify_nonce($_POST['aurora_nonce'], 'aurora_mock_delete')) {
            wp_die('Security check failed');
        }

        // Check permissions
        if (!current_user_can('manage_options')) {
            wp_die('Insufficient permissions');
        }

        // Delete packages
        $generator = Aurora_Mock_Data_Generator::get_instance();
        $result = $generator->delete_all_packages();

        // Redirect with message
        if (!$result['success']) {
            // Deletion was disabled
            $error_message = isset($result['errors'][0]) ? $result['errors'][0] : 'Package deletion failed';
            $redirect_url = add_query_arg([
                'page' => 'package-mock-data',
                'error' => urlencode($error_message),
            ], admin_url('edit.php?post_type=package'));
        } else {
            $redirect_url = add_query_arg([
                'page' => 'package-mock-data',
                'deleted' => $result['deleted'],
            ], admin_url('edit.php?post_type=package'));
        }

        wp_redirect($redirect_url);
        exit;
    }

    /**
     * Handle generate header data
     */
    public function handle_generate_header()
    {
        // Verify nonce
        if (!isset($_POST['aurora_nonce']) || !wp_verify_nonce($_POST['aurora_nonce'], 'aurora_mock_header')) {
            wp_die('Security check failed');
        }

        // Check permissions
        if (!current_user_can('manage_options')) {
            wp_die('Insufficient permissions');
        }

        // Generate header data
        $generator = Aurora_Mock_Data_Generator::get_instance();
        $result = $generator->generate_header_data();

        // Redirect with message
        $redirect_url = add_query_arg([
            'page' => 'package-mock-data',
            'header_generated' => $result['success'] ? 1 : 0,
        ], admin_url('edit.php?post_type=package'));

        wp_redirect($redirect_url);
        exit;
    }

    /**
     * Handle generate menus
     */
    public function handle_generate_menus()
    {
        // Verify nonce
        if (!isset($_POST['aurora_nonce']) || !wp_verify_nonce($_POST['aurora_nonce'], 'aurora_mock_menus')) {
            wp_die('Security check failed');
        }

        // Check permissions
        if (!current_user_can('manage_options')) {
            wp_die('Insufficient permissions');
        }

        // Generate menus
        $generator = Aurora_Mock_Data_Generator::get_instance();
        $result = $generator->generate_navigation_menus();

        // Redirect with message
        $redirect_url = add_query_arg([
            'page' => 'package-mock-data',
            'menus_generated' => $result['success'] ? 1 : 0,
        ], admin_url('edit.php?post_type=package'));

        wp_redirect($redirect_url);
        exit;
    }

    /**
     * Handle generate footer data
     */
    public function handle_generate_footer()
    {
        // Verify nonce
        if (!isset($_POST['aurora_nonce']) || !wp_verify_nonce($_POST['aurora_nonce'], 'aurora_mock_footer')) {
            wp_die('Security check failed');
        }

        // Check permissions
        if (!current_user_can('manage_options')) {
            wp_die('Insufficient permissions');
        }

        // Generate footer data
        $generator = Aurora_Mock_Data_Generator::get_instance();
        $result = $generator->generate_footer_data();

        // Redirect with message
        $redirect_url = add_query_arg([
            'page' => 'package-mock-data',
            'footer_generated' => $result['success'] ? 1 : 0,
        ], admin_url('edit.php?post_type=package'));

        wp_redirect($redirect_url);
        exit;
    }

    /**
     * Handle generate footer menus
     */
    public function handle_generate_footer_menus()
    {
        // Verify nonce
        if (!isset($_POST['aurora_nonce']) || !wp_verify_nonce($_POST['aurora_nonce'], 'aurora_mock_footer_menus')) {
            wp_die('Security check failed');
        }

        // Check permissions
        if (!current_user_can('manage_options')) {
            wp_die('Insufficient permissions');
        }

        // Generate footer menus
        $generator = Aurora_Mock_Data_Generator::get_instance();
        $result = $generator->generate_footer_menus();

        // Redirect with message
        $redirect_url = add_query_arg([
            'page' => 'package-mock-data',
            'footer_menus_generated' => $result['success'] ? 1 : 0,
        ], admin_url('edit.php?post_type=package'));

        wp_redirect($redirect_url);
        exit;
    }

    /**
     * Handle add images to packages
     */
    public function handle_add_images()
    {
        // Verify nonce
        if (!isset($_POST['aurora_nonce']) || !wp_verify_nonce($_POST['aurora_nonce'], 'aurora_add_images')) {
            wp_die('Security check failed');
        }

        // Check permissions
        if (!current_user_can('manage_options')) {
            wp_die('Insufficient permissions');
        }

        // Add images to packages
        $generator = Aurora_Mock_Data_Generator::get_instance();
        $result = $generator->add_images_to_packages();

        // Redirect with message including fixed broken images count
        if (!$result['success']) {
            // Adding images was disabled
            $error_message = isset($result['errors'][0]) ? $result['errors'][0] : 'Adding images to packages failed';
            $redirect_url = add_query_arg([
                'page' => 'package-mock-data',
                'error' => urlencode($error_message),
            ], admin_url('edit.php?post_type=package'));
        } else {
            $redirect_url = add_query_arg([
                'page' => 'package-mock-data',
                'images_added' => $result['updated'],
                'images_total' => $result['total'],
                'images_fixed' => $result['fixed_broken'],
            ], admin_url('edit.php?post_type=package'));
        }

        wp_redirect($redirect_url);
        exit;
    }

    /**
     * FASE 8D: Handle add images to deals
     */
    public function handle_add_images_deals()
    {
        if (!isset($_POST['aurora_nonce']) || !wp_verify_nonce($_POST['aurora_nonce'], 'aurora_add_images_deals')) {
            wp_die('Security check failed');
        }

        if (!current_user_can('manage_options')) {
            wp_die('Insufficient permissions');
        }

        $generator = Aurora_Mock_Data_Generator::get_instance();
        $result = $generator->add_images_to_deals();

        $redirect_url = add_query_arg([
            'page' => 'package-mock-data',
            'deals_images_added' => $result['updated'],
            'deals_images_total' => $result['total'],
        ], admin_url('edit.php?post_type=package'));

        wp_redirect($redirect_url);
        exit;
    }

    /**
     * FASE 8D: Handle add images to locations
     */
    public function handle_add_images_locations()
    {
        if (!isset($_POST['aurora_nonce']) || !wp_verify_nonce($_POST['aurora_nonce'], 'aurora_add_images_locations')) {
            wp_die('Security check failed');
        }

        if (!current_user_can('manage_options')) {
            wp_die('Insufficient permissions');
        }

        $generator = Aurora_Mock_Data_Generator::get_instance();
        $result = $generator->add_images_to_locations();

        $redirect_url = add_query_arg([
            'page' => 'package-mock-data',
            'locations_images_added' => $result['updated'],
            'locations_images_total' => $result['total'],
        ], admin_url('edit.php?post_type=package'));

        wp_redirect($redirect_url);
        exit;
    }

    /**
     * FASE 8D: Handle add images to guides
     */
    public function handle_add_images_guides()
    {
        if (!isset($_POST['aurora_nonce']) || !wp_verify_nonce($_POST['aurora_nonce'], 'aurora_add_images_guides')) {
            wp_die('Security check failed');
        }

        if (!current_user_can('manage_options')) {
            wp_die('Insufficient permissions');
        }

        $generator = Aurora_Mock_Data_Generator::get_instance();
        $result = $generator->add_images_to_guides();

        $redirect_url = add_query_arg([
            'page' => 'package-mock-data',
            'guides_images_added' => $result['updated'],
            'guides_images_total' => $result['total'],
        ], admin_url('edit.php?post_type=package'));

        wp_redirect($redirect_url);
        exit;
    }

    /**
     * FASE 8D: Handle add images to reviews
     */
    public function handle_add_images_reviews()
    {
        if (!isset($_POST['aurora_nonce']) || !wp_verify_nonce($_POST['aurora_nonce'], 'aurora_add_images_reviews')) {
            wp_die('Security check failed');
        }

        if (!current_user_can('manage_options')) {
            wp_die('Insufficient permissions');
        }

        $generator = Aurora_Mock_Data_Generator::get_instance();
        $result = $generator->add_images_to_reviews();

        $redirect_url = add_query_arg([
            'page' => 'package-mock-data',
            'reviews_images_added' => $result['updated'],
            'reviews_images_total' => $result['total'],
        ], admin_url('edit.php?post_type=package'));

        wp_redirect($redirect_url);
        exit;
    }

    /**
     * FASE 8D: Handle add images to collaborators
     */
    public function handle_add_images_collaborators()
    {
        if (!isset($_POST['aurora_nonce']) || !wp_verify_nonce($_POST['aurora_nonce'], 'aurora_add_images_collaborators')) {
            wp_die('Security check failed');
        }

        if (!current_user_can('manage_options')) {
            wp_die('Insufficient permissions');
        }

        $generator = Aurora_Mock_Data_Generator::get_instance();
        $result = $generator->add_images_to_collaborators();

        $redirect_url = add_query_arg([
            'page' => 'package-mock-data',
            'collaborators_images_added' => $result['updated'],
            'collaborators_images_total' => $result['total'],
        ], admin_url('edit.php?post_type=package'));

        wp_redirect($redirect_url);
        exit;
    }

    /**
     * FASE 8D: Handle add images to destinations
     */
    public function handle_add_images_destinations()
    {
        if (!isset($_POST['aurora_nonce']) || !wp_verify_nonce($_POST['aurora_nonce'], 'aurora_add_images_destinations')) {
            wp_die('Security check failed');
        }

        if (!current_user_can('manage_options')) {
            wp_die('Insufficient permissions');
        }

        $generator = Aurora_Mock_Data_Generator::get_instance();
        $result = $generator->add_images_to_destinations();

        $redirect_url = add_query_arg([
            'page' => 'package-mock-data',
            'destinations_images_added' => $result['updated'],
            'destinations_images_total' => $result['total'],
        ], admin_url('edit.php?post_type=package'));

        wp_redirect($redirect_url);
        exit;
    }

    /**
     * FASE 8D: Handle add images to taxonomy terms
     */
    public function handle_add_images_taxonomies()
    {
        if (!isset($_POST['aurora_nonce']) || !wp_verify_nonce($_POST['aurora_nonce'], 'aurora_add_images_taxonomies')) {
            wp_die('Security check failed');
        }

        if (!current_user_can('manage_options')) {
            wp_die('Insufficient permissions');
        }

        $generator = Aurora_Mock_Data_Generator::get_instance();
        $result = $generator->add_images_to_taxonomy_terms();

        $redirect_url = add_query_arg([
            'page' => 'package-mock-data',
            'taxonomies_images_added' => $result['updated'],
            'taxonomies_images_total' => $result['total'],
        ], admin_url('edit.php?post_type=package'));

        wp_redirect($redirect_url);
        exit;
    }

    /**
     * FASE 8D: Handle add ALL images (master handler)
     */
    public function handle_add_all_images()
    {
        if (!isset($_POST['aurora_nonce']) || !wp_verify_nonce($_POST['aurora_nonce'], 'aurora_add_all_images')) {
            wp_die('Security check failed');
        }

        if (!current_user_can('manage_options')) {
            wp_die('Insufficient permissions');
        }

        $generator = Aurora_Mock_Data_Generator::get_instance();

        // Call all image methods (SKIP packages - disabled)
        // $packages = $generator->add_images_to_packages(); // DISABLED
        $deals = $generator->add_images_to_deals();
        $locations = $generator->add_images_to_locations();
        $guides = $generator->add_images_to_guides();
        $reviews = $generator->add_images_to_reviews();
        $collaborators = $generator->add_images_to_collaborators();
        $destinations = $generator->add_images_to_destinations();
        $taxonomies = $generator->add_images_to_taxonomy_terms();

        // Calculate totals (excluding packages)
        $total_updated = $deals['updated'] + $locations['updated'] +
                        $guides['updated'] + $reviews['updated'] + $collaborators['updated'] +
                        $destinations['updated'] + $taxonomies['updated'];

        $total_processed = $deals['total'] + $locations['total'] +
                          $guides['total'] + $reviews['total'] + $collaborators['total'] +
                          $destinations['total'] + $taxonomies['total'];

        $redirect_url = add_query_arg([
            'page' => 'package-mock-data',
            'all_images_added' => $total_updated,
            'all_images_total' => $total_processed,
        ], admin_url('edit.php?post_type=package'));

        wp_redirect($redirect_url);
        exit;
    }

    /**
     * Handle generate blog posts (FASE 1)
     */
    public function handle_generate_blog_posts()
    {
        // Verify nonce
        if (!isset($_POST['aurora_nonce']) || !wp_verify_nonce($_POST['aurora_nonce'], 'aurora_blog_generate')) {
            wp_die('Security check failed');
        }

        // Check permissions
        if (!current_user_can('manage_options')) {
            wp_die('Insufficient permissions');
        }

        // Generate blog posts
        $generator = Aurora_Mock_Data_Generator::get_instance();
        $result = $generator->generate_blog_posts();

        // Redirect with message
        $redirect_url = add_query_arg([
            'page' => 'package-mock-data',
            'blog_generated' => $result['created'],
            'blog_total' => $result['total'],
        ], admin_url('edit.php?post_type=package'));

        wp_redirect($redirect_url);
        exit;
    }

    /**
     * Handle add images to blog posts (FASE 2)
     */
    public function handle_add_blog_images()
    {
        // Verify nonce
        if (!isset($_POST['aurora_nonce']) || !wp_verify_nonce($_POST['aurora_nonce'], 'aurora_blog_images')) {
            wp_die('Security check failed');
        }

        // Check permissions
        if (!current_user_can('manage_options')) {
            wp_die('Insufficient permissions');
        }

        // Add images to blog posts
        $generator = Aurora_Mock_Data_Generator::get_instance();
        $result = $generator->add_images_to_blog_posts();

        // Redirect with message
        $redirect_url = add_query_arg([
            'page' => 'package-mock-data',
            'blog_images_added' => $result['updated'],
        ], admin_url('edit.php?post_type=package'));

        wp_redirect($redirect_url);
        exit;
    }

    /**
     * Handle delete all blog posts
     */
    public function handle_delete_blog_posts()
    {
        // Verify nonce
        if (!isset($_POST['aurora_nonce']) || !wp_verify_nonce($_POST['aurora_nonce'], 'aurora_blog_delete')) {
            wp_die('Security check failed');
        }

        // Check permissions
        if (!current_user_can('manage_options')) {
            wp_die('Insufficient permissions');
        }

        // Delete blog posts
        $generator = Aurora_Mock_Data_Generator::get_instance();
        $result = $generator->delete_all_blog_posts();

        // Redirect with message
        $redirect_url = add_query_arg([
            'page' => 'package-mock-data',
            'blog_deleted' => $result['deleted'],
        ], admin_url('edit.php?post_type=package'));

        wp_redirect($redirect_url);
        exit;
    }

    /**
     * FASE 9A: Enqueue wizard assets
     */
    public function enqueue_wizard_assets($hook) {
        // Only load on our admin page
        if ($hook !== 'package_page_package-mock-data') {
            return;
        }

        // Enqueue CSS
        wp_enqueue_style(
            'aurora-wizard-modal',
            plugin_dir_url(dirname(__FILE__)) . 'assets/css/wizard-modal.css',
            [],
            '1.0.0'
        );

        // Enqueue JS
        wp_enqueue_script(
            'aurora-wizard-modal',
            plugin_dir_url(dirname(__FILE__)) . 'assets/js/wizard-modal.js',
            ['jquery'],
            '1.0.0',
            true
        );

        // Localize script with AJAX data
        wp_localize_script('aurora-wizard-modal', 'auroraWizardData', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('aurora_wizard_nonce')
        ]);
    }

    /**
     * FASE 9A: Handle wizard batch processing via AJAX
     */
    public function handle_wizard_process_batch() {
        // Verify nonce
        check_ajax_referer('aurora_wizard_nonce', 'nonce');

        // Check permissions
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Insufficient permissions']);
            return;
        }

        // Get parameters
        $step = isset($_POST['step']) ? intval($_POST['step']) : 0;
        $batch = isset($_POST['batch']) ? intval($_POST['batch']) : 0;
        $checkpoint_data = isset($_POST['checkpoint_data']) ? $_POST['checkpoint_data'] : [];

        // Require wizard processor
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-wizard-processor.php';

        // Process batch
        $wizard = new Aurora_Wizard_Processor();
        $result = $wizard->process_batch($step, $batch, $checkpoint_data);

        // Return JSON response
        if ($result['success']) {
            wp_send_json_success($result);
        } else {
            wp_send_json_error($result);
        }
    }

    /**
     * FASE 9A: Get final statistics via AJAX
     */
    public function handle_wizard_get_stats() {
        // Verify nonce
        check_ajax_referer('aurora_wizard_nonce', 'nonce');

        // Check permissions
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Insufficient permissions']);
            return;
        }

        // Require wizard processor
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-wizard-processor.php';

        // Get statistics
        $wizard = new Aurora_Wizard_Processor();
        $reflection = new ReflectionClass($wizard);
        $method = $reflection->getMethod('get_final_statistics');
        $method->setAccessible(true);
        $stats = $method->invoke($wizard);

        wp_send_json_success(['statistics' => $stats]);
    }

    /**
     * FASE 9: Handle cleanup all mock data AJAX request
     */
    public function handle_wizard_cleanup() {
        // Verify nonce
        check_ajax_referer('aurora_wizard_nonce', 'nonce');

        // Check permissions
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Insufficient permissions']);
            return;
        }

        // Require wizard processor
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-wizard-processor.php';

        // Execute cleanup
        $wizard = new Aurora_Wizard_Processor();
        $result = $wizard->cleanup_all_mock_data();

        if ($result['success']) {
            wp_send_json_success($result);
        } else {
            wp_send_json_error($result);
        }
    }
}
