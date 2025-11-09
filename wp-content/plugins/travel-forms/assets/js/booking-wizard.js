/**
 * Booking Wizard JavaScript
 * Handles wizard navigation, validation, and submission
 */

(function ($) {
    'use strict';

    /**
     * Booking Wizard Class
     */
    class BookingWizard {
        constructor() {
            this.overlay = $('#booking-wizard-overlay');
            this.aside = $('#booking-wizard-aside');
            this.currentStep = 1;
            this.totalSteps = 4;
            this.wizardData = {};
            this.countdownInterval = null;

            this.init();
        }

        /**
         * Initialize wizard
         */
        init() {
            this.bindEvents();
            this.initCountdown();
        }

        /**
         * Bind all events
         */
        bindEvents() {
            // Listen for custom event from dates-and-prices block
            $(document).on('travelBlocksPurchaseRequested', (e) => {
                this.open(e.detail || {});
            });

            // Close button
            $('.booking-wizard-close').on('click', () => this.close());

            // Overlay click
            this.overlay.on('click', (e) => {
                if ($(e.target).is(this.overlay)) {
                    this.close();
                }
            });

            // Escape key
            $(document).on('keydown', (e) => {
                if (e.key === 'Escape' && this.overlay.hasClass('is-visible')) {
                    this.close();
                }
            });

            // Step navigation buttons
            $('[data-action="continue"]').on('click', () => this.nextStep());
            $('[data-action="previous"]').on('click', () => this.previousStep());

            // Stepper navigation (click on step circles)
            $('.stepper-step').on('click', (e) => {
                const step = $(e.currentTarget).data('step');
                if (step < this.currentStep) {
                    this.goToStep(step);
                }
            });

            // Step 1: Travellers stepper
            $('.wizard-stepper__btn').on('click', this.handleStepperClick.bind(this));

            // Step 1: Room type selection
            $('input[name="room_type"]').on('change', this.handleRoomTypeChange.bind(this));

            // Step 2: Add-ons stepper
            $('.wizard-addon-item__controls .wizard-stepper__btn').on('click', this.handleAddonStepper.bind(this));

            // Step 2: Toggle add-on details
            $('.wizard-addon-item__toggle').on('click', this.handleAddonToggle.bind(this));

            // Step 4: Payment option selection
            $('input[name="payment_option"]').on('change', this.handlePaymentOptionChange.bind(this));

            // Step 4: Payment method buttons
            $('.wizard-payment-btn').on('click', this.handlePaymentMethod.bind(this));
        }

        /**
         * Open wizard
         */
        open(data = {}) {
            this.wizardData = {
                packageId: data.packageId || null,
                departureDate: data.departureDate || null,
                returnDate: data.returnDate || null,
                ...data
            };

            // Populate package data
            this.populatePackageData();

            // Show wizard
            this.overlay.addClass('is-visible');
            $('body').css('overflow', 'hidden');

            // Start countdown
            this.startCountdown();
        }

        /**
         * Close wizard
         */
        close() {
            this.overlay.removeClass('is-visible');
            $('body').css('overflow', '');

            // Stop countdown
            this.stopCountdown();

            // Reset to step 1 after animation
            setTimeout(() => {
                this.goToStep(1);
            }, 300);
        }

        /**
         * Go to specific step
         */
        goToStep(step) {
            if (step < 1 || step > this.totalSteps) return;

            // Hide current step
            $(`.wizard-step[data-step="${this.currentStep}"]`).removeClass('wizard-step--active').hide();

            // Show new step
            $(`.wizard-step[data-step="${step}"]`).addClass('wizard-step--active').show();

            // Update stepper
            this.updateStepper(step);

            // Update current step
            this.currentStep = step;

            // Scroll to top
            this.aside.scrollTop(0);
        }

        /**
         * Next step
         */
        nextStep() {
            if (!this.validateCurrentStep()) {
                return;
            }

            // Save current step data
            this.saveCurrentStepData();

            // Go to next step
            if (this.currentStep < this.totalSteps) {
                this.goToStep(this.currentStep + 1);
            }
        }

        /**
         * Previous step
         */
        previousStep() {
            if (this.currentStep > 1) {
                this.goToStep(this.currentStep - 1);
            }
        }

        /**
         * Update stepper UI
         */
        updateStepper(activeStep) {
            $('.stepper-step').each((index, el) => {
                const $step = $(el);
                const stepNum = $step.data('step');

                $step.removeClass('stepper-step--active stepper-step--completed');

                if (stepNum < activeStep) {
                    $step.addClass('stepper-step--completed');
                    $step.find('.stepper-step__circle').html('');
                } else if (stepNum === activeStep) {
                    $step.addClass('stepper-step--active');
                    $step.find('.stepper-step__circle').html(stepNum);
                } else {
                    $step.find('.stepper-step__circle').html(stepNum);
                }
            });
        }

        /**
         * Validate current step
         */
        validateCurrentStep() {
            switch (this.currentStep) {
                case 1:
                    return this.validateStep1();
                case 2:
                    return true; // Add-ons are optional
                case 3:
                    return this.validateStep3();
                case 4:
                    return true; // Validation happens on payment button click
                default:
                    return true;
            }
        }

        /**
         * Validate Step 1
         */
        validateStep1() {
            const travellers = parseInt($('#wizard-travellers').val());
            const roomType = $('input[name="room_type"]:checked').val();
            const travelDates = $('#wizard-travel-dates').val();

            if (!travellers || travellers < 1) {
                alert('Please select number of travellers');
                return false;
            }

            if (!roomType) {
                alert('Please select a room type');
                return false;
            }

            if (!travelDates) {
                alert('Please select travel dates');
                return false;
            }

            return true;
        }

        /**
         * Validate Step 3
         */
        validateStep3() {
            const requiredFields = [
                '#billing-title',
                '#billing-first-name',
                '#billing-last-name',
                '#billing-email',
                '#billing-phone',
                '#billing-dob',
                '#billing-country',
                '#billing-address',
                '#billing-state',
                '#billing-city',
                '#billing-zip'
            ];

            let isValid = true;

            requiredFields.forEach(field => {
                const $field = $(field);
                if (!$field.val()) {
                    $field.css('border-color', 'var(--wiz-error)');
                    isValid = false;
                } else {
                    $field.css('border-color', '');
                }
            });

            // Check terms
            if (!$('#billing-terms').is(':checked')) {
                alert('Please accept the terms and conditions');
                isValid = false;
            }

            if (!isValid) {
                alert('Please fill in all required fields');
            }

            return isValid;
        }

        /**
         * Save current step data
         */
        saveCurrentStepData() {
            switch (this.currentStep) {
                case 1:
                    this.wizardData.step1 = {
                        travellers: parseInt($('#wizard-travellers').val()),
                        roomType: $('input[name="room_type"]:checked').val(),
                        travelDates: $('#wizard-travel-dates').val()
                    };
                    break;

                case 2:
                    this.wizardData.step2 = {
                        addons: this.getSelectedAddons()
                    };
                    break;

                case 3:
                    this.wizardData.step3 = {
                        title: $('#billing-title').val(),
                        firstName: $('#billing-first-name').val(),
                        lastName: $('#billing-last-name').val(),
                        email: $('#billing-email').val(),
                        phone: $('#billing-phone').val(),
                        document: $('#billing-document').val(),
                        documentNumber: $('#billing-document-number').val(),
                        dob: $('#billing-dob').val(),
                        country: $('#billing-country').val(),
                        address: $('#billing-address').val(),
                        state: $('#billing-state').val(),
                        city: $('#billing-city').val(),
                        zip: $('#billing-zip').val()
                    };
                    break;
            }
        }

        /**
         * Handle stepper button click (Step 1)
         */
        handleStepperClick(e) {
            const $btn = $(e.currentTarget);
            const $stepper = $btn.closest('.wizard-stepper');
            const $input = $stepper.find('.wizard-stepper__value');
            const action = $btn.data('action');
            const currentValue = parseInt($input.val()) || 0;
            const min = parseInt($input.attr('min')) || 0;
            const max = parseInt($input.attr('max')) || 99;

            let newValue = currentValue;

            if (action === 'increment' && currentValue < max) {
                newValue = currentValue + 1;
            } else if (action === 'decrement' && currentValue > min) {
                newValue = currentValue - 1;
            }

            $input.val(newValue);

            // Update button states
            $stepper.find('[data-action="decrement"]').prop('disabled', newValue <= min);
            $stepper.find('[data-action="increment"]').prop('disabled', newValue >= max);
        }

        /**
         * Handle room type change
         */
        handleRoomTypeChange(e) {
            const $radio = $(e.currentTarget);
            $('.wizard-room-card').removeClass('wizard-room-card--selected');
            $radio.closest('.wizard-room-card').addClass('wizard-room-card--selected');

            // Update price
            // TODO: Calculate and update total price
        }

        /**
         * Handle add-on stepper (Step 2)
         */
        handleAddonStepper(e) {
            const $btn = $(e.currentTarget);
            const addon = $btn.data('addon');
            const $stepper = $btn.closest('.wizard-stepper');
            const $value = $stepper.find(`.wizard-stepper__value[data-addon="${addon}"]`);
            const action = $btn.hasClass('wizard-stepper__btn--minus') ? 'decrement' : 'increment';
            const currentValue = parseInt($value.text()) || 0;

            let newValue = currentValue;

            if (action === 'increment') {
                newValue = currentValue + 1;
            } else if (action === 'decrement' && currentValue > 0) {
                newValue = currentValue - 1;
            }

            $value.text(newValue);

            // Update button states
            $stepper.find('.wizard-stepper__btn--minus').prop('disabled', newValue === 0);
        }

        /**
         * Handle add-on details toggle
         */
        handleAddonToggle(e) {
            const $toggle = $(e.currentTarget);
            const $item = $toggle.closest('.wizard-addon-item');
            const $details = $item.find('.wizard-addon-item__details');

            $toggle.toggleClass('wizard-addon-item__toggle--expanded');
            $details.slideToggle(200);
        }

        /**
         * Get selected add-ons
         */
        getSelectedAddons() {
            const addons = [];

            $('.wizard-stepper__value[data-addon]').each((index, el) => {
                const $el = $(el);
                const qty = parseInt($el.text()) || 0;

                if (qty > 0) {
                    addons.push({
                        id: $el.data('addon'),
                        quantity: qty
                    });
                }
            });

            return addons;
        }

        /**
         * Handle payment option change
         */
        handlePaymentOptionChange(e) {
            const $radio = $(e.currentTarget);
            $('.wizard-payment-card').removeClass('wizard-payment-card--selected');
            $radio.closest('.wizard-payment-card').addClass('wizard-payment-card--selected');
        }

        /**
         * Handle payment method selection
         */
        handlePaymentMethod(e) {
            const $btn = $(e.currentTarget);
            const method = $btn.data('method');

            // Save all data
            this.saveCurrentStepData();

            // Get final data
            this.wizardData.step4 = {
                paymentOption: $('input[name="payment_option"]:checked').val(),
                paymentMethod: method
            };

            console.log('Submitting wizard data:', this.wizardData);

            // Submit to backend
            this.submitBooking();
        }

        /**
         * Submit booking to backend
         */
        submitBooking() {
            $.ajax({
                url: bookingWizardConfig.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'submit_booking_wizard',
                    nonce: bookingWizardConfig.nonce,
                    wizardData: this.wizardData
                },
                beforeSend: () => {
                    // Show loading state
                    $('.wizard-payment-btn').prop('disabled', true).text('Processing...');
                },
                success: (response) => {
                    if (response.success) {
                        alert(response.data.message);
                        this.close();
                        // Redirect or show confirmation
                    } else {
                        alert('Error: ' + response.data.message);
                    }
                },
                error: () => {
                    alert('An error occurred. Please try again.');
                },
                complete: () => {
                    $('.wizard-payment-btn').prop('disabled', false).text('Pay Now');
                }
            });
        }

        /**
         * Populate package data
         */
        populatePackageData() {
            // Populate dates
            if (this.wizardData.departureDate) {
                $('#wizard-travel-dates').val(
                    `From: ${this.wizardData.departureDate}  >  To: ${this.wizardData.returnDate || ''}`
                );
            }

            // If no packageId, can't fetch package data
            if (!this.wizardData.packageId) {
                console.warn('No packageId provided, using placeholder data');
                return;
            }

            // Fetch package data from server
            $.ajax({
                url: bookingWizardConfig.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'get_package_data',
                    packageId: this.wizardData.packageId,
                    nonce: bookingWizardConfig.nonce,
                },
                success: (response) => {
                    if (response.success && response.data) {
                        this.updatePackageDisplay(response.data);
                    } else {
                        console.error('Failed to fetch package data:', response);
                    }
                },
                error: (xhr, status, error) => {
                    console.error('AJAX error fetching package data:', error);
                }
            });
        }

        /**
         * Update package display with real data
         */
        updatePackageDisplay(packageData) {
            // Update package title
            if (packageData.title) {
                $('#wizard-package-name').text(packageData.title);
            }

            // Update package image
            if (packageData.thumbnail) {
                $('#wizard-package-image').attr('src', packageData.thumbnail);
            }

            // Update price
            if (packageData.price_from) {
                $('#wizard-package-price').text(`USD $${packageData.price_from.toFixed(2)}`);
            }

            // Update duration
            if (packageData.duration) {
                $('#wizard-package-duration').text(`${packageData.duration} days`);
            }

            // Store package data for later use
            this.wizardData.packageData = packageData;
        }

        /**
         * Initialize countdown
         */
        initCountdown() {
            this.countdownTime = 20 * 60; // 20 minutes in seconds
        }

        /**
         * Start countdown
         */
        startCountdown() {
            this.updateCountdownDisplay();

            this.countdownInterval = setInterval(() => {
                this.countdownTime--;

                if (this.countdownTime <= 0) {
                    this.stopCountdown();
                    alert('Your booking session has expired. Please start again.');
                    this.close();
                } else {
                    this.updateCountdownDisplay();
                }
            }, 1000);
        }

        /**
         * Stop countdown
         */
        stopCountdown() {
            if (this.countdownInterval) {
                clearInterval(this.countdownInterval);
                this.countdownInterval = null;
            }
        }

        /**
         * Update countdown display
         */
        updateCountdownDisplay() {
            const minutes = Math.floor(this.countdownTime / 60);
            const seconds = this.countdownTime % 60;
            const display = `${minutes}:${seconds.toString().padStart(2, '0')}`;

            $('[id^="wizard-countdown"]').text(display);
        }
    }

    /**
     * Initialize on document ready
     */
    $(document).ready(function () {
        const wizardInstance = new BookingWizard();

        // Make it globally accessible for debugging
        window.bookingWizard = wizardInstance;
    });

})(jQuery);
