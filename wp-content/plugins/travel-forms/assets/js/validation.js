/**
 * Travel Forms - Frontend Validation and AJAX Submission
 *
 * @package Travel\Forms
 */

(function() {
    'use strict';

    // Wait for DOM to be ready
    document.addEventListener('DOMContentLoaded', function() {
        initializeForms();
    });

    /**
     * Initialize all travel forms
     */
    function initializeForms() {
        const forms = document.querySelectorAll('.travel-form-inner');

        forms.forEach(function(form) {
            // Add real-time validation
            addRealtimeValidation(form);

            // Handle form submission
            form.addEventListener('submit', handleFormSubmit);

            // Handle conditional fields (e.g., brochure form address)
            handleConditionalFields(form);
        });
    }

    /**
     * Add real-time validation to form fields
     */
    function addRealtimeValidation(form) {
        const fields = form.querySelectorAll('input, textarea, select');

        fields.forEach(function(field) {
            field.addEventListener('blur', function() {
                validateField(field);
            });

            field.addEventListener('input', function() {
                // Clear error on input
                clearFieldError(field);
            });
        });
    }

    /**
     * Validate a single field
     */
    function validateField(field) {
        const fieldWrapper = field.closest('.form-field');
        const errorElement = fieldWrapper ? fieldWrapper.querySelector('.error-message') : null;

        if (!errorElement) return true;

        // Clear previous error
        clearFieldError(field);

        // Check if required
        if (field.hasAttribute('required') && !field.value.trim()) {
            showFieldError(field, travelFormsConfig.messages.required);
            return false;
        }

        // Email validation
        if (field.type === 'email' && field.value.trim()) {
            if (!isValidEmail(field.value)) {
                showFieldError(field, travelFormsConfig.messages.email);
                return false;
            }
        }

        // Phone validation
        if (field.type === 'tel' && field.value.trim()) {
            if (!isValidPhone(field.value)) {
                showFieldError(field, travelFormsConfig.messages.phone);
                return false;
            }
        }

        // Number validation
        if (field.type === 'number' && field.value.trim()) {
            if (isNaN(field.value) || field.value < 0) {
                showFieldError(field, 'Please enter a valid number');
                return false;
            }
        }

        return true;
    }

    /**
     * Handle form submission
     */
    function handleFormSubmit(e) {
        e.preventDefault();

        const form = e.target;
        const formId = form.dataset.formId;
        const submitButton = form.querySelector('button[type="submit"]');
        const messagesContainer = form.querySelector('.form-messages');

        // Validate all fields
        let isValid = true;
        const fields = form.querySelectorAll('input[required], textarea[required], select[required]');

        fields.forEach(function(field) {
            if (!validateField(field)) {
                isValid = false;
            }
        });

        if (!isValid) {
            showFormMessage(messagesContainer, 'Please fix the errors above', 'error');
            return;
        }

        // Disable submit button
        submitButton.disabled = true;
        submitButton.textContent = 'Submitting...';

        // Collect form data
        const formData = new FormData(form);
        const data = {};

        formData.forEach(function(value, key) {
            data[key] = value;
        });

        // Prepare AJAX request
        const requestData = new FormData();
        requestData.append('action', 'submit_' + formId);
        requestData.append('nonce', travelFormsConfig.nonce);
        requestData.append('form_data', JSON.stringify(data));

        // Send via Fetch API
        fetch(travelFormsConfig.ajaxUrl, {
            method: 'POST',
            body: requestData,
            credentials: 'same-origin'
        })
        .then(function(response) {
            return response.json();
        })
        .then(function(result) {
            if (result.success) {
                // Success
                showFormMessage(messagesContainer, result.data.message, 'success');
                form.reset();

                // Scroll to message
                messagesContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
            } else {
                // Error
                const message = result.data.message || travelFormsConfig.messages.error;
                showFormMessage(messagesContainer, message, 'error');

                // Show field-specific errors if available
                if (result.data.errors) {
                    Object.keys(result.data.errors).forEach(function(fieldName) {
                        const field = form.querySelector('[name="' + fieldName + '"]');
                        if (field) {
                            showFieldError(field, result.data.errors[fieldName][0]);
                        }
                    });
                }
            }
        })
        .catch(function(error) {
            console.error('Form submission error:', error);
            showFormMessage(messagesContainer, travelFormsConfig.messages.error, 'error');
        })
        .finally(function() {
            // Re-enable submit button
            submitButton.disabled = false;
            submitButton.textContent = submitButton.dataset.originalText || 'Submit';
        });
    }

    /**
     * Show field error message
     */
    function showFieldError(field, message) {
        const fieldWrapper = field.closest('.form-field');
        if (!fieldWrapper) return;

        const errorElement = fieldWrapper.querySelector('.error-message');
        if (errorElement) {
            errorElement.textContent = message;
            errorElement.style.display = 'block';
        }

        field.classList.add('error');
        fieldWrapper.classList.add('has-error');
    }

    /**
     * Clear field error message
     */
    function clearFieldError(field) {
        const fieldWrapper = field.closest('.form-field');
        if (!fieldWrapper) return;

        const errorElement = fieldWrapper.querySelector('.error-message');
        if (errorElement) {
            errorElement.textContent = '';
            errorElement.style.display = 'none';
        }

        field.classList.remove('error');
        fieldWrapper.classList.remove('has-error');
    }

    /**
     * Show form-level message
     */
    function showFormMessage(container, message, type) {
        if (!container) return;

        container.innerHTML = '<div class="form-message form-message-' + type + '">' + message + '</div>';
        container.style.display = 'block';

        // Auto-hide success messages after 5 seconds
        if (type === 'success') {
            setTimeout(function() {
                container.style.display = 'none';
            }, 5000);
        }
    }

    /**
     * Validate email format
     */
    function isValidEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    /**
     * Validate phone format
     */
    function isValidPhone(phone) {
        const cleaned = phone.replace(/[^0-9+]/g, '');
        return cleaned.length >= 7 && cleaned.length <= 20;
    }

    /**
     * Handle conditional fields (e.g., show address field only for physical brochure)
     */
    function handleConditionalFields(form) {
        const formatField = form.querySelector('[name="format"]');
        const addressField = form.querySelector('[name="address"]');

        if (formatField && addressField) {
            const addressWrapper = addressField.closest('.conditional-field');

            formatField.addEventListener('change', function() {
                if (this.value === 'physical') {
                    addressWrapper.classList.remove('hidden');
                    addressField.setAttribute('required', 'required');
                } else {
                    addressWrapper.classList.add('hidden');
                    addressField.removeAttribute('required');
                    addressField.value = '';
                }
            });
        }
    }

})();
