<?php
/**
 * Block: Contact Planner Form
 *
 * Formulario de contacto con imagen de fondo y panel flotante
 * Native WordPress block - NO ACF
 *
 * @package Travel\Blocks\Blocks\Package
 * @since 1.0.0
 */

namespace Travel\Blocks\Blocks\Package;

use Travel\Blocks\Helpers\EditorHelper;

class ContactPlannerForm
{
    private string $name = 'contact-planner-form';
    private string $title = 'Contact Planner Form';
    private string $description = 'Contact form with background image and floating panel';

    public function register(): void
    {
        register_block_type('travel-blocks/' . $this->name, [
            'api_version' => 2,
            'title' => __($this->title, 'travel-blocks'),
            'description' => __($this->description, 'travel-blocks'),
            'category' => 'template-blocks',
            'icon' => 'email-alt',
            'keywords' => ['contact', 'form', 'planner', 'inquiry', 'background'],
            'supports' => [
                'anchor' => true,
                'html' => false,
            ],
            'render_callback' => [$this, 'render'],
            'show_in_rest' => true,
        ]);
        add_action('enqueue_block_assets', [$this, 'enqueue_assets']);
    }

    public function enqueue_assets(): void
    {
        if (!is_admin()) {
            wp_enqueue_style(
                'contact-planner-form-style',
                TRAVEL_BLOCKS_URL . 'assets/blocks/contact-planner-form.css',
                [],
                TRAVEL_BLOCKS_VERSION
            );

            wp_enqueue_script(
                'contact-planner-form-script',
                TRAVEL_BLOCKS_URL . 'assets/blocks/contact-planner-form.js',
                [],
                TRAVEL_BLOCKS_VERSION,
                true
            );

            wp_localize_script('contact-planner-form-script', 'travelPlannerForm', [
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('travel_planner_form'),
            ]);
        }
    }

    public function render($attributes, $content, $block): string
    {
        try {
            $post_id = get_the_ID();
            $is_preview = EditorHelper::is_editor_mode($post_id);

            $form_data = $is_preview ? $this->get_preview_data() : $this->get_post_data($post_id);

            $data = [
                'block_id' => 'contact-planner-form-' . uniqid(),
                'class_name' => 'contact-planner-form' . (!empty($attributes['className']) ? ' ' . $attributes['className'] : ''),
                'background_image' => $form_data['background_image'],
                'overlay_opacity' => $form_data['overlay_opacity'],
                'panel_title' => $form_data['panel_title'],
                'panel_subtitle' => $form_data['panel_subtitle'],
                'highlight_word' => $form_data['highlight_word'],
                'button_text' => $form_data['button_text'],
                'success_message' => $form_data['success_message'],
                'is_preview' => $is_preview,
                'current_package_id' => $post_id,
                'package_title' => get_the_title($post_id),
            ];

            ob_start();
            $this->load_template('contact-planner-form', $data);
            return ob_get_clean();

        } catch (\Exception $e) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                return '<div style="padding: 10px; background: #ffebee; border: 1px solid #f44336;">' .
                       '<p>Error en Contact Planner Form: ' . esc_html($e->getMessage()) . '</p>' .
                       '</div>';
            }
            return '';
        }
    }

    private function get_preview_data(): array
    {
        return [
            'background_image' => 'https://images.unsplash.com/photo-1526392060635-9d6019884377?w=1920&h=600&fit=crop',
            'overlay_opacity' => 50,
            'panel_title' => 'Start planning your dream trip',
            'panel_subtitle' => 'Our travel experts are ready to help you create the perfect itinerary',
            'highlight_word' => 'dream',
            'button_text' => 'CONTACT US NOW',
            'success_message' => 'Thank you! Our travel planner will contact you within 24 hours.',
        ];
    }

    private function get_post_data(int $post_id): array
    {
        // Get background image - try custom field first, fallback to featured image
        $background_image = get_post_meta($post_id, 'planner_form_background', true);
        if (empty($background_image)) {
            $featured_id = get_post_thumbnail_id($post_id);
            $background_image = $featured_id ? wp_get_attachment_image_url($featured_id, 'full') : '';
        }

        return [
            'background_image' => $background_image,
            'overlay_opacity' => get_post_meta($post_id, 'planner_form_overlay_opacity', true) ?: 50,
            'panel_title' => get_post_meta($post_id, 'planner_form_title', true) ?: __('Start planning your dream trip', 'travel-blocks'),
            'panel_subtitle' => get_post_meta($post_id, 'planner_form_subtitle', true) ?: __('Our travel experts are ready to help you', 'travel-blocks'),
            'highlight_word' => get_post_meta($post_id, 'planner_form_highlight_word', true) ?: 'dream',
            'button_text' => get_post_meta($post_id, 'planner_form_button_text', true) ?: __('CONTACT US NOW', 'travel-blocks'),
            'success_message' => get_post_meta($post_id, 'planner_form_success_message', true) ?: __('Thank you! We\'ll contact you soon.', 'travel-blocks'),
        ];
    }

    protected function load_template(string $template_name, array $data = []): void
    {
        $template_path = TRAVEL_BLOCKS_PATH . 'templates/' . $template_name . '.php';

        if (!file_exists($template_path)) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                echo '<div style="padding:1rem;background:#fff3cd;border-left:4px solid #ffc107;">';
                echo '<strong>Template not found:</strong> ' . esc_html($template_name . '.php');
                echo '</div>';
            }
            return;
        }

        extract($data, EXTR_SKIP);
        include $template_path;
    }
}
