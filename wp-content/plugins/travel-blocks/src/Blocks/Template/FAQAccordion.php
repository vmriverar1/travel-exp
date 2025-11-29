<?php
/**
 * FAQ Accordion Template Block
 *
 * ACF-powered FAQ accordion with schema.org markup
 *
 * @package Travel\Blocks\Blocks\Template
 * @since 2.0.0
 */

namespace Travel\Blocks\Blocks\Template;

use Travel\Blocks\Core\TemplateBlockBase;
use Travel\Blocks\Core\PreviewDataTrait;

class FAQAccordion extends TemplateBlockBase
{
    use PreviewDataTrait;

    public function __construct()
    {
        $this->name = 'faq-accordion-template';
        $this->title = 'FAQ Accordion (Template)';
        $this->description = 'Frequently Asked Questions accordion with schema markup for templates';
        $this->icon = 'editor-help';
        $this->keywords = ['faq', 'accordion', 'questions', 'answers', 'help', 'template'];
    }

    /**
     * Register block using ACF
     */
    public function register(): void
    {
        if (!function_exists('acf_register_block_type')) {
            return;
        }

        acf_register_block_type([
            'name' => $this->name,
            'title' => __($this->title, 'travel-blocks'),
            'description' => __($this->description, 'travel-blocks'),
            'category' => $this->category,
            'icon' => $this->icon,
            'keywords' => $this->keywords,
            'supports' => $this->supports,
            'render_callback' => [$this, 'render'],
            'mode' => 'preview',
            'example' => [
                'attributes' => [
                    'mode' => 'preview',
                    'data' => $this->get_preview_faqs(),
                ],
            ],
        ]);

        add_action('enqueue_block_assets', [$this, 'enqueue_assets']);
    }

    protected function render_preview(array $attributes): string
    {
        $data = [
            'title' => 'Frequently Asked Questions',
            'faqs' => $this->get_preview_faqs(),
            'is_preview' => true,
        ];

        return $this->load_template('faq-accordion', $data);
    }

    protected function render_live(int $post_id, array $attributes): string
    {
        $data = [
            'title' => get_field('faq_title') ?: 'Frequently Asked Questions',
            'faqs' => $this->get_acf_faqs_data(),
            'is_preview' => false,
        ];

        return $this->load_template('faq-accordion', $data);
    }

    /**
     * Get ACF FAQs data from taxonomy terms assigned to current post
     * Get ACF FAQs data from taxonomy terms assigned to current post
     *
     * @return array FAQs data from taxonomy
     * @return array FAQs data from taxonomy
     */
    private function get_acf_faqs_data(): array
    {
        $faqs = [];

        // Get current post ID
        $post_id = get_the_ID();

        // Get FAQ terms assigned to this package
        $faq_terms = get_the_terms($post_id, 'faq');

        if (!is_wp_error($faq_terms) && !empty($faq_terms)) {
            foreach ($faq_terms as $term) {
                // Get the 2 ACF fields:
                // 1. Pregunta - la pregunta completa
                $pregunta = get_field('pregunta', 'faq_' . $term->term_id);

                // 2. Respuesta - la respuesta completa (WYSIWYG)
                $respuesta = get_field('respuesta', 'faq_' . $term->term_id);

                // Validar que tengamos pregunta y respuesta
                if (!empty($pregunta) && !empty($respuesta)) {
                    $faqs[] = [
                        'question' => $pregunta,
                        'answer' => $respuesta, // WYSIWYG already has HTML formatting
                    ];
                }
            }
        }

        return $faqs;
    }

    /**
     * Enqueue FAQ accordion assets
     */
    public function enqueue_assets(): void
    {
        $css_path = TRAVEL_BLOCKS_PATH . 'assets/blocks/template/faq-accordion.css';
        $js_path = TRAVEL_BLOCKS_PATH . 'assets/blocks/template/faq-accordion.js';

        if (file_exists($css_path)) {
            wp_enqueue_style(
                'travel-blocks-faq-accordion',
                TRAVEL_BLOCKS_URL . 'assets/blocks/template/faq-accordion.css',
                [],
                TRAVEL_BLOCKS_VERSION
            );
        }

        if (file_exists($js_path)) {
            wp_enqueue_script(
                'travel-blocks-faq-accordion',
                TRAVEL_BLOCKS_URL . 'assets/blocks/template/faq-accordion.js',
                [],
                TRAVEL_BLOCKS_VERSION,
                true
            );
        }
    }
}
