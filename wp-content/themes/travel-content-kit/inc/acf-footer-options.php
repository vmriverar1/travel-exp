<?php
/**
 * ACF Options Page: Footer Configuration
 * Configuración administrable del footer
 */

if (!function_exists('acf_add_options_page')) {
    return;
}

// Options page
acf_add_options_sub_page([
    'page_title' => __('Footer Settings', 'travel'),
    'menu_title' => __('Footer', 'travel'),
    'menu_slug' => 'footer-settings',
    'parent_slug' => 'themes.php',
    'capability' => 'manage_options',
]);

// Field Group
acf_add_local_field_group([
    'key' => 'group_footer_settings',
    'title' => 'Footer Configuration',
    'fields' => [

        // Tab: Company Info
        [
            'key' => 'field_tab_company',
            'label' => 'Company Info',
            'type' => 'tab',
        ],
        [
            'key' => 'field_footer_logo',
            'label' => 'Footer Logo',
            'name' => 'footer_logo',
            'type' => 'image',
            'return_format' => 'id',
            'preview_size' => 'medium',
            'instructions' => 'Logo displayed in footer (recommended: 250x55px)',
        ],
        [
            'key' => 'field_company_name',
            'label' => 'Company Name',
            'name' => 'company_name',
            'type' => 'text',
            'default_value' => 'Machu Picchu Peru by Valencia Travel Cusco, Inc.',
            'instructions' => 'Full company name for copyright',
        ],
        [
            'key' => 'field_company_ruc',
            'label' => 'RUC #',
            'name' => 'company_ruc',
            'type' => 'text',
            'default_value' => '20490568957',
            'instructions' => 'Company tax ID number',
        ],
        [
            'key' => 'field_company_address',
            'label' => 'Company Address',
            'name' => 'company_address',
            'type' => 'textarea',
            'rows' => 3,
            'default_value' => 'Portal Panes #123 / Centro Comercial Ruiseñores Office #306–307 Cusco — Peru',
            'instructions' => 'Physical address displayed in footer',
        ],

        // Tab: Contact Info
        [
            'key' => 'field_tab_contact',
            'label' => 'Contact Info',
            'type' => 'tab',
        ],
        [
            'key' => 'field_contact_toll_free',
            'label' => 'Toll Free (USA/Canada)',
            'name' => 'contact_toll_free',
            'type' => 'text',
            'default_value' => '1-(888)-803-8004',
            'instructions' => 'Toll-free phone number',
        ],
        [
            'key' => 'field_contact_peru_phone',
            'label' => 'Peru Office Phone',
            'name' => 'contact_peru_phone',
            'type' => 'text',
            'default_value' => '+51 84 255907',
            'instructions' => 'Main office phone in Peru',
        ],
        [
            'key' => 'field_contact_phone_24_7_1',
            'label' => '24/7 Phone #1',
            'name' => 'contact_phone_24_7_1',
            'type' => 'text',
            'default_value' => '+51 992 236 677',
            'instructions' => 'First 24/7 emergency contact',
        ],
        [
            'key' => 'field_contact_phone_24_7_2',
            'label' => '24/7 Phone #2',
            'name' => 'contact_phone_24_7_2',
            'type' => 'text',
            'default_value' => '+51 979706446',
            'instructions' => 'Second 24/7 emergency contact',
        ],
        [
            'key' => 'field_contact_email',
            'label' => 'Contact Email',
            'name' => 'contact_email',
            'type' => 'email',
            'default_value' => 'info@machupicchuperu.com',
            'instructions' => 'Main contact email',
        ],

        // Tab: Office Hours
        [
            'key' => 'field_tab_office_hours',
            'label' => 'Office Hours',
            'type' => 'tab',
        ],
        [
            'key' => 'field_office_weekdays',
            'label' => 'Weekdays Text',
            'name' => 'office_weekdays',
            'type' => 'text',
            'default_value' => 'Monday through Saturday',
            'instructions' => 'Description of working days',
        ],
        [
            'key' => 'field_office_morning',
            'label' => 'Morning Hours',
            'name' => 'office_morning',
            'type' => 'text',
            'default_value' => '8AM – 1:30PM',
            'instructions' => 'Morning schedule',
        ],
        [
            'key' => 'field_office_afternoon',
            'label' => 'Afternoon Hours',
            'name' => 'office_afternoon',
            'type' => 'text',
            'default_value' => '3PM – 5:30PM',
            'instructions' => 'Afternoon schedule',
        ],
        [
            'key' => 'field_office_sunday',
            'label' => 'Sunday Hours',
            'name' => 'office_sunday',
            'type' => 'text',
            'default_value' => 'Sunday 8AM – 1:30PM',
            'instructions' => 'Sunday schedule',
        ],

        // Tab: Social Media
        [
            'key' => 'field_tab_social',
            'label' => 'Social Media',
            'type' => 'tab',
        ],
        [
            'key' => 'field_social_networks',
            'label' => 'Social Networks',
            'name' => 'social_networks',
            'type' => 'repeater',
            'layout' => 'table',
            'button_label' => 'Add Network',
            'instructions' => 'Add social media profiles',
            'sub_fields' => [
                [
                    'key' => 'field_social_platform',
                    'label' => 'Platform',
                    'name' => 'platform',
                    'type' => 'select',
                    'choices' => [
                        'facebook' => 'Facebook',
                        'instagram' => 'Instagram',
                        'pinterest' => 'Pinterest',
                        'linkedin' => 'LinkedIn',
                        'youtube' => 'YouTube',
                    ],
                    'default_value' => 'facebook',
                ],
                [
                    'key' => 'field_social_url',
                    'label' => 'URL',
                    'name' => 'url',
                    'type' => 'url',
                    'required' => 1,
                ],
            ],
        ],
        [
            'key' => 'field_review_platforms',
            'label' => 'Review Platforms',
            'name' => 'review_platforms',
            'type' => 'repeater',
            'layout' => 'table',
            'button_label' => 'Add Platform',
            'instructions' => 'Add review platform links',
            'sub_fields' => [
                [
                    'key' => 'field_review_platform',
                    'label' => 'Platform',
                    'name' => 'platform',
                    'type' => 'select',
                    'choices' => [
                        'tripadvisor' => 'TripAdvisor',
                        'google' => 'Google Reviews',
                        'facebook' => 'Facebook Reviews',
                    ],
                    'default_value' => 'tripadvisor',
                ],
                [
                    'key' => 'field_review_url',
                    'label' => 'URL',
                    'name' => 'url',
                    'type' => 'url',
                    'required' => 1,
                ],
            ],
        ],

        // Tab: Footer Map
        [
            'key' => 'field_tab_footer_map',
            'label' => 'Footer Map',
            'type' => 'tab',
        ],
        [
            'key' => 'field_footer_map_image',
            'label' => 'Map Image',
            'name' => 'footer_map_image',
            'type' => 'image',
            'return_format' => 'url',
            'preview_size' => 'medium',
            'instructions' => 'Upload decorative world map image for footer',
        ],

        // Tab: Payment Methods
        [
            'key' => 'field_tab_payment_methods',
            'label' => 'Payment Methods',
            'type' => 'tab',
        ],
        [
            'key' => 'field_payment_methods',
            'label' => 'Accepted Payment Methods',
            'name' => 'payment_methods',
            'type' => 'repeater',
            'layout' => 'table',
            'button_label' => 'Add Payment Method',
            'instructions' => 'Add accepted credit card and payment methods',
            'sub_fields' => [
                [
                    'key' => 'field_payment_method_image',
                    'label' => 'Card/Method Image',
                    'name' => 'image',
                    'type' => 'image',
                    'return_format' => 'url',
                    'preview_size' => 'thumbnail',
                    'required' => 1,
                ],
                [
                    'key' => 'field_payment_method_name',
                    'label' => 'Name',
                    'name' => 'name',
                    'type' => 'text',
                    'instructions' => 'e.g., Visa, Mastercard, American Express',
                    'required' => 1,
                ],
            ],
        ],
        [
            'key' => 'field_payment_gateways',
            'label' => 'Payment Gateways',
            'name' => 'payment_gateways',
            'type' => 'repeater',
            'layout' => 'table',
            'button_label' => 'Add Gateway',
            'instructions' => 'Add payment gateway processors (Stripe, PayPal, etc.)',
            'sub_fields' => [
                [
                    'key' => 'field_gateway_name',
                    'label' => 'Gateway Name',
                    'name' => 'name',
                    'type' => 'text',
                    'required' => 1,
                ],
                [
                    'key' => 'field_gateway_url',
                    'label' => 'URL',
                    'name' => 'url',
                    'type' => 'url',
                    'instructions' => 'Optional link to gateway website',
                ],
            ],
        ],

    ],
    'location' => [
        [
            [
                'param' => 'options_page',
                'operator' => '==',
                'value' => 'footer-settings',
            ],
        ],
    ],
]);
