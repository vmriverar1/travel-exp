<?php
/**
 * Block: FAQ Accordion
 *
 * Interactive accordion for frequently asked questions with Schema.org markup for SEO.
 * Generates FAQPage structured data for Google Rich Results.
 *
 * Features:
 * - Repeater field for unlimited FAQ items
 * - WYSIWYG editor for rich-text answers
 * - Optional "open by default" per item
 * - Interactive accordion with JavaScript
 * - Schema.org FAQPage JSON-LD markup
 * - SEO-optimized for Google FAQ rich results
 *
 * SEO Benefits:
 * - Automatic FAQPage schema generation
 * - Google Rich Results eligible
 * - Improved search visibility
 *
 * ⚠️ Note: Similar blocks exist in Package and Template namespaces.
 * This is the ACF-based general-purpose FAQ accordion.
 *
 * @package Travel\Blocks\ACF
 * @since 1.0.0
 * @version 1.1.0 - Refactored: namespace fix, improved Schema.org documentation
 */

namespace Travel\Blocks\ACF;

use Travel\Blocks\Core\BlockBase;

class FAQAccordion extends BlockBase
{
    public function __construct()
    {
        $this->name        = 'faq-accordion';
        $this->title       = __('FAQ Accordion', 'travel-blocks');
        $this->description = __('Frequently asked questions with accordion and SEO schema', 'travel-blocks');
        $this->category    = 'travel';
        $this->icon        = 'editor-help';
        $this->keywords    = ['faq', 'questions', 'accordion', 'help'];
        $this->mode        = 'preview';

        $this->supports = [
            'align' => true,
            'mode'  => true,
            'multiple' => true,
        ];
    }

    /**
     * Register block and its ACF fields.
     *
     * Registers ACF block type and defines field group with:
     * - section_title: Optional section heading
     * - section_description: Optional intro text
     * - faq_items: Repeater field for Q&A pairs
     *   - question: FAQ question text (required)
     *   - answer: WYSIWYG answer (required)
     *   - open_default: Whether item starts expanded
     *
     * @return void
     */
    public function register(): void
    {
        parent::register();

        // Register ACF fields for this block
        if (function_exists('acf_add_local_field_group')) {
            acf_add_local_field_group([
                'key' => 'group_block_faq_accordion',
                'title' => __('FAQ Accordion Block', 'travel-blocks'),
                'fields' => [
                    [
                        'key' => 'field_faq_title',
                        'label' => __('Section Title', 'travel-blocks'),
                        'name' => 'section_title',
                        'type' => 'text',
                        'default_value' => __('Frequently Asked Questions', 'travel-blocks'),
                    ],
                    [
                        'key' => 'field_faq_description',
                        'label' => __('Section Description', 'travel-blocks'),
                        'name' => 'section_description',
                        'type' => 'textarea',
                        'rows' => 2,
                    ],
                    [
                        'key' => 'field_faq_items',
                        'label' => __('FAQ Items', 'travel-blocks'),
                        'name' => 'faq_items',
                        'type' => 'repeater',
                        'min' => 1,
                        'layout' => 'block',
                        'button_label' => __('Add Question', 'travel-blocks'),
                        'sub_fields' => [
                            [
                                'key' => 'field_faq_question',
                                'label' => __('Question', 'travel-blocks'),
                                'name' => 'question',
                                'type' => 'text',
                                'required' => 1,
                                'placeholder' => __('What is your question?', 'travel-blocks'),
                            ],
                            [
                                'key' => 'field_faq_answer',
                                'label' => __('Answer', 'travel-blocks'),
                                'name' => 'answer',
                                'type' => 'wysiwyg',
                                'required' => 1,
                                'toolbar' => 'basic',
                                'media_upload' => 0,
                            ],
                            [
                                'key' => 'field_faq_open_default',
                                'label' => __('Open by Default', 'travel-blocks'),
                                'name' => 'open_default',
                                'type' => 'true_false',
                                'default_value' => 0,
                                'ui' => 1,
                            ],
                        ],
                    ],
                ],
                'location' => [
                    [
                        [
                            'param' => 'block',
                            'operator' => '==',
                            'value' => 'acf/faq-accordion',
                        ],
                    ],
                ],
            ]);
        }
    }

    /**
     * Render the block output.
     *
     * Generates interactive FAQ accordion with Schema.org markup:
     * - Renders section title and description
     * - Creates accordion items from repeater field
     * - Generates FAQPage JSON-LD schema for SEO
     * - Passes all data to template for rendering
     *
     * @param array  $block      Block settings and attributes
     * @param string $content    Block content (unused)
     * @param bool   $is_preview Whether block is being previewed in editor
     * @param int    $post_id    Current post ID
     *
     * @return void
     */
    public function render(array $block, string $content = '', bool $is_preview = false, int $post_id = 0): void
    {
        // Get field values
        $section_title = get_field('section_title');
        $section_description = get_field('section_description');
        $faq_items = get_field('faq_items') ?: [];

        // Prepare template data
        $data = [
            'block'               => $block,
            'is_preview'          => $is_preview,
            'section_title'       => $section_title,
            'section_description' => $section_description,
            'faq_items'           => $faq_items,
            'schema'              => $this->generate_faq_schema($faq_items),
        ];

        // Load template
        $this->load_template('faq-accordion', $data);
    }

    /**
     * Generate FAQ Schema.org markup.
     *
     * Creates FAQPage structured data in JSON-LD format for Google Rich Results.
     * Each FAQ item becomes a Question with an acceptedAnswer.
     *
     * Schema Structure:
     * - @type: FAQPage
     * - mainEntity: Array of Question objects
     *   - Each Question has name (question text)
     *   - Each Question has acceptedAnswer (Answer object with text)
     *
     * Sanitization:
     * - Uses wp_strip_all_tags() to remove HTML from schema
     * - Ensures clean text for search engines
     *
     * @param array $faq_items FAQ items from ACF repeater field
     *
     * @return string JSON-LD schema string, or empty if no items
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

    /**
     * Enqueue block-specific assets.
     *
     * Loads CSS for accordion styling and JavaScript for:
     * - Accordion expand/collapse functionality
     * - Click event handling
     * - Open/close animations
     * - "Open by default" behavior
     *
     * @return void
     */
    public function enqueue_assets(): void
    {
        wp_enqueue_style(
            'block-faq-accordion',
            TRAVEL_BLOCKS_URL . 'assets/blocks/faq-accordion.css',
            [],
            TRAVEL_BLOCKS_VERSION
        );

        wp_enqueue_script(
            'block-faq-accordion',
            TRAVEL_BLOCKS_URL . 'assets/blocks/faq-accordion.js',
            [],
            TRAVEL_BLOCKS_VERSION,
            true
        );
    }
}
