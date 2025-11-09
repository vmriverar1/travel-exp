/**
 * Hero Contact Form JavaScript
 *
 * Handles validation, AJAX submission, and state management
 *
 * @package TravelBlocks
 * @since 2.0.0
 */

(function() {
    'use strict';

    /**
     * Initialize all hero forms on the page
     */
    function initHeroForms() {
        const forms = document.querySelectorAll('.hero-form__form');

        forms.forEach(form => {
            if (form.dataset.initialized === 'true') return;
            form.dataset.initialized = 'true';

            setupFormValidation(form);
            setupFormSubmit(form);
        });
    }

    /**
     * Setup real-time validation on form fields
     */
    function setupFormValidation(form) {
        const fields = form.querySelectorAll('input, textarea, select');

        fields.forEach(field => {
            // Validate on blur
            field.addEventListener('blur', function() {
                validateField(this);
            });

            // Remove error on input
            field.addEventListener('input', function() {
                if (this.classList.contains('error')) {
                    this.classList.remove('error');
                }
            });
        });
    }

    /**
     * Validate a single field
     */
    function validateField(field) {
        const value = field.value.trim();
        let isValid = true;

        // Required fields
        if (field.hasAttribute('required') && !value) {
            isValid = false;
        }

        // Email validation
        if (field.type === 'email' && value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            isValid = emailRegex.test(value);
        }

        // Phone validation (basic)
        if (field.type === 'tel' && value) {
            const phoneRegex = /^[\d\s\-\+\(\)]+$/;
            isValid = phoneRegex.test(value);
        }

        // Add/remove error class
        if (!isValid) {
            field.classList.add('error');
        } else {
            field.classList.remove('error');
        }

        return isValid;
    }

    /**
     * Validate entire form
     */
    function validateForm(form) {
        const fields = form.querySelectorAll('input[required], textarea[required], select[required]');
        let isValid = true;

        fields.forEach(field => {
            if (!validateField(field)) {
                isValid = false;
            }
        });

        return isValid;
    }

    /**
     * Setup form submission handler
     */
    function setupFormSubmit(form) {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            // Validate form
            if (!validateForm(form)) {
                // Focus first error field
                const firstError = form.querySelector('.error');
                if (firstError) {
                    firstError.focus();
                }
                return;
            }

            // Check if AJAX is enabled
            const heroForm = form.closest('.hero-form');
            const enableAjax = heroForm && heroForm.dataset.enableAjax === '1';

            if (enableAjax) {
                await handleAjaxSubmit(form);
            } else {
                // Standard form submission
                form.submit();
            }
        });
    }

    /**
     * Handle AJAX form submission
     */
    async function handleAjaxSubmit(form) {
        const formData = new FormData(form);
        const submitButton = form.querySelector('.btn-cta');
        const btnText = submitButton.querySelector('.btn-cta__text');
        const btnLoading = submitButton.querySelector('.btn-cta__loading');
        const successMsg = form.querySelector('.hero-form__success');
        const errorMsg = form.querySelector('.hero-form__error');
        const errorSpan = errorMsg.querySelector('span');

        // Set loading state
        setLoadingState(submitButton, btnText, btnLoading, true);
        hideMessages(successMsg, errorMsg);

        // Prepare data
        const data = {
            action: 'travel_hero_form_submit',
            nonce: formData.get('nonce'),
            first_name: formData.get('first_name'),
            last_name: formData.get('last_name'),
            email: formData.get('email'),
            phone: formData.get('phone'),
            country: formData.get('country'),
            package_interest: formData.get('package_interest'),
            package_id: formData.get('package_id'),
            message: formData.get('message'),
        };

        try {
            const response = await fetch(travelContactForm.ajaxUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams(data),
            });

            const result = await response.json();

            if (result.success) {
                // Show success message
                showSuccess(successMsg);

                // Reset form
                form.reset();

                // Remove error classes
                form.querySelectorAll('.error').forEach(field => {
                    field.classList.remove('error');
                });

                // Hide success after 5 seconds
                setTimeout(() => {
                    hideMessages(successMsg, errorMsg);
                }, 5000);

            } else {
                // Show error message
                const errorMessage = result.data?.message || 'Something went wrong. Please try again.';
                showError(errorMsg, errorSpan, errorMessage);
            }

        } catch (error) {
            console.error('Form submission error:', error);
            showError(errorMsg, errorSpan, 'Network error. Please check your connection and try again.');
        } finally {
            setLoadingState(submitButton, btnText, btnLoading, false);
        }
    }

    /**
     * Set loading state on submit button
     */
    function setLoadingState(button, textEl, loadingEl, isLoading) {
        if (isLoading) {
            button.disabled = true;
            textEl.setAttribute('hidden', '');
            loadingEl.removeAttribute('hidden');
        } else {
            button.disabled = false;
            textEl.removeAttribute('hidden');
            loadingEl.setAttribute('hidden', '');
        }
    }

    /**
     * Hide all messages
     */
    function hideMessages(successMsg, errorMsg) {
        if (successMsg) successMsg.setAttribute('hidden', '');
        if (errorMsg) errorMsg.setAttribute('hidden', '');
    }

    /**
     * Show success message
     */
    function showSuccess(successMsg) {
        if (successMsg) {
            successMsg.removeAttribute('hidden');

            // Scroll to message
            successMsg.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    }

    /**
     * Show error message
     */
    function showError(errorMsg, errorSpan, message) {
        if (errorMsg && errorSpan) {
            errorSpan.textContent = message;
            errorMsg.removeAttribute('hidden');

            // Scroll to message
            errorMsg.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    }

    /**
     * Initialize on DOM ready
     */
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initHeroForms);
    } else {
        initHeroForms();
    }

    /**
     * Re-initialize on Gutenberg editor changes
     */
    if (typeof wp !== 'undefined' && wp.data) {
        wp.data.subscribe(() => {
            // Debounce to avoid multiple calls
            clearTimeout(window.heroFormInitTimeout);
            window.heroFormInitTimeout = setTimeout(initHeroForms, 300);
        });
    }

    /**
     * Expose to global scope for manual initialization
     */
    window.TravelBlocks = window.TravelBlocks || {};
    window.TravelBlocks.HeroForm = {
        init: initHeroForms,
        validate: validateForm
    };

})();
