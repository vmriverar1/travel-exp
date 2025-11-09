<?php
/**
 * ACF Options Page - Header Settings
 * Permite administrar todos los elementos del header desde WordPress admin
 */

// Registrar Options Page
if (function_exists('acf_add_options_page')) {
    acf_add_options_page([
        'page_title' => 'Header Settings',
        'menu_title' => 'Header',
        'menu_slug' => 'header-settings',
        'capability' => 'edit_theme_options',
        'icon_url' => 'dashicons-align-center',
        'position' => 61,
        'redirect' => false,
    ]);
}

// Registrar Field Group
if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group([
        'key' => 'group_header_settings',
        'title' => 'Header Configuration',
        'fields' => [

            // === TAB: Header Principal ===
            [
                'key' => 'field_tab_header',
                'label' => 'Header Principal',
                'name' => '',
                'type' => 'tab',
                'placement' => 'top',
            ],

            // Logo (White for header)
            [
                'key' => 'field_header_logo',
                'label' => 'Site Logo (White)',
                'name' => 'header_logo',
                'type' => 'image',
                'instructions' => 'Upload the site logo in white for the header (recommended: PNG with transparent background, 250x55px)',
                'return_format' => 'id',
                'preview_size' => 'medium',
                'library' => 'all',
            ],

            // Logo (Color for aside menu)
            [
                'key' => 'field_aside_logo',
                'label' => 'Aside Menu Logo (Color)',
                'name' => 'aside_logo',
                'type' => 'image',
                'instructions' => 'Upload the site logo in color for the aside menu (recommended: PNG, 250x55px)',
                'return_format' => 'id',
                'preview_size' => 'medium',
                'library' => 'all',
            ],

            // Phone
            [
                'key' => 'field_header_phone',
                'label' => 'Phone Number',
                'name' => 'header_phone',
                'type' => 'text',
                'instructions' => 'Format: +1-(888)-803-8004',
                'default_value' => '+1-(888)-803-8004',
                'placeholder' => '+1-(888)-803-8004',
            ],
            [
                'key' => 'field_header_phone_link',
                'label' => 'Phone Link (for tel:)',
                'name' => 'header_phone_link',
                'type' => 'text',
                'instructions' => 'Format: +18888038004 (sin guiones)',
                'default_value' => '+18888038004',
                'placeholder' => '+18888038004',
            ],

            // Language
            [
                'key' => 'field_header_language',
                'label' => 'Language Code',
                'name' => 'header_language',
                'type' => 'text',
                'instructions' => 'Current language code (EN, ES, etc)',
                'default_value' => 'EN',
                'placeholder' => 'EN',
                'maxlength' => 2,
            ],

            // Favorites URL
            [
                'key' => 'field_header_favorites_url',
                'label' => 'Favorites Page URL',
                'name' => 'header_favorites_url',
                'type' => 'url',
                'instructions' => 'URL to favorites page',
                'default_value' => home_url('/favorites'),
            ],

            // === TAB: Aside Menu - Tour Packages ===
            [
                'key' => 'field_tab_tour_packages',
                'label' => 'Tour Packages Section',
                'name' => '',
                'type' => 'tab',
            ],
            [
                'key' => 'field_aside_tour_title',
                'label' => 'Section Title',
                'name' => 'aside_tour_title',
                'type' => 'text',
                'default_value' => 'Tour Packages',
            ],

            // === TAB: Reviews Section ===
            [
                'key' => 'field_tab_reviews',
                'label' => 'Reviews Section',
                'name' => '',
                'type' => 'tab',
            ],
            [
                'key' => 'field_aside_reviews_title',
                'label' => 'Section Title',
                'name' => 'aside_reviews_title',
                'type' => 'text',
                'default_value' => 'Hear from travelers',
            ],
            [
                'key' => 'field_aside_reviews_text',
                'label' => 'Description Text',
                'name' => 'aside_reviews_text',
                'type' => 'text',
                'default_value' => '+2315 Real stories traveling with us',
            ],
            [
                'key' => 'field_aside_review_badges',
                'label' => 'Review Badges',
                'name' => 'aside_review_badges',
                'type' => 'repeater',
                'instructions' => 'Add review platform badges (TripAdvisor, Google, etc.)',
                'layout' => 'row',
                'button_label' => 'Add Badge',
                'sub_fields' => [
                    [
                        'key' => 'field_badge_image',
                        'label' => 'Badge Image',
                        'name' => 'badge_image',
                        'type' => 'image',
                        'return_format' => 'url',
                        'preview_size' => 'thumbnail',
                        'wrapper' => ['width' => '50'],
                    ],
                    [
                        'key' => 'field_badge_url',
                        'label' => 'Badge Link URL',
                        'name' => 'badge_url',
                        'type' => 'url',
                        'instructions' => 'Optional: Link to reviews page',
                        'wrapper' => ['width' => '50'],
                    ],
                ],
            ],

            // === TAB: FAQs Section ===
            [
                'key' => 'field_tab_faqs',
                'label' => 'FAQs Section',
                'name' => '',
                'type' => 'tab',
            ],
            [
                'key' => 'field_aside_faqs_title',
                'label' => 'Section Title',
                'name' => 'aside_faqs_title',
                'type' => 'text',
                'default_value' => 'FAQs',
            ],
            [
                'key' => 'field_aside_faqs_text',
                'label' => 'Description Text',
                'name' => 'aside_faqs_text',
                'type' => 'textarea',
                'rows' => 2,
                'default_value' => 'Clear, simple answers to help you plan with confidence.',
            ],
            [
                'key' => 'field_aside_faqs_url',
                'label' => 'FAQs Page URL',
                'name' => 'aside_faqs_url',
                'type' => 'url',
                'instructions' => 'URL to FAQs page',
                'default_value' => home_url('/faqs'),
            ],
            [
                'key' => 'field_aside_faqs_button_text',
                'label' => 'Button Text',
                'name' => 'aside_faqs_button_text',
                'type' => 'text',
                'default_value' => 'Get my answers',
            ],

            // === TAB: Tailor Made Tours ===
            [
                'key' => 'field_tab_tailor',
                'label' => 'Tailor Made Tours',
                'name' => '',
                'type' => 'tab',
            ],
            [
                'key' => 'field_aside_tailor_title',
                'label' => 'Section Title',
                'name' => 'aside_tailor_title',
                'type' => 'text',
                'default_value' => 'Tailor Made Tours',
            ],
            [
                'key' => 'field_aside_tailor_text',
                'label' => 'Description Text',
                'name' => 'aside_tailor_text',
                'type' => 'textarea',
                'rows' => 2,
                'default_value' => 'Your trip, your way fully customized to your style, time and interests.',
            ],
            [
                'key' => 'field_aside_tailor_url',
                'label' => 'Custom Tours Page URL',
                'name' => 'aside_tailor_url',
                'type' => 'url',
                'instructions' => 'URL to custom tours page',
                'default_value' => home_url('/custom-tours'),
            ],
            [
                'key' => 'field_aside_tailor_button_text',
                'label' => 'Button Text',
                'name' => 'aside_tailor_button_text',
                'type' => 'text',
                'default_value' => 'Design my journey',
            ],

            // === TAB: Favorites Section ===
            [
                'key' => 'field_tab_favorites',
                'label' => 'Favorites Section',
                'name' => '',
                'type' => 'tab',
            ],
            [
                'key' => 'field_aside_favorites_title',
                'label' => 'Section Title',
                'name' => 'aside_favorites_title',
                'type' => 'text',
                'default_value' => 'Favorites',
            ],
            [
                'key' => 'field_aside_favorites_text',
                'label' => 'Description Text',
                'name' => 'aside_favorites_text',
                'type' => 'textarea',
                'rows' => 2,
                'default_value' => 'Here you will find your chosen experiences of Machu Picchu Peru.',
            ],
            [
                'key' => 'field_aside_favorites_url',
                'label' => 'Favorites Page URL',
                'name' => 'aside_favorites_url',
                'type' => 'url',
                'instructions' => 'URL to favorites page',
                'default_value' => home_url('/favorites'),
            ],
            [
                'key' => 'field_aside_favorites_button_text',
                'label' => 'Button Text',
                'name' => 'aside_favorites_button_text',
                'type' => 'text',
                'default_value' => 'My Favs',
            ],

            // === TAB: Contact Section ===
            [
                'key' => 'field_tab_contact',
                'label' => 'Contact Section',
                'name' => '',
                'type' => 'tab',
            ],
            [
                'key' => 'field_aside_contact_url',
                'label' => 'Contact Page URL',
                'name' => 'aside_contact_url',
                'type' => 'url',
                'instructions' => 'URL to contact page',
                'default_value' => home_url('/contact'),
            ],
            [
                'key' => 'field_aside_contact_button_text',
                'label' => 'Contact Button Text',
                'name' => 'aside_contact_button_text',
                'type' => 'text',
                'default_value' => 'Contact Us',
            ],

            // === TAB: Social Media ===
            [
                'key' => 'field_tab_social',
                'label' => 'Social Media',
                'name' => '',
                'type' => 'tab',
            ],
            [
                'key' => 'field_aside_social_icons',
                'label' => 'Social Media Icons',
                'name' => 'aside_social_icons',
                'type' => 'repeater',
                'instructions' => 'Add social media icons for the aside menu footer',
                'layout' => 'row',
                'button_label' => 'Add Social Icon',
                'sub_fields' => [
                    [
                        'key' => 'field_social_icon_type',
                        'label' => 'Icon Type',
                        'name' => 'icon_type',
                        'type' => 'select',
                        'choices' => [
                            'facebook' => 'Facebook',
                            'instagram' => 'Instagram',
                            'pinterest' => 'Pinterest',
                            'youtube' => 'YouTube',
                            'tiktok' => 'TikTok',
                            'twitter' => 'Twitter',
                            'linkedin' => 'LinkedIn',
                            'whatsapp' => 'WhatsApp',
                        ],
                        'default_value' => 'facebook',
                        'wrapper' => ['width' => '30'],
                    ],
                    [
                        'key' => 'field_social_icon_url',
                        'label' => 'URL',
                        'name' => 'icon_url',
                        'type' => 'url',
                        'required' => 1,
                        'wrapper' => ['width' => '70'],
                    ],
                ],
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'options_page',
                    'operator' => '==',
                    'value' => 'header-settings',
                ],
            ],
        ],
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'seamless',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
    ]);
}
