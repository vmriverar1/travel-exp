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
            console.log('📦 BookingWizard: Constructor called');

            this.overlay = $('#booking-wizard-overlay');
            this.aside = $('#booking-wizard-aside');

            console.log('📦 BookingWizard: Overlay found?', this.overlay.length > 0);
            console.log('📦 BookingWizard: Aside found?', this.aside.length > 0);

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
            console.log('🎬 BookingWizard: Initializing...');
            this.bindEvents();
            this.initCountdown();
            console.log('✅ BookingWizard: Initialized successfully');
        }

        /**
         * Bind all events
         */
        bindEvents() {
            console.log('🔗 BookingWizard: Binding events...');

            // Listen for custom event from dates-and-prices block
            $(document).on('travelBlocksPurchaseRequested', (e) => {
                console.log('🎯 BookingWizard: travelBlocksPurchaseRequested event received!', e.detail);
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
            console.log('🚀 BookingWizard: open() called with data:', data);

            this.wizardData = {
                packageId: data.packageId || null,
                departureDate: data.departureDate || null,
                returnDate: data.returnDate || null,
                selectedPrice: parseFloat(data.price) || 0,
                singleSupp: parseFloat(data.singleSupp) || 0,
                ...data
            };

            console.log('📝 BookingWizard: wizardData set:', this.wizardData);

            // Populate package data
            this.populatePackageData();

            // Initialize travellers to 1
            $('#wizard-travellers').val(1);
            $('.wizard-stepper__btn[data-action="decrement"]').prop('disabled', true);

            // Initialize room type logic
            this.updateRoomTypeAvailability();

            // Show wizard
            this.overlay.addClass('is-visible');

            console.log('   - Overlay hasClass("is-visible") AFTER:', this.overlay.hasClass('is-visible'));
            console.log('   - Overlay classes after:', this.overlay.attr('class'));
            console.log('   - Overlay style after:', this.overlay.attr('style'));
            console.log('   - Body overflow set to hidden');

            // EXPERIMENTO: Forzar display y opacity directamente
            this.overlay.css({
                'display': 'block',
                'opacity': '1',
                'visibility': 'visible'
            });
            console.log('🧪 TEST: Forced inline styles (display:block, opacity:1, visibility:visible)');

            $('body').css('overflow', 'hidden');

            // Start countdown
            this.startCountdown();

            console.log('✅ BookingWizard: Wizard should now be visible!');
            console.log('🔍 FINAL STATE:');
            console.log('   - overlay[0]:', this.overlay[0]);
            console.log('   - overlay.length:', this.overlay.length);
            console.log('   - All classes:', this.overlay.attr('class'));
            console.log('   - All inline styles:', this.overlay.attr('style'));
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

            // Update room type availability and prices
            this.updateRoomTypeAvailability();
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
         * Submit booking to backend and process payment
         */
        submitBooking() {
            const paymentMethod = this.wizardData.step4.paymentMethod;

            // Log wizard data being sent
            console.log('[Wizard] Submitting booking');
            console.log('[Wizard] Payment method:', paymentMethod);
            console.log('[Wizard] Wizard data:', this.wizardData);

            // First create the booking
            $.ajax({
                url: bookingWizardConfig.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'submit_booking_wizard',
                    nonce: bookingWizardConfig.nonce,
                    wizardData: this.wizardData
                },
                beforeSend: () => {
                    $('.wizard-payment-btn').prop('disabled', true).text('Creating booking...');
                },
                success: (response) => {
                    if (response.success) {
                        // Booking created, now process payment
                        const bookingData = response.data;
                        this.wizardData.bookingId = bookingData.bookingId;
                        this.wizardData.bookingUuid = bookingData.bookingUuid;
                        this.wizardData.bookingReference = bookingData.bookingReference;

                        console.log('[Wizard] Booking created:', bookingData);

                        // Process payment based on method
                        if (paymentMethod === 'flywire') {
                            this.processFlywirePayment(bookingData);
                        } else if (paymentMethod === 'stripe' || paymentMethod === 'stripe-embedded') {
                            this.processStripePayment(bookingData);
                        } else {
                            alert('Invalid payment method selected');
                            $('.wizard-payment-btn').prop('disabled', false).text('Pay Now');
                        }
                    } else {
                        // Enhanced error logging
                        console.error('[Wizard] Booking creation failed');
                        console.error('[Wizard] Error message:', response.data.message);

                        if (response.data.details) {
                            console.error('[Wizard] Error details:', response.data.details);

                            // Show detailed error if available
                            if (response.data.details.http_status) {
                                console.error('[Wizard] HTTP Status:', response.data.details.http_status);
                            }
                            if (response.data.details.url) {
                                console.error('[Wizard] API URL:', response.data.details.url);
                            }
                            if (response.data.details.response_body) {
                                console.error('[Wizard] API Response:', response.data.details.response_body);
                            }
                        }

                        alert('Error creating booking: ' + response.data.message + '\n\nCheck browser console for details.');
                        $('.wizard-payment-btn').prop('disabled', false).text('Pay Now');
                    }
                },
                error: (xhr, status, error) => {
                    console.error('[Wizard] AJAX request failed');
                    console.error('[Wizard] Status:', status);
                    console.error('[Wizard] Error:', error);
                    console.error('[Wizard] Response:', xhr.responseText);

                    alert('An error occurred. Please try again.\n\nCheck browser console for details.');
                    $('.wizard-payment-btn').prop('disabled', false).text('Pay Now');
                }
            });
        }

        /**
         * Process Flywire payment
         */
        processFlywirePayment(bookingData) {
            console.log('[Wizard] Processing Flywire payment');

            // Calculate totals
            const totals = this.calculateTotals();
            const billing = this.wizardData.step3; // Step 3 contains billing info
            const packageTitle = this.wizardData.packageData?.title || 'Tour Package';

            // Prepare Flywire configuration
            const config = {
                env: bookingWizardConfig.flywire.env,
                recipientCode: bookingWizardConfig.flywire.portalCode,
                amount: totals.total,

                // Pre-fill payer information (from step3)
                firstName: billing.firstName,
                lastName: billing.lastName,
                email: billing.email,
                phone: billing.phone,
                address: billing.address,
                city: billing.city,
                state: billing.state,
                zip: billing.zip,
                country: billing.country,

                // Recipient fields
                recipientFields: {
                    trip_name: packageTitle,
                    booking_number: bookingData.bookingId,
                    additional_comments: billing.notes || '',
                    booking_reference: bookingData.bookingReference,
                    type: this.wizardData.step4.paymentOption === 'full' ? 1 : 4
                },

                // Payment options
                paymentOptionsConfig: {
                    filters: {
                        type: ['online', 'credit_card']
                    }
                },

                // Request info
                requestPayerInfo: true,
                requestRecipientInfo: true,
                skipCompletedSteps: true,

                // Callback configuration
                callbackId: bookingData.bookingReference,
                callbackUrl: `${bookingWizardConfig.flywire.apiUrl}/flywire-notifications`,
                callbackVersion: '2',

                // Input validation handler
                onInvalidInput: (errors) => {
                    errors.forEach((error) => {
                        console.error('[Flywire] Validation error:', error.msg);
                    });
                },

                // Completion callback
                onCompleteCallback: async (args) => {
                    const { reference, status } = args;

                    console.log('[Flywire] Payment completed:', { reference, status });

                    // Update invoice with payment details
                    await this.updateFlywireInvoice({
                        payment_id: reference,
                        first_name: billing.firstName,
                        last_name: billing.lastName,
                        email: billing.email,
                        phone: billing.phone,
                        address: billing.address,
                        city: billing.city,
                        state: billing.state,
                        zip: billing.zip,
                        country: billing.country,
                        fields: {},
                        payment_method: {},
                        external_reference: bookingData.bookingReference
                    });

                    // Close wizard
                    this.close();

                    // Redirect to confirmation page
                    window.location.href = `/booking/confirmation?status=${status}&invoice=${reference}`;
                }
            };

            // Initialize and render Flywire modal
            try {
                if (window.FlywirePayment) {
                    const modal = window.FlywirePayment.initiate(config);
                    modal?.render();
                } else {
                    console.error('[Wizard] Flywire script not loaded');
                    alert('Payment system not available. Please try again.');
                    $('.wizard-payment-btn').prop('disabled', false).text('Pay Now');
                }
            } catch (error) {
                console.error('[Wizard] Flywire error:', error);
                alert('An error occurred with the payment system. Please try again.');
                $('.wizard-payment-btn').prop('disabled', false).text('Pay Now');
            }
        }

        /**
         * Update Flywire invoice
         */
        updateFlywireInvoice(data) {
            return $.ajax({
                url: bookingWizardConfig.flywire.apiUrl + '/flywire-notifications',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ data: data }),
                success: (result) => {
                    console.log('[Wizard] Invoice updated:', result);
                },
                error: (error) => {
                    console.error('[Wizard] Invoice update failed:', error);
                }
            });
        }

        /**
         * Process Stripe payment
         */
        async processStripePayment(bookingData) {
            console.log('[Wizard] Processing Stripe payment');

            // Calculate totals
            const totals = this.calculateTotals();
            const billing = this.wizardData.step3; // Step 3 contains billing info
            const packageTitle = this.wizardData.packageData?.title || 'Tour Package';
            const travellers = this.wizardData.step1.travellers || 1;

            // Prepare checkout data
            const checkoutData = {
                customerEmail: billing.email,
                customerName: `${billing.firstName} ${billing.lastName}`,
                totalPrice: totals.total,
                packageName: packageTitle,
                packageDetails: `${travellers} traveller(s)`,
                packageId: this.wizardData.packageId,
                bookingId: bookingData.bookingId,
                bookingReference: bookingData.bookingReference,
                currency: 'usd',
                passengerCount: travellers
            };

            // Create Stripe checkout session
            $.ajax({
                url: bookingWizardConfig.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'create_stripe_checkout',
                    nonce: bookingWizardConfig.nonce,
                    checkoutData: checkoutData,
                    bookingData: bookingData
                },
                success: async (response) => {
                    if (response.success) {
                        console.log('[Wizard] Stripe session created:', response.data);

                        // Initialize Stripe
                        const stripe = await this.getStripeInstance();
                        if (!stripe) {
                            alert('Payment system not available. Please try again.');
                            $('.wizard-payment-btn').prop('disabled', false).text('Pay Now');
                            return;
                        }

                        // Initialize embedded checkout
                        const checkout = await stripe.initEmbeddedCheckout({
                            clientSecret: response.data.clientSecret
                        });

                        // Mount checkout in the wizard
                        // Note: You'll need to add a container in the template
                        const checkoutContainer = $('#stripe-checkout-container');
                        if (checkoutContainer.length) {
                            // Hide wizard content, show checkout
                            $('.wizard-step').hide();
                            checkoutContainer.show();
                            checkout.mount(checkoutContainer[0]);
                        } else {
                            // Fallback: redirect to hosted checkout
                            if (response.data.url) {
                                window.location.href = response.data.url;
                            } else {
                                alert('Payment URL not available');
                                $('.wizard-payment-btn').prop('disabled', false).text('Pay Now');
                            }
                        }
                    } else {
                        alert('Error creating payment session: ' + response.data.message);
                        $('.wizard-payment-btn').prop('disabled', false).text('Pay Now');
                    }
                },
                error: (xhr, status, error) => {
                    console.error('[Wizard] Stripe session creation failed:', error);
                    alert('An error occurred. Please try again.');
                    $('.wizard-payment-btn').prop('disabled', false).text('Pay Now');
                }
            });
        }

        /**
         * Get Stripe instance (cached)
         */
        async getStripeInstance() {
            if (this.stripeInstance) {
                return this.stripeInstance;
            }

            if (!window.Stripe) {
                console.error('[Wizard] Stripe.js not loaded');
                return null;
            }

            const publishableKey = bookingWizardConfig.stripe.publishableKey;
            if (!publishableKey) {
                console.error('[Wizard] Stripe publishable key not configured');
                return null;
            }

            this.stripeInstance = window.Stripe(publishableKey);
            return this.stripeInstance;
        }

        /**
         * Calculate totals for payment
         */
        calculateTotals() {
            const step1 = this.wizardData.step1 || {};
            const step2 = this.wizardData.step2 || {};
            const step4 = this.wizardData.step4 || {};

            const travellers = parseInt(step1.travellers) || 1;
            const roomType = step1.roomType || 'twin';
            const selectedPrice = parseFloat(this.wizardData.selectedPrice) || 0;
            const singleSupp = parseFloat(this.wizardData.singleSupp) || 0;

            // Calculate base package cost
            let packageCost = selectedPrice * travellers;

            // Add solo supplement if applicable
            if (roomType === 'solo' && travellers === 1) {
                packageCost += singleSupp;
            } else if (travellers > 1 && travellers % 2 !== 0) {
                // Odd number, last person gets solo supplement
                packageCost += singleSupp;
            }

            // Calculate extras
            let extrasTotal = 0;
            if (step2.extras) {
                step2.extras.forEach(extra => {
                    extrasTotal += (parseFloat(extra.price) || 0) * (parseInt(extra.quantity) || 0);
                });
            }

            // Calculate addons
            let addonsTotal = 0;
            if (step2.addons) {
                step2.addons.forEach(addon => {
                    addonsTotal += (parseFloat(addon.price) || 0) * (parseInt(addon.quantity) || 0);
                });
            }

            const subtotal = packageCost + extrasTotal + addonsTotal;

            // Determine amount to pay
            const paymentOption = step4.paymentOption || 'full';
            const amountToPay = (paymentOption === 'deposit') ? 200.00 : subtotal;

            // Calculate fee (4%)
            const fee = parseFloat((amountToPay * 0.04).toFixed(2));
            const total = amountToPay + fee;

            return {
                packageCost: packageCost,
                extrasTotal: extrasTotal,
                addonsTotal: addonsTotal,
                subtotal: subtotal,
                amountToPay: amountToPay,
                fee: fee,
                total: total
            };
        }

        /**
         * Populate package data
         */
        populatePackageData() {
            // TODO: Fetch package data from backend or use data passed in open()
            // For now, using placeholder data

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
            // Update package title and link
            if (packageData.title) {
                $('.wizard-package-link').text(packageData.title);
                if (packageData.permalink) {
                    $('.wizard-package-link').attr('href', packageData.permalink);
                }
            }

            // Update package image
            if (packageData.thumbnail) {
                $('.wizard-package-info__thumbnail img').attr('src', packageData.thumbnail);
                $('.wizard-package-info__thumbnail img').attr('alt', packageData.title || 'Package thumbnail');
            }

            // Update price (update all instances in TWIN and SOLO rooms)
            if (packageData.price_from) {
                const formattedPrice = `USD $ ${this.formatPrice(packageData.price_from)}`;
                $('.wizard-room-card[data-room="twin"] .price-amount').text(formattedPrice);
                $('.price-amount').first().text(formattedPrice);
            }

            // Update duration (days and nights)
            if (packageData.duration) {
                const days = packageData.duration;
                const nights = days - 1;
                $('.duration-days').text(days);
                $('.duration-nights').text(nights);
            }

            // Update max travellers
            if (packageData.max_people) {
                $('#wizard-travellers').attr('max', packageData.max_people);
            }

            // Store package data for later use
            this.wizardData.packageData = packageData;

            console.log('[Wizard] Package display updated:', packageData);
        }

        /**
         * Format price with thousands separator
         */
        formatPrice(price) {
            return price.toLocaleString('en-US', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            });
        }

        /**
         * Update room type availability based on number of travellers
         */
        updateRoomTypeAvailability() {
            const travellers = parseInt($('#wizard-travellers').val()) || 1;
            const selectedPrice = this.wizardData.selectedPrice || 0;
            const singleSupp = this.wizardData.singleSupp || 0;
            const priceNormal = this.wizardData.packageData?.price_normal || selectedPrice;

            // Calculate prices based on travellers logic
            let twinPrice = 0;
            let soloPrice = 0;

            if (travellers === 1) {
                // 1 person: can choose TWIN or SOLO
                twinPrice = selectedPrice;
                soloPrice = priceNormal + singleSupp;

                // Enable room selection
                $('input[name="room_type"]').prop('disabled', false);
                $('.wizard-room-card').removeClass('wizard-room-card--disabled');

            } else {
                // 2+ people: TWIN for pairs, SOLO for odd person
                const pairs = Math.floor(travellers / 2);
                const hasOddPerson = travellers % 2 !== 0;

                twinPrice = pairs * 2 * selectedPrice;
                if (hasOddPerson) {
                    twinPrice += (pairs * 2 * selectedPrice); // Twin price for pairs
                    soloPrice = priceNormal + singleSupp; // Solo price for odd person
                }

                // Total for all travellers
                const totalPrice = twinPrice + (hasOddPerson ? soloPrice : 0);

                // Disable room selection and force TWIN
                $('input[name="room_type"]').prop('disabled', true);
                $('input[name="room_type"][value="twin"]').prop('checked', true).prop('disabled', false);
                $('.wizard-room-card').addClass('wizard-room-card--disabled');
                $('.wizard-room-card[data-room="twin"]').removeClass('wizard-room-card--disabled').addClass('wizard-room-card--selected');
                $('.wizard-room-card[data-room="solo"]').removeClass('wizard-room-card--selected');

                // Update TWIN card to show total
                twinPrice = totalPrice;
                soloPrice = 0; // Not selectable
            }

            // Update prices in UI
            const formattedTwin = `USD $ ${this.formatPrice(twinPrice)}`;
            const formattedSolo = soloPrice > 0 ? `USD $ ${this.formatPrice(soloPrice)}` : 'N/A';

            $('.wizard-room-card[data-room="twin"] .price-amount').text(formattedTwin);
            if (travellers === 1) {
                $('.wizard-room-card[data-room="solo"] .price-amount').text(formattedSolo);
            } else {
                $('.wizard-room-card[data-room="solo"] .price-amount').text('Auto-calculated');
            }

            console.log('[Wizard] Room prices updated:', {
                travellers: travellers,
                selectedPrice: selectedPrice,
                singleSupp: singleSupp,
                twinPrice: twinPrice,
                soloPrice: soloPrice
            });
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
        console.log('📄 BookingWizard: Document ready, creating BookingWizard instance...');

        const wizardInstance = new BookingWizard();

        console.log('✅ BookingWizard: Instance created:', wizardInstance);

        // Make it globally accessible for debugging
        window.bookingWizard = wizardInstance;

        console.log('💡 BookingWizard: You can access wizard via window.bookingWizard');
    });

})(jQuery);
