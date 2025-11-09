<?php
/**
 * ACF Field Group: Promo Cards Block
 *
 * Fields for the Promo Cards template block
 *
 * @package Aurora\ACFKit\FieldGroups
 */

namespace Aurora\ACFKit\FieldGroups;

class BlockPromoCards
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
            'key' => 'group_block_promo_cards',
            'title' => 'Promo Cards Block',
            'fields' => [
                // Card 1
                [
                    'key' => 'field_promo_cards_card1_tab',
                    'label' => 'Card 1',
                    'name' => '',
                    'type' => 'tab',
                    'placement' => 'top',
                ],
                [
                    'key' => 'field_promo_card_1_image',
                    'label' => 'Image',
                    'name' => 'promo_card_1_image',
                    'type' => 'image',
                    'required' => 1,
                    'return_format' => 'array',
                    'preview_size' => 'medium',
                    'library' => 'all',
                    'instructions' => 'Select a circular image (square images work best)',
                ],
                [
                    'key' => 'field_promo_card_1_title',
                    'label' => 'Title',
                    'name' => 'promo_card_1_title',
                    'type' => 'text',
                    'required' => 1,
                    'placeholder' => 'Enter card title',
                ],
                [
                    'key' => 'field_promo_card_1_description',
                    'label' => 'Description',
                    'name' => 'promo_card_1_description',
                    'type' => 'textarea',
                    'required' => 1,
                    'rows' => 3,
                    'placeholder' => 'Enter card description',
                ],
                [
                    'key' => 'field_promo_card_1_cta_text',
                    'label' => 'CTA Text',
                    'name' => 'promo_card_1_cta_text',
                    'type' => 'text',
                    'required' => 0,
                    'placeholder' => 'Learn More',
                ],
                [
                    'key' => 'field_promo_card_1_cta_url',
                    'label' => 'CTA URL',
                    'name' => 'promo_card_1_cta_url',
                    'type' => 'url',
                    'required' => 0,
                    'placeholder' => 'https://',
                ],

                // Card 2
                [
                    'key' => 'field_promo_cards_card2_tab',
                    'label' => 'Card 2',
                    'name' => '',
                    'type' => 'tab',
                    'placement' => 'top',
                ],
                [
                    'key' => 'field_promo_card_2_image',
                    'label' => 'Image',
                    'name' => 'promo_card_2_image',
                    'type' => 'image',
                    'required' => 1,
                    'return_format' => 'array',
                    'preview_size' => 'medium',
                    'library' => 'all',
                    'instructions' => 'Select a circular image (square images work best)',
                ],
                [
                    'key' => 'field_promo_card_2_title',
                    'label' => 'Title',
                    'name' => 'promo_card_2_title',
                    'type' => 'text',
                    'required' => 1,
                    'placeholder' => 'Enter card title',
                ],
                [
                    'key' => 'field_promo_card_2_description',
                    'label' => 'Description',
                    'name' => 'promo_card_2_description',
                    'type' => 'textarea',
                    'required' => 1,
                    'rows' => 3,
                    'placeholder' => 'Enter card description',
                ],
                [
                    'key' => 'field_promo_card_2_cta_text',
                    'label' => 'CTA Text',
                    'name' => 'promo_card_2_cta_text',
                    'type' => 'text',
                    'required' => 0,
                    'placeholder' => 'Learn More',
                ],
                [
                    'key' => 'field_promo_card_2_cta_url',
                    'label' => 'CTA URL',
                    'name' => 'promo_card_2_cta_url',
                    'type' => 'url',
                    'required' => 0,
                    'placeholder' => 'https://',
                ],
            ],
            'location' => [
                [
                    [
                        'param' => 'block',
                        'operator' => '==',
                        'value' => 'acf/promo-cards',
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
