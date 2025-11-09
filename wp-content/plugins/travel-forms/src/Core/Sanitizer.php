<?php
/**
 * Form Sanitizer
 *
 * Sanitizes form field data.
 *
 * @package Travel\Forms\Core
 * @since 1.0.0
 */

namespace Travel\Forms\Core;

class Sanitizer
{
    /**
     * Sanitize form data based on field types.
     *
     * @param array $data       Form data
     * @param array $field_types Field type definitions
     *
     * @return array Sanitized data
     */
    public static function sanitize(array $data, array $field_types): array
    {
        $sanitized = [];

        foreach ($data as $field => $value) {
            $type = $field_types[$field] ?? 'text';
            $sanitized[$field] = self::sanitize_field($value, $type);
        }

        return $sanitized;
    }

    /**
     * Sanitize a single field based on its type.
     *
     * @param mixed  $value Field value
     * @param string $type  Field type
     *
     * @return mixed Sanitized value
     */
    private static function sanitize_field($value, string $type)
    {
        switch ($type) {
            case 'email':
                return sanitize_email($value);

            case 'url':
                return esc_url_raw($value);

            case 'textarea':
                return sanitize_textarea_field($value);

            case 'number':
            case 'numeric':
                return is_numeric($value) ? $value : 0;

            case 'int':
            case 'integer':
                return absint($value);

            case 'float':
                return floatval($value);

            case 'phone':
                return self::sanitize_phone($value);

            case 'date':
                return sanitize_text_field($value);

            case 'checkbox':
            case 'bool':
            case 'boolean':
                return (bool) $value;

            case 'array':
                return is_array($value) ? array_map('sanitize_text_field', $value) : [];

            case 'text':
            default:
                return sanitize_text_field($value);
        }
    }

    /**
     * Sanitize phone number.
     *
     * @param string $phone Phone number
     *
     * @return string
     */
    private static function sanitize_phone(string $phone): string
    {
        // Keep only numbers, +, -, (, ), and spaces
        return preg_replace('/[^0-9+\-() ]/', '', $phone);
    }

    /**
     * Remove all HTML tags from value.
     *
     * @param string $value Value to strip
     *
     * @return string
     */
    public static function strip_all_tags(string $value): string
    {
        return wp_strip_all_tags($value);
    }

    /**
     * Escape output for safe HTML display.
     *
     * @param string $value Value to escape
     *
     * @return string
     */
    public static function escape_output(string $value): string
    {
        return esc_html($value);
    }
}
