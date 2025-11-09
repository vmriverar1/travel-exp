<?php
/**
 * Form Validator
 *
 * Validates form field data against defined rules.
 *
 * @package Travel\Forms\Core
 * @since 1.0.0
 */

namespace Travel\Forms\Core;

class Validator
{
    /**
     * Validation errors.
     *
     * @var array
     */
    private array $errors = [];

    /**
     * Validate data against rules.
     *
     * @param array $data  Form data
     * @param array $rules Validation rules
     *
     * @return bool True if valid, false otherwise
     */
    public function validate(array $data, array $rules): bool
    {
        $this->errors = [];

        foreach ($rules as $field => $rule_set) {
            $value = $data[$field] ?? '';
            $rules_array = explode('|', $rule_set);

            foreach ($rules_array as $rule) {
                $this->apply_rule($field, $value, $rule, $data);
            }
        }

        return empty($this->errors);
    }

    /**
     * Apply a single validation rule.
     *
     * @param string $field Field name
     * @param mixed  $value Field value
     * @param string $rule  Rule to apply
     * @param array  $data  All form data
     *
     * @return void
     */
    private function apply_rule(string $field, $value, string $rule, array $data): void
    {
        // Parse rule parameters (e.g., "min:3" => ["min", "3"])
        $parts = explode(':', $rule);
        $rule_name = $parts[0];
        $param = $parts[1] ?? null;

        switch ($rule_name) {
            case 'required':
                if (empty($value) && $value !== '0') {
                    $this->add_error($field, sprintf(
                        __('The %s field is required', 'travel-forms'),
                        $this->get_field_label($field)
                    ));
                }
                break;

            case 'email':
                if (!empty($value) && !is_email($value)) {
                    $this->add_error($field, sprintf(
                        __('The %s must be a valid email address', 'travel-forms'),
                        $this->get_field_label($field)
                    ));
                }
                break;

            case 'phone':
                if (!empty($value) && !$this->is_valid_phone($value)) {
                    $this->add_error($field, sprintf(
                        __('The %s must be a valid phone number', 'travel-forms'),
                        $this->get_field_label($field)
                    ));
                }
                break;

            case 'min':
                if (!empty($value) && strlen($value) < $param) {
                    $this->add_error($field, sprintf(
                        __('The %s must be at least %d characters', 'travel-forms'),
                        $this->get_field_label($field),
                        $param
                    ));
                }
                break;

            case 'max':
                if (!empty($value) && strlen($value) > $param) {
                    $this->add_error($field, sprintf(
                        __('The %s must not exceed %d characters', 'travel-forms'),
                        $this->get_field_label($field),
                        $param
                    ));
                }
                break;

            case 'numeric':
                if (!empty($value) && !is_numeric($value)) {
                    $this->add_error($field, sprintf(
                        __('The %s must be a number', 'travel-forms'),
                        $this->get_field_label($field)
                    ));
                }
                break;

            case 'date':
                if (!empty($value) && !$this->is_valid_date($value)) {
                    $this->add_error($field, sprintf(
                        __('The %s must be a valid date', 'travel-forms'),
                        $this->get_field_label($field)
                    ));
                }
                break;

            case 'url':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_URL)) {
                    $this->add_error($field, sprintf(
                        __('The %s must be a valid URL', 'travel-forms'),
                        $this->get_field_label($field)
                    ));
                }
                break;

            case 'alpha':
                if (!empty($value) && !ctype_alpha(str_replace(' ', '', $value))) {
                    $this->add_error($field, sprintf(
                        __('The %s may only contain letters', 'travel-forms'),
                        $this->get_field_label($field)
                    ));
                }
                break;
        }
    }

    /**
     * Check if phone number is valid.
     *
     * @param string $phone Phone number
     *
     * @return bool
     */
    private function is_valid_phone(string $phone): bool
    {
        // Remove common phone number characters
        $cleaned = preg_replace('/[^0-9+]/', '', $phone);

        // Must have at least 7 digits
        return strlen($cleaned) >= 7 && strlen($cleaned) <= 20;
    }

    /**
     * Check if date is valid.
     *
     * @param string $date Date string
     *
     * @return bool
     */
    private function is_valid_date(string $date): bool
    {
        $timestamp = strtotime($date);
        return $timestamp !== false;
    }

    /**
     * Add validation error.
     *
     * @param string $field   Field name
     * @param string $message Error message
     *
     * @return void
     */
    private function add_error(string $field, string $message): void
    {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = [];
        }

        $this->errors[$field][] = $message;
    }

    /**
     * Get validation errors.
     *
     * @return array
     */
    public function get_errors(): array
    {
        return $this->errors;
    }

    /**
     * Get first error for a field.
     *
     * @param string $field Field name
     *
     * @return string|null
     */
    public function get_first_error(string $field): ?string
    {
        return $this->errors[$field][0] ?? null;
    }

    /**
     * Get human-readable field label.
     *
     * @param string $field Field name
     *
     * @return string
     */
    private function get_field_label(string $field): string
    {
        return ucwords(str_replace('_', ' ', $field));
    }
}
