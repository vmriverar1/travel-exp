<?php
namespace Aurora\ACFKit\FieldGroups;

use Aurora\ACFKit\Core\FieldGroup;

class PackageAdditionalContent extends FieldGroup
{
    public function register(): void
    {
        if (!function_exists('acf_add_local_field_group')) return;

        acf_add_local_field_group([
            'key' => 'group_package_additional_content',
            'title' => '📝 Package - Additional Content',
            'fields' => [

                // ===== FLEXIBLE CONTENT SECTIONS =====
                [
                    'key' => 'field_package_additional_sections',
                    'label' => '📄 Additional Sections',
                    'name' => 'additional_sections',
                    'type' => 'repeater',
                    'instructions' => 'Add custom sections to the package page (FAQs, Tips, Equipment, etc.).',
                    'required' => 0,
                    'min' => 0,
                    'max' => 20,
                    'layout' => 'block',
                    'button_label' => 'Add Section',
                    'collapsed' => 'field_section_title',
                    'sub_fields' => [
                        [
                            'key' => 'field_section_active',
                            'label' => 'Active',
                            'name' => 'active',
                            'type' => 'true_false',
                            'instructions' => 'Enable or disable this section.',
                            'required' => 0,
                            'default_value' => 1,
                            'ui' => 1,
                            'wrapper' => ['width' => 15],
                        ],
                        [
                            'key' => 'field_section_order',
                            'label' => 'Order',
                            'name' => 'order',
                            'type' => 'number',
                            'instructions' => 'Display order (lower numbers appear first).',
                            'required' => 1,
                            'default_value' => 1,
                            'min' => 1,
                            'max' => 100,
                            'wrapper' => ['width' => 15],
                        ],
                        [
                            'key' => 'field_section_type',
                            'label' => 'Section Type',
                            'name' => 'type',
                            'type' => 'select',
                            'instructions' => 'Select the type of content section.',
                            'required' => 1,
                            'choices' => [
                                'faq' => '❓ FAQ',
                                'tips' => '💡 Travel Tips',
                                'equipment' => '🎒 Equipment List',
                                'requirements' => '📋 Requirements',
                                'insurance' => '🛡️ Insurance',
                                'cancellation' => '🚫 Cancellation Policy',
                                'custom' => '📝 Custom Section',
                            ],
                            'default_value' => 'custom',
                            'ui' => 1,
                            'wrapper' => ['width' => 35],
                        ],
                        [
                            'key' => 'field_section_title',
                            'label' => 'Section Title',
                            'name' => 'title',
                            'type' => 'text',
                            'instructions' => 'Title for this section.',
                            'required' => 1,
                            'maxlength' => 100,
                            'placeholder' => 'Frequently Asked Questions',
                            'wrapper' => ['width' => 35],
                        ],
                        [
                            'key' => 'field_section_icon',
                            'label' => 'Icon',
                            'name' => 'icon',
                            'type' => 'text',
                            'instructions' => 'Icon class or emoji for this section (optional).',
                            'required' => 0,
                            'maxlength' => 50,
                            'placeholder' => 'dashicon-class or 🎒',
                            'wrapper' => ['width' => 50],
                        ],
                        [
                            'key' => 'field_section_style',
                            'label' => 'Display Style',
                            'name' => 'style',
                            'type' => 'select',
                            'instructions' => 'How to display this section.',
                            'required' => 0,
                            'choices' => [
                                'default' => 'Default',
                                'accordion' => 'Accordion',
                                'tabs' => 'Tabs',
                                'cards' => 'Cards',
                                'list' => 'List',
                            ],
                            'default_value' => 'default',
                            'ui' => 1,
                            'wrapper' => ['width' => 50],
                        ],
                        [
                            'key' => 'field_section_content',
                            'label' => 'Content',
                            'name' => 'content',
                            'type' => 'wysiwyg',
                            'instructions' => 'Main content for this section.',
                            'required' => 1,
                            'tabs' => 'all',
                            'toolbar' => 'full',
                            'media_upload' => 1,
                        ],
                        [
                            'key' => 'field_section_items',
                            'label' => 'Items List',
                            'name' => 'items',
                            'type' => 'repeater',
                            'instructions' => 'Add individual items for FAQ, equipment list, etc.',
                            'required' => 0,
                            'min' => 0,
                            'max' => 50,
                            'layout' => 'table',
                            'button_label' => 'Add Item',
                            'sub_fields' => [
                                [
                                    'key' => 'field_item_label',
                                    'label' => 'Label/Question',
                                    'name' => 'label',
                                    'type' => 'text',
                                    'required' => 1,
                                    'maxlength' => 200,
                                    'wrapper' => ['width' => 40],
                                ],
                                [
                                    'key' => 'field_item_content',
                                    'label' => 'Content/Answer',
                                    'name' => 'content',
                                    'type' => 'textarea',
                                    'required' => 0,
                                    'rows' => 3,
                                    'maxlength' => 1000,
                                    'wrapper' => ['width' => 60],
                                ],
                            ],
                        ],
                    ],
                ],

                // ===== IMPORTANT NOTES =====
                [
                    'key' => 'field_package_important_notes',
                    'label' => '⚠️ Important Notes',
                    'name' => 'important_notes',
                    'type' => 'wysiwyg',
                    'instructions' => 'Critical information travelers must know before booking.',
                    'required' => 0,
                    'tabs' => 'visual',
                    'toolbar' => 'basic',
                    'media_upload' => 0,
                ],

                // ===== TERMS & CONDITIONS =====
                [
                    'key' => 'field_package_terms',
                    'label' => '📜 Terms & Conditions',
                    'name' => 'terms',
                    'type' => 'wysiwyg',
                    'instructions' => 'Specific terms and conditions for this package.',
                    'required' => 0,
                    'tabs' => 'visual',
                    'toolbar' => 'basic',
                    'media_upload' => 0,
                ],

            ],
            'location' => [
                [
                    [
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'package',
                    ],
                ],
            ],
            'menu_order' => 60,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
        ]);
    }
}
