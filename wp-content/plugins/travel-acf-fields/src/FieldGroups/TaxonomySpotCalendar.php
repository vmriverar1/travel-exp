<?php
namespace Aurora\ACFKit\FieldGroups;

use Aurora\ACFKit\Core\FieldGroup;

class TaxonomySpotCalendar extends FieldGroup
{
    public function register(): void
    {
        if (!function_exists('acf_add_local_field_group')) {
            return;
        }

        acf_add_local_field_group([
            'key' => 'group_taxonomy_spot_calendar',
            'title' => 'Spot Calendar Details',
            'fields' => [
                [
                    'key' => 'field_spot_calendar_featured_image',
                    'label' => '🖼️ Imagen Destacada',
                    'name' => 'featured_image',
                    'type' => 'image',
                    'instructions' => 'Imagen destacada para este spot calendar (opcional).',
                    'required' => 0,
                    'return_format' => 'array',
                    'preview_size' => 'medium',
                    'library' => 'all',
                ],
                [
                    'key' => 'field_spot_calendar_month',
                    'label' => 'Month',
                    'name' => 'month',
                    'type' => 'select',
                    'required' => 1,
                    'choices' => [
                        'january' => 'January',
                        'february' => 'February',
                        'march' => 'March',
                        'april' => 'April',
                        'may' => 'May',
                        'june' => 'June',
                        'july' => 'July',
                        'august' => 'August',
                        'september' => 'September',
                        'october' => 'October',
                        'november' => 'November',
                        'december' => 'December',
                    ],
                    'default_value' => '',
                    'allow_null' => 0,
                    'ui' => 1,
                    'ajax' => 0,
                    'return_format' => 'value',
                ],
                [
                    'key' => 'field_spot_calendar_spot_start_day',
                    'label' => 'Spot Start Day',
                    'name' => 'spot_start_day',
                    'type' => 'number',
                    'required' => 1,
                    'instructions' => 'Enter the starting day number for spots calculation',
                    'default_value' => 1,
                    'min' => 1,
                    'max' => 31,
                    'step' => 1,
                ],
                [
                    'key' => 'field_spot_calendar_calendar_dates',
                    'label' => 'Calendar Dates',
                    'name' => 'calendar_dates',
                    'type' => 'repeater',
                    'required' => 0,
                    'instructions' => 'Add specific departure dates with available spots',
                    'layout' => 'table',
                    'button_label' => 'Add Date',
                    'sub_fields' => [
                        [
                            'key' => 'field_spot_calendar_date',
                            'label' => 'Date',
                            'name' => 'date',
                            'type' => 'date_picker',
                            'required' => 1,
                            'display_format' => 'F j, Y',
                            'return_format' => 'Y-m-d',
                            'first_day' => 1,
                        ],
                        [
                            'key' => 'field_spot_calendar_spots',
                            'label' => 'Spots',
                            'name' => 'spots',
                            'type' => 'number',
                            'required' => 1,
                            'instructions' => 'Number of available spots for this date',
                            'default_value' => 0,
                            'min' => 0,
                            'step' => 1,
                        ],
                    ],
                    'min' => 0,
                    'max' => 0,
                ],
            ],
            'location' => [
                [
                    [
                        'param' => 'taxonomy',
                        'operator' => '==',
                        'value' => 'spot_calendar',
                    ],
                ],
            ],
            'menu_order' => 0,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'active' => true,
        ]);
    }
}
