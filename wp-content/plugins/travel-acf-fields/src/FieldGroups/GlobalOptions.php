<?php
/**
 * Field Group: Global Options
 *
 * Site-wide settings: logo, contact info, social media, etc.
 *
 * @package Aurora\ACFKit\FieldGroups
 * @since 1.0.0
 */

namespace Aurora\ACFKit\FieldGroups;

use Aurora\ACFKit\Core\FieldGroup;

class GlobalOptions extends FieldGroup
{
    public function __construct()
    {
        $this->key   = 'group_global_options';
        $this->title = __('Global Options', 'travel');

        $this->fields = [
            // ========== TAB: HEADER ==========
            [
                'key' => 'field_tab_header',
                'label' => __('🎯 Header', 'travel'),
                'name' => '',
                'type' => 'tab',
                'placement' => 'left',
            ],
            // Logo (White for header)
            [
                'key' => 'field_header_logo_white',
                'label' => __('Site Logo (White)', 'travel'),
                'name' => 'header_logo',
                'type' => 'image',
                'instructions' => __('Upload the site logo in white for the header (recommended: PNG with transparent background, 250x55px)', 'travel'),
                'return_format' => 'id',
                'preview_size' => 'medium',
                'library' => 'all',
                'wrapper' => ['width' => 50],
            ],
            // Logo (Color for aside menu)
            [
                'key' => 'field_aside_logo_color',
                'label' => __('Aside Menu Logo (Color)', 'travel'),
                'name' => 'aside_logo',
                'type' => 'image',
                'instructions' => __('Upload the site logo in color for the aside menu (recommended: PNG, 250x55px)', 'travel'),
                'return_format' => 'id',
                'preview_size' => 'medium',
                'library' => 'all',
                'wrapper' => ['width' => 50],
            ],
            // Phone
            [
                'key' => 'field_header_phone_main',
                'label' => __('Phone Number', 'travel'),
                'name' => 'header_phone',
                'type' => 'text',
                'instructions' => __('Format: +1-(888)-803-8004', 'travel'),
                'default_value' => '+1-(888)-803-8004',
                'placeholder' => '+1-(888)-803-8004',
                'wrapper' => ['width' => 50],
            ],
            [
                'key' => 'field_header_phone_link_main',
                'label' => __('Phone Link (for tel:)', 'travel'),
                'name' => 'header_phone_link',
                'type' => 'text',
                'instructions' => __('Format: +18888038004 (sin guiones)', 'travel'),
                'default_value' => '+18888038004',
                'placeholder' => '+18888038004',
                'wrapper' => ['width' => 50],
            ],
            // Language
            [
                'key' => 'field_header_language_code',
                'label' => __('Language Code', 'travel'),
                'name' => 'header_language',
                'type' => 'text',
                'instructions' => __('Current language code (EN, ES, etc)', 'travel'),
                'default_value' => 'EN',
                'placeholder' => 'EN',
                'maxlength' => 2,
                'wrapper' => ['width' => 50],
            ],
            // Favorites URL
            [
                'key' => 'field_header_favorites_url_main',
                'label' => __('Favorites Page URL', 'travel'),
                'name' => 'header_favorites_url',
                'type' => 'url',
                'instructions' => __('URL to favorites page', 'travel'),
                'default_value' => home_url('/favorites'),
                'wrapper' => ['width' => 50],
            ],

            // ========== TAB: ASIDE MENU - TOUR PACKAGES ==========
            [
                'key' => 'field_tab_aside_tour_packages',
                'label' => __('📦 Aside - Tour Packages', 'travel'),
                'name' => '',
                'type' => 'tab',
                'placement' => 'left',
            ],
            [
                'key' => 'field_aside_tour_title_main',
                'label' => __('Section Title', 'travel'),
                'name' => 'aside_tour_title',
                'type' => 'text',
                'default_value' => 'Tour Packages',
            ],

            // ========== TAB: ASIDE MENU - REVIEWS ==========
            [
                'key' => 'field_tab_aside_reviews',
                'label' => __('⭐ Aside - Reviews', 'travel'),
                'name' => '',
                'type' => 'tab',
                'placement' => 'left',
            ],
            [
                'key' => 'field_aside_reviews_title_main',
                'label' => __('Section Title', 'travel'),
                'name' => 'aside_reviews_title',
                'type' => 'text',
                'default_value' => 'Hear from travelers',
            ],
            [
                'key' => 'field_aside_reviews_text_main',
                'label' => __('Description Text', 'travel'),
                'name' => 'aside_reviews_text',
                'type' => 'text',
                'default_value' => '+2315 Real stories traveling with us',
            ],
            [
                'key' => 'field_aside_review_badges_main',
                'label' => __('Review Badges', 'travel'),
                'name' => 'aside_review_badges',
                'type' => 'repeater',
                'instructions' => __('Add review platform badges (TripAdvisor, Google, etc.)', 'travel'),
                'layout' => 'row',
                'button_label' => __('Add Badge', 'travel'),
                'sub_fields' => [
                    [
                        'key' => 'field_badge_image_main',
                        'label' => __('Badge Image', 'travel'),
                        'name' => 'badge_image',
                        'type' => 'image',
                        'return_format' => 'url',
                        'preview_size' => 'thumbnail',
                        'wrapper' => ['width' => '50'],
                    ],
                    [
                        'key' => 'field_badge_url_main',
                        'label' => __('Badge Link URL', 'travel'),
                        'name' => 'badge_url',
                        'type' => 'url',
                        'instructions' => __('Optional: Link to reviews page', 'travel'),
                        'wrapper' => ['width' => '50'],
                    ],
                ],
            ],

            // ========== TAB: ASIDE MENU - FAQS ==========
            [
                'key' => 'field_tab_aside_faqs',
                'label' => __('❓ Aside - FAQs', 'travel'),
                'name' => '',
                'type' => 'tab',
                'placement' => 'left',
            ],
            [
                'key' => 'field_aside_faqs_title_main',
                'label' => __('Section Title', 'travel'),
                'name' => 'aside_faqs_title',
                'type' => 'text',
                'default_value' => 'FAQs',
            ],
            [
                'key' => 'field_aside_faqs_text_main',
                'label' => __('Description Text', 'travel'),
                'name' => 'aside_faqs_text',
                'type' => 'textarea',
                'rows' => 2,
                'default_value' => 'Clear, simple answers to help you plan with confidence.',
            ],
            [
                'key' => 'field_aside_faqs_url_main',
                'label' => __('FAQs Page URL', 'travel'),
                'name' => 'aside_faqs_url',
                'type' => 'url',
                'instructions' => __('URL to FAQs page', 'travel'),
                'default_value' => home_url('/faqs'),
            ],
            [
                'key' => 'field_aside_faqs_button_text_main',
                'label' => __('Button Text', 'travel'),
                'name' => 'aside_faqs_button_text',
                'type' => 'text',
                'default_value' => 'Get my answers',
            ],

            // ========== TAB: ASIDE MENU - TAILOR MADE ==========
            [
                'key' => 'field_tab_aside_tailor',
                'label' => __('✂️ Aside - Tailor Made', 'travel'),
                'name' => '',
                'type' => 'tab',
                'placement' => 'left',
            ],
            [
                'key' => 'field_aside_tailor_title_main',
                'label' => __('Section Title', 'travel'),
                'name' => 'aside_tailor_title',
                'type' => 'text',
                'default_value' => 'Tailor Made Tours',
            ],
            [
                'key' => 'field_aside_tailor_text_main',
                'label' => __('Description Text', 'travel'),
                'name' => 'aside_tailor_text',
                'type' => 'textarea',
                'rows' => 2,
                'default_value' => 'Your trip, your way fully customized to your style, time and interests.',
            ],
            [
                'key' => 'field_aside_tailor_url_main',
                'label' => __('Custom Tours Page URL', 'travel'),
                'name' => 'aside_tailor_url',
                'type' => 'url',
                'instructions' => __('URL to custom tours page', 'travel'),
                'default_value' => home_url('/custom-tours'),
            ],
            [
                'key' => 'field_aside_tailor_button_text_main',
                'label' => __('Button Text', 'travel'),
                'name' => 'aside_tailor_button_text',
                'type' => 'text',
                'default_value' => 'Design my journey',
            ],

            // ========== TAB: ASIDE MENU - FAVORITES ==========
            [
                'key' => 'field_tab_aside_favorites',
                'label' => __('❤️ Aside - Favorites', 'travel'),
                'name' => '',
                'type' => 'tab',
                'placement' => 'left',
            ],
            [
                'key' => 'field_aside_favorites_title_main',
                'label' => __('Section Title', 'travel'),
                'name' => 'aside_favorites_title',
                'type' => 'text',
                'default_value' => 'Favorites',
            ],
            [
                'key' => 'field_aside_favorites_text_main',
                'label' => __('Description Text', 'travel'),
                'name' => 'aside_favorites_text',
                'type' => 'textarea',
                'rows' => 2,
                'default_value' => 'Here you will find your chosen experiences of Machu Picchu Peru.',
            ],
            [
                'key' => 'field_aside_favorites_url_main',
                'label' => __('Favorites Page URL', 'travel'),
                'name' => 'aside_favorites_url',
                'type' => 'url',
                'instructions' => __('URL to favorites page', 'travel'),
                'default_value' => home_url('/favorites'),
            ],
            [
                'key' => 'field_aside_favorites_button_text_main',
                'label' => __('Button Text', 'travel'),
                'name' => 'aside_favorites_button_text',
                'type' => 'text',
                'default_value' => 'My Favs',
            ],

            // ========== TAB: ASIDE MENU - CONTACT ==========
            [
                'key' => 'field_tab_aside_contact',
                'label' => __('📧 Aside - Contact', 'travel'),
                'name' => '',
                'type' => 'tab',
                'placement' => 'left',
            ],
            [
                'key' => 'field_aside_contact_url_main',
                'label' => __('Contact Page URL', 'travel'),
                'name' => 'aside_contact_url',
                'type' => 'url',
                'instructions' => __('URL to contact page', 'travel'),
                'default_value' => home_url('/contact'),
            ],
            [
                'key' => 'field_aside_contact_button_text_main',
                'label' => __('Contact Button Text', 'travel'),
                'name' => 'aside_contact_button_text',
                'type' => 'text',
                'default_value' => 'Contact Us',
            ],

            // ========== TAB: ASIDE MENU - SOCIAL MEDIA ==========
            [
                'key' => 'field_tab_aside_social',
                'label' => __('🌐 Aside - Social Media', 'travel'),
                'name' => '',
                'type' => 'tab',
                'placement' => 'left',
            ],
            [
                'key' => 'field_aside_social_icons_main',
                'label' => __('Social Media Icons', 'travel'),
                'name' => 'aside_social_icons',
                'type' => 'repeater',
                'instructions' => __('Add social media icons for the aside menu footer', 'travel'),
                'layout' => 'row',
                'button_label' => __('Add Social Icon', 'travel'),
                'sub_fields' => [
                    [
                        'key' => 'field_social_icon_type_main',
                        'label' => __('Icon Type', 'travel'),
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
                        'key' => 'field_social_icon_url_main',
                        'label' => __('URL', 'travel'),
                        'name' => 'icon_url',
                        'type' => 'url',
                        'required' => 1,
                        'wrapper' => ['width' => '70'],
                    ],
                ],
            ],

            // ========== TAB: FOOTER - COMPANY INFO ==========
            [
                'key' => 'field_tab_footer_company',
                'label' => __('🏢 Footer - Company Info', 'travel'),
                'name' => '',
                'type' => 'tab',
                'placement' => 'left',
            ],
            [
                'key' => 'field_footer_logo_main',
                'label' => __('Footer Logo', 'travel'),
                'name' => 'footer_logo',
                'type' => 'image',
                'return_format' => 'id',
                'preview_size' => 'medium',
                'instructions' => __('Logo displayed in footer (recommended: 250x55px)', 'travel'),
            ],
            [
                'key' => 'field_company_name_footer',
                'label' => __('Company Name', 'travel'),
                'name' => 'company_name',
                'type' => 'text',
                'default_value' => 'Machu Picchu Peru by Valencia Travel Cusco, Inc.',
                'instructions' => __('Full company name for copyright', 'travel'),
            ],
            [
                'key' => 'field_company_ruc_footer',
                'label' => __('RUC #', 'travel'),
                'name' => 'company_ruc',
                'type' => 'text',
                'default_value' => '20490568957',
                'instructions' => __('Company tax ID number', 'travel'),
                'wrapper' => ['width' => 50],
            ],
            [
                'key' => 'field_company_address_footer',
                'label' => __('Company Address', 'travel'),
                'name' => 'company_address',
                'type' => 'textarea',
                'rows' => 3,
                'default_value' => 'Portal Panes #123 / Centro Comercial Ruiseñores Office #306–307 Cusco — Peru',
                'instructions' => __('Physical address displayed in footer', 'travel'),
                'wrapper' => ['width' => 50],
            ],

            // ========== TAB: FOOTER - CONTACT INFO ==========
            [
                'key' => 'field_tab_footer_contact',
                'label' => __('📲 Footer - Contact', 'travel'),
                'name' => '',
                'type' => 'tab',
                'placement' => 'left',
            ],
            [
                'key' => 'field_contact_toll_free_footer',
                'label' => __('Toll Free (USA/Canada)', 'travel'),
                'name' => 'contact_toll_free',
                'type' => 'text',
                'default_value' => '1-(888)-803-8004',
                'instructions' => __('Toll-free phone number', 'travel'),
                'wrapper' => ['width' => 50],
            ],
            [
                'key' => 'field_contact_peru_phone_footer',
                'label' => __('Peru Office Phone', 'travel'),
                'name' => 'contact_peru_phone',
                'type' => 'text',
                'default_value' => '+51 84 255907',
                'instructions' => __('Main office phone in Peru', 'travel'),
                'wrapper' => ['width' => 50],
            ],
            [
                'key' => 'field_contact_phone_24_7_1_footer',
                'label' => __('24/7 Phone #1', 'travel'),
                'name' => 'contact_phone_24_7_1',
                'type' => 'text',
                'default_value' => '+51 992 236 677',
                'instructions' => __('First 24/7 emergency contact', 'travel'),
                'wrapper' => ['width' => 50],
            ],
            [
                'key' => 'field_contact_phone_24_7_2_footer',
                'label' => __('24/7 Phone #2', 'travel'),
                'name' => 'contact_phone_24_7_2',
                'type' => 'text',
                'default_value' => '+51 979706446',
                'instructions' => __('Second 24/7 emergency contact', 'travel'),
                'wrapper' => ['width' => 50],
            ],
            [
                'key' => 'field_contact_email_footer',
                'label' => __('Contact Email', 'travel'),
                'name' => 'contact_email',
                'type' => 'email',
                'default_value' => 'info@machupicchuperu.com',
                'instructions' => __('Main contact email', 'travel'),
            ],

            // ========== TAB: FOOTER - OFFICE HOURS ==========
            [
                'key' => 'field_tab_footer_office_hours',
                'label' => __('⏰ Footer - Office Hours', 'travel'),
                'name' => '',
                'type' => 'tab',
                'placement' => 'left',
            ],
            [
                'key' => 'field_office_weekdays_footer',
                'label' => __('Weekdays Text', 'travel'),
                'name' => 'office_weekdays',
                'type' => 'text',
                'default_value' => 'Monday through Saturday',
                'instructions' => __('Description of working days', 'travel'),
            ],
            [
                'key' => 'field_office_morning_footer',
                'label' => __('Morning Hours', 'travel'),
                'name' => 'office_morning',
                'type' => 'text',
                'default_value' => '8AM – 1:30PM',
                'instructions' => __('Morning schedule', 'travel'),
                'wrapper' => ['width' => 33.33],
            ],
            [
                'key' => 'field_office_afternoon_footer',
                'label' => __('Afternoon Hours', 'travel'),
                'name' => 'office_afternoon',
                'type' => 'text',
                'default_value' => '3PM – 5:30PM',
                'instructions' => __('Afternoon schedule', 'travel'),
                'wrapper' => ['width' => 33.33],
            ],
            [
                'key' => 'field_office_sunday_footer',
                'label' => __('Sunday Hours', 'travel'),
                'name' => 'office_sunday',
                'type' => 'text',
                'default_value' => 'Sunday 8AM – 1:30PM',
                'instructions' => __('Sunday schedule', 'travel'),
                'wrapper' => ['width' => 33.33],
            ],

            // ========== TAB: FOOTER - SOCIAL MEDIA ==========
            [
                'key' => 'field_tab_footer_social',
                'label' => __('🌍 Footer - Social Media', 'travel'),
                'name' => '',
                'type' => 'tab',
                'placement' => 'left',
            ],
            [
                'key' => 'field_social_networks_footer',
                'label' => __('Social Networks', 'travel'),
                'name' => 'social_networks',
                'type' => 'repeater',
                'layout' => 'table',
                'button_label' => __('Add Network', 'travel'),
                'instructions' => __('Add social media profiles for footer', 'travel'),
                'sub_fields' => [
                    [
                        'key' => 'field_social_platform_footer',
                        'label' => __('Platform', 'travel'),
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
                        'key' => 'field_social_url_footer',
                        'label' => __('URL', 'travel'),
                        'name' => 'url',
                        'type' => 'url',
                        'required' => 1,
                    ],
                ],
            ],
            [
                'key' => 'field_review_platforms_footer',
                'label' => __('Review Platforms', 'travel'),
                'name' => 'review_platforms',
                'type' => 'repeater',
                'layout' => 'table',
                'button_label' => __('Add Platform', 'travel'),
                'instructions' => __('Add review platform links for footer', 'travel'),
                'sub_fields' => [
                    [
                        'key' => 'field_review_platform_footer',
                        'label' => __('Platform', 'travel'),
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
                        'key' => 'field_review_url_footer',
                        'label' => __('URL', 'travel'),
                        'name' => 'url',
                        'type' => 'url',
                        'required' => 1,
                    ],
                ],
            ],

            // ========== TAB: FOOTER - MAP & PAYMENT ==========
            [
                'key' => 'field_tab_footer_extras',
                'label' => __('🗺️ Footer - Map & Payment', 'travel'),
                'name' => '',
                'type' => 'tab',
                'placement' => 'left',
            ],
            [
                'key' => 'field_footer_map_image_main',
                'label' => __('Map Image', 'travel'),
                'name' => 'footer_map_image',
                'type' => 'image',
                'return_format' => 'url',
                'preview_size' => 'medium',
                'instructions' => __('Upload decorative world map image for footer', 'travel'),
            ],
            [
                'key' => 'field_payment_methods_footer',
                'label' => __('Accepted Payment Methods', 'travel'),
                'name' => 'payment_methods',
                'type' => 'repeater',
                'layout' => 'table',
                'button_label' => __('Add Payment Method', 'travel'),
                'instructions' => __('Add accepted credit card and payment methods', 'travel'),
                'sub_fields' => [
                    [
                        'key' => 'field_payment_method_image_footer',
                        'label' => __('Card/Method Image', 'travel'),
                        'name' => 'image',
                        'type' => 'image',
                        'return_format' => 'url',
                        'preview_size' => 'thumbnail',
                        'required' => 1,
                    ],
                    [
                        'key' => 'field_payment_method_name_footer',
                        'label' => __('Name', 'travel'),
                        'name' => 'name',
                        'type' => 'text',
                        'instructions' => __('e.g., Visa, Mastercard, American Express', 'travel'),
                        'required' => 1,
                    ],
                ],
            ],
            [
                'key' => 'field_payment_gateways_footer',
                'label' => __('Payment Gateways', 'travel'),
                'name' => 'payment_gateways',
                'type' => 'repeater',
                'layout' => 'table',
                'button_label' => __('Add Gateway', 'travel'),
                'instructions' => __('Add payment gateway processors (Stripe, PayPal, etc.)', 'travel'),
                'sub_fields' => [
                    [
                        'key' => 'field_gateway_name_footer',
                        'label' => __('Gateway Name', 'travel'),
                        'name' => 'name',
                        'type' => 'text',
                        'required' => 1,
                    ],
                    [
                        'key' => 'field_gateway_url_footer',
                        'label' => __('URL', 'travel'),
                        'name' => 'url',
                        'type' => 'url',
                        'instructions' => __('Optional link to gateway website', 'travel'),
                    ],
                ],
            ],

            // ========== TAB: CONTACT INFORMATION ==========
            [
                'key' => 'field_tab_contact',
                'label' => __('📞 Contact Information', 'travel'),
                'name' => '',
                'type' => 'tab',
                'placement' => 'left',
            ],
            [
                'key' => 'field_contact_phone',
                'label' => __('Phone Number', 'travel'),
                'name' => 'contact_phone',
                'type' => 'text',
                'wrapper' => ['width' => 50],
            ],
            [
                'key' => 'field_contact_email',
                'label' => __('Email Address', 'travel'),
                'name' => 'contact_email',
                'type' => 'email',
                'wrapper' => ['width' => 50],
            ],
            [
                'key' => 'field_contact_address',
                'label' => __('Physical Address', 'travel'),
                'name' => 'contact_address',
                'type' => 'textarea',
                'rows' => 3,
                'wrapper' => ['width' => 100],
            ],
            [
                'key' => 'field_contact_whatsapp',
                'label' => __('WhatsApp Number', 'travel'),
                'name' => 'contact_whatsapp',
                'type' => 'text',
                'instructions' => __('Include country code (e.g., +51 999 999 999)', 'travel'),
                'wrapper' => ['width' => 50],
            ],

            // Tab: Social Media
            [
                'key' => 'field_tab_social',
                'label' => __('Social Media', 'travel'),
                'name' => '',
                'type' => 'tab',
                'placement' => 'left',
            ],
            [
                'key' => 'field_social_facebook',
                'label' => __('Facebook URL', 'travel'),
                'name' => 'social_facebook',
                'type' => 'url',
                'wrapper' => ['width' => 50],
            ],
            [
                'key' => 'field_social_instagram',
                'label' => __('Instagram URL', 'travel'),
                'name' => 'social_instagram',
                'type' => 'url',
                'wrapper' => ['width' => 50],
            ],
            [
                'key' => 'field_social_twitter',
                'label' => __('Twitter/X URL', 'travel'),
                'name' => 'social_twitter',
                'type' => 'url',
                'wrapper' => ['width' => 50],
            ],
            [
                'key' => 'field_social_youtube',
                'label' => __('YouTube URL', 'travel'),
                'name' => 'social_youtube',
                'type' => 'url',
                'wrapper' => ['width' => 50],
            ],
            [
                'key' => 'field_social_tripadvisor',
                'label' => __('TripAdvisor URL', 'travel'),
                'name' => 'social_tripadvisor',
                'type' => 'url',
                'wrapper' => ['width' => 50],
            ],

            // Tab: Branding
            [
                'key' => 'field_tab_branding',
                'label' => __('Branding', 'travel'),
                'name' => '',
                'type' => 'tab',
                'placement' => 'left',
            ],
            [
                'key' => 'field_logo',
                'label' => __('Site Logo', 'travel'),
                'name' => 'site_logo',
                'type' => 'image',
                'return_format' => 'array',
                'preview_size' => 'medium',
                'library' => 'all',
                'wrapper' => ['width' => 50],
            ],
            [
                'key' => 'field_logo_footer',
                'label' => __('Footer Logo', 'travel'),
                'name' => 'footer_logo',
                'type' => 'image',
                'return_format' => 'array',
                'preview_size' => 'medium',
                'library' => 'all',
                'instructions' => __('Optional alternative logo for footer', 'travel'),
                'wrapper' => ['width' => 50],
            ],
            [
                'key' => 'field_header_logo',
                'label' => __('Header Logo (White/Light)', 'travel'),
                'name' => 'header_logo',
                'type' => 'image',
                'return_format' => 'id',
                'preview_size' => 'medium',
                'library' => 'all',
                'instructions' => __('Logo for header with dark gradient background (most pages). Usually a white or light version.', 'travel'),
                'wrapper' => ['width' => 50],
            ],
            [
                'key' => 'field_header_logo_color',
                'label' => __('Header Logo (Color)', 'travel'),
                'name' => 'header_logo_color',
                'type' => 'image',
                'return_format' => 'id',
                'preview_size' => 'medium',
                'library' => 'all',
                'instructions' => __('Logo in color for package pages with white background. Auto-used on package pages.', 'travel'),
                'wrapper' => ['width' => 50],
            ],

            // Tab: Footer
            [
                'key' => 'field_tab_footer',
                'label' => __('Footer Settings', 'travel'),
                'name' => '',
                'type' => 'tab',
                'placement' => 'left',
            ],
            [
                'key' => 'field_footer_about',
                'label' => __('About Text', 'travel'),
                'name' => 'footer_about',
                'type' => 'textarea',
                'rows' => 4,
                'instructions' => __('Short description for footer about section', 'travel'),
            ],
            [
                'key' => 'field_footer_copyright',
                'label' => __('Copyright Text', 'travel'),
                'name' => 'footer_copyright',
                'type' => 'text',
                'default_value' => '© ' . date('Y') . ' Machu Picchu Peru. All rights reserved.',
            ],
            [
                'key' => 'field_footer_review_logos',
                'label' => __('Review Platform Logos', 'travel'),
                'name' => 'footer_review_logos',
                'type' => 'repeater',
                'instructions' => __('Upload logos of review platforms (TripAdvisor, Google, Facebook)', 'travel'),
                'layout' => 'table',
                'button_label' => __('Add Review Logo', 'travel'),
                'sub_fields' => [
                    [
                        'key' => 'field_review_logo_image',
                        'label' => __('Logo Image', 'travel'),
                        'name' => 'logo_image',
                        'type' => 'image',
                        'return_format' => 'array',
                        'preview_size' => 'thumbnail',
                        'library' => 'all',
                        'wrapper' => ['width' => 50],
                    ],
                    [
                        'key' => 'field_review_logo_url',
                        'label' => __('Review Page URL', 'travel'),
                        'name' => 'logo_url',
                        'type' => 'url',
                        'instructions' => __('Link to your review profile', 'travel'),
                        'wrapper' => ['width' => 50],
                    ],
                ],
            ],
            [
                'key' => 'field_company_name',
                'label' => __('Company Name', 'travel'),
                'name' => 'company_name',
                'type' => 'text',
                'instructions' => __('Full legal company name', 'travel'),
                'default_value' => 'Machu Picchu Peru by Valencia Travel Cusco, Inc.',
            ],
            [
                'key' => 'field_company_ruc',
                'label' => __('RUC Number', 'travel'),
                'name' => 'company_ruc',
                'type' => 'text',
                'instructions' => __('Tax identification number (RUC)', 'travel'),
                'placeholder' => '20490568957',
                'wrapper' => ['width' => 50],
            ],
            [
                'key' => 'field_company_address',
                'label' => __('Company Address', 'travel'),
                'name' => 'company_address',
                'type' => 'textarea',
                'rows' => 2,
                'instructions' => __('Full physical address for footer display', 'travel'),
                'placeholder' => 'Portal Panes #123 / Centro Comercial Ruiseñores Office #306–307 Cusco — Peru',
                'wrapper' => ['width' => 50],
            ],

            // Tab: Booking Settings
            [
                'key' => 'field_tab_booking',
                'label' => __('Booking Settings', 'travel'),
                'name' => '',
                'type' => 'tab',
                'placement' => 'left',
            ],
            [
                'key' => 'field_booking_enabled',
                'label' => __('Enable Online Booking', 'travel'),
                'name' => 'booking_enabled',
                'type' => 'true_false',
                'default_value' => 1,
                'ui' => 1,
            ],
            [
                'key' => 'field_booking_cta_text',
                'label' => __('Default Booking CTA Text', 'travel'),
                'name' => 'booking_cta_text',
                'type' => 'text',
                'default_value' => __('Book Now', 'travel'),
                'conditional_logic' => [
                    [
                        [
                            'field' => 'field_booking_enabled',
                            'operator' => '==',
                            'value' => '1',
                        ],
                    ],
                ],
            ],
        ];

        // Location: Options Page
        $this->location = [
            [
                [
                    'param' => 'options_page',
                    'operator' => '==',
                    'value' => 'global-options',
                ],
            ],
        ];

        $this->settings = [
            'position' => 'normal',
            'style' => 'default',
            'active' => true,
            'show_in_rest' => 1,
        ];
    }

    /**
     * Override register to also create the options page
     */
    public function register(): void
    {
        // Register ACF Options Page using admin_menu hook (runs after acf/init)
        add_action('admin_menu', function () {
            if (function_exists('acf_add_options_page')) {
                // Check if already registered to avoid duplicates
                $existing = acf_get_options_page('global-options');
                if (!$existing) {
                    acf_add_options_page([
                        'page_title' => __('Global Options', 'travel'),
                        'menu_title' => __('Global Options', 'travel'),
                        'menu_slug' => 'global-options',
                        'capability' => 'manage_options',
                        'icon_url' => 'dashicons-admin-site-alt3',
                        'position' => 60,
                        'redirect' => false,
                    ]);
                }
            }
        }, 5);

        // Register the field group directly if ACF already loaded
        // Otherwise use the parent hook method
        if (function_exists('acf_add_local_field_group') && did_action('acf/init')) {
            // ACF already initialized - register directly
            acf_add_local_field_group(array_merge([
                'key'      => $this->key,
                'title'    => $this->title,
                'fields'   => $this->fields,
                'location' => $this->location,
            ], $this->settings));
        } else {
            // ACF not initialized yet - use parent hook method
            parent::register();
        }
    }
}
