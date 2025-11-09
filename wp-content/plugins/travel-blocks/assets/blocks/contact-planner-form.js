/**
 * Contact Planner Form - JavaScript
 *
 * Handles form submission with AJAX
 *
 * @package Travel\Blocks
 * @since 1.0.0
 */

(function() {
    'use strict';

    // Initialize all contact planner forms
    function initContactPlannerForms() {
        const forms = document.querySelectorAll('.contact-planner-form__form');

        forms.forEach(form => {
            form.addEventListener('submit', handleFormSubmit);
        });
    }

    /**
     * Handle form submission
     */
    function handleFormSubmit(event) {
        event.preventDefault();

        const form = event.target;
        const container = form.closest('.contact-planner-form');
        const submitButton = form.querySelector('.contact-planner-form__button');
        const successMessage = form.querySelector('.contact-planner-form__success');
        const errorMessage = form.querySelector('.contact-planner-form__error');

        // Hide previous messages
        successMessage.style.display = 'none';
        errorMessage.style.display = 'none';

        // Validate form
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        // Show loading state
        container.classList.add('is-loading');
        submitButton.disabled = true;

        // Collect form data
        const formData = new FormData(form);
        const data = {
            action: 'travel_planner_form_submit',
            nonce: travelPlannerForm.nonce,
            first_name: formData.get('first_name'),
            email: formData.get('email'),
            country: formData.get('country'),
            travel_dates: formData.get('travel_dates'),
            group_size: formData.get('group_size'),
            call_preference: formData.get('call_preference') === 'yes',
            package_id: form.dataset.packageId,
            package_title: form.dataset.packageTitle,
        };

        // Send AJAX request
        fetch(travelPlannerForm.ajaxUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams(data)
        })
        .then(response => response.json())
        .then(response => {
            // Remove loading state
            container.classList.remove('is-loading');
            submitButton.disabled = false;

            if (response.success) {
                // Show success message
                successMessage.style.display = 'flex';

                // Reset form
                form.reset();

                // Hide success message after 5 seconds
                setTimeout(() => {
                    successMessage.style.display = 'none';
                }, 5000);

                // Trigger custom event
                const event = new CustomEvent('travelPlannerFormSubmitted', {
                    detail: { data, response }
                });
                document.dispatchEvent(event);

            } else {
                // Show error message
                errorMessage.style.display = 'flex';
                if (response.data && response.data.message) {
                    errorMessage.querySelector('span').textContent = response.data.message;
                }

                // Hide error message after 5 seconds
                setTimeout(() => {
                    errorMessage.style.display = 'none';
                }, 5000);
            }
        })
        .catch(error => {
            console.error('Contact Planner Form Error:', error);

            // Remove loading state
            container.classList.remove('is-loading');
            submitButton.disabled = false;

            // Show error message
            errorMessage.style.display = 'flex';

            // Hide error message after 5 seconds
            setTimeout(() => {
                errorMessage.style.display = 'none';
            }, 5000);
        });
    }

    // Initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initContactPlannerForms);
    } else {
        initContactPlannerForms();
    }

})();
