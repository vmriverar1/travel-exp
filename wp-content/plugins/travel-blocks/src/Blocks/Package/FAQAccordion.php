<?php
/**
 * Block: FAQ Accordion (Package)
 *
 * Native WordPress block for FAQs with Schema markup
 * Gets data from Package post meta fields
 *
 * @package Travel\Blocks\Blocks\Package
 * @since 1.0.0
 */

namespace Travel\Blocks\Blocks\Package;

use Travel\Blocks\Helpers\EditorHelper;

class FAQAccordion
{
    private string $name = 'faq-accordion-package';
    private string $title = 'FAQ Accordion (Package)';
    private string $description = 'Frequently asked questions with accordion and SEO schema - NO ACF';

    public function register(): void
    {
        register_block_type('travel-blocks/' . $this->name, [
            'api_version' => 2,
            'title' => __($this->title, 'travel-blocks'),
            'description' => __($this->description, 'travel-blocks'),
            'category' => 'travel',
            'icon' => 'editor-help',
            'keywords' => ['faq', 'questions', 'accordion', 'help', 'package'],
            'supports' => [
                'anchor' => true,
                'html' => false,
            ],
            'render_callback' => [$this, 'render'],
        ]);
        add_action('enqueue_block_assets', [$this, 'enqueue_assets']);
    }

    public function enqueue_assets(): void
    {
        if (!is_admin()) {
            // Reuse ACF FAQ Accordion assets
            wp_enqueue_style(
                'faq-accordion-style',
                TRAVEL_BLOCKS_URL . 'assets/blocks/faq-accordion.css',
                [],
                TRAVEL_BLOCKS_VERSION
            );

            wp_enqueue_script(
                'faq-accordion-script',
                TRAVEL_BLOCKS_URL . 'assets/blocks/faq-accordion.js',
                [],
                TRAVEL_BLOCKS_VERSION,
                true
            );
        }
    }

    public function render($attributes, $content, $block): string
    {
        try {
            $post_id = get_the_ID();
            $is_preview = EditorHelper::is_editor_mode($post_id);

            if ($is_preview || !$post_id) {
                $faq_data = $this->get_preview_data();
            } else {
                $faq_data = $this->get_post_data($post_id);
            }

            if (empty($faq_data['faq_items'])) {
                return '';
            }

            $data = [
                'block_id' => 'faq-accordion-' . uniqid(),
                'class_name' => 'faq-accordion' . (!empty($attributes['className']) ? ' ' . $attributes['className'] : ''),
                'section_title' => $faq_data['section_title'] ?? '',
                'section_description' => $faq_data['section_description'] ?? '',
                'faq_items' => $faq_data['faq_items'],
                'schema' => $this->generate_faq_schema($faq_data['faq_items']),
                'is_preview' => $is_preview,
            ];

            ob_start();
            $this->load_template('faq-accordion', $data);
            return ob_get_clean();

        } catch (\Exception $e) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                return '<div style="padding: 10px; background: #ffebee; border: 1px solid #f44336;">' .
                       '<p>Error en FAQ Accordion: ' . esc_html($e->getMessage()) . '</p>' .
                       '</div>';
            }
            return '';
        }
    }

    private function get_preview_data(): array
    {
        return [
            'section_title' => 'Frequently Asked Questions',
            'section_description' => 'Find answers to common questions about this package',
            'faq_items' => [
                [
                    'question' => 'What is the difficulty level of this trek?',
                    'answer' => 'This trek is rated as moderate. You should have a good level of fitness and be comfortable hiking for several hours per day.',
                    'open_default' => true,
                ],
                [
                    'question' => 'What is included in the package price?',
                    'answer' => 'The package includes all entrance fees, professional guide, accommodation, meals as specified in the itinerary, and transportation.',
                    'open_default' => false,
                ],
                [
                    'question' => 'Can I customize this itinerary?',
                    'answer' => 'Yes! We offer fully customizable itineraries. Contact us to discuss your specific needs and preferences.',
                    'open_default' => false,
                ],
                [
                    'question' => 'What is your cancellation policy?',
                    'answer' => 'Cancellations made 30+ days before departure receive a full refund. 15-29 days: 50% refund. Less than 15 days: no refund.',
                    'open_default' => false,
                ],
            ],
        ];
    }

    private function get_post_data(int $post_id): array
    {
        $faqs = get_post_meta($post_id, 'faqs', true);

        if (!is_array($faqs)) {
            return ['faq_items' => []];
        }

        // Transform FAQs to expected format
        $faq_items = [];
        foreach ($faqs as $index => $faq) {
            if (is_array($faq) && !empty($faq['question']) && !empty($faq['answer'])) {
                $faq_items[] = [
                    'question' => $faq['question'],
                    'answer' => $faq['answer'],
                    'open_default' => $index === 0, // First question open by default
                ];
            }
        }

        return [
            'section_title' => get_post_meta($post_id, 'faq_section_title', true) ?: __('Frequently Asked Questions', 'travel-blocks'),
            'section_description' => get_post_meta($post_id, 'faq_section_description', true),
            'faq_items' => $faq_items,
        ];
    }

    /**
     * Generate FAQ Schema markup for SEO
     */
    private function generate_faq_schema(array $faq_items): string
    {
        if (empty($faq_items)) {
            return '';
        }

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => [],
        ];

        foreach ($faq_items as $item) {
            if (empty($item['question']) || empty($item['answer'])) {
                continue;
            }

            $schema['mainEntity'][] = [
                '@type' => 'Question',
                'name' => wp_strip_all_tags($item['question']),
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => wp_strip_all_tags($item['answer']),
                ],
            ];
        }

        return wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
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
