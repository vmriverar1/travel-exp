<?php
/**
 * ACF Field Group: FAQ Accordion Block
 *
 * Fields for the FAQ Accordion template block
 *
 * @package Aurora\ACFKit\FieldGroups
 */

namespace Aurora\ACFKit\FieldGroups;

class BlockFAQAccordion
{
    public function __construct()
    {
        add_action('acf/include_fields', [$this, 'register_fields']);
    }

    public function register_fields(): void
    {
        if (!function_exists('acf_add_local_field_group')) {
            return;
        }

        acf_add_local_field_group([
            'key' => 'group_block_faq_accordion',
            'title' => 'FAQ Accordion Block',
            'fields' => [
                [
                    'key' => 'field_faq_title',
                    'label' => 'Section Title',
                    'name' => 'faq_title',
                    'type' => 'text',
                    'required' => 0,
                    'default_value' => 'Frequently Asked Questions',
                    'placeholder' => 'Frequently Asked Questions',
                ],
                [
                    'key' => 'field_faq_items',
                    'label' => 'FAQ Items',
                    'name' => 'faq_items',
                    'type' => 'repeater',
                    'required' => 1,
                    'layout' => 'block',
                    'button_label' => 'Add Question',
                    'min' => 1,
                    'sub_fields' => [
                        [
                            'key' => 'field_faq_question',
                            'label' => 'Question',
                            'name' => 'question',
                            'type' => 'text',
                            'required' => 1,
                            'placeholder' => 'Enter your question here',
                        ],
                        [
                            'key' => 'field_faq_answer',
                            'label' => 'Answer',
                            'name' => 'answer',
                            'type' => 'textarea',
                            'required' => 1,
                            'rows' => 4,
                            'placeholder' => 'Enter your answer here',
                        ],
                    ],
                ],
            ],
            'location' => [
                [
                    [
                        'param' => 'block',
                        'operator' => '==',
                        'value' => 'acf/faq-accordion-template',
                    ],
                ],
            ],
            'menu_order' => 0,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
        ]);
    }
}
