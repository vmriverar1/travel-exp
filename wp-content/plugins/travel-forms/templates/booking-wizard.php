<?php
/**
 * Template: Booking Wizard Aside
 *
 * 4-step wizard for package booking:
 * 1. Booking Details
 * 2. Add-ons & Extras
 * 3. Billing Information
 * 4. Order Details
 */

defined('ABSPATH') || exit;
?>

<!-- Overlay -->
<div id="booking-wizard-overlay" class="booking-wizard-overlay">

    <!-- Aside Panel -->
    <aside id="booking-wizard-aside" class="booking-wizard-aside">

        <!-- Close Button -->
        <button type="button" class="booking-wizard-close" aria-label="<?php esc_attr_e('Close', 'travel-forms'); ?>">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                <path d="M12 4L4 12M4 4L12 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
        </button>

        <!-- Stepper Header -->
        <div class="booking-wizard-stepper">
            <div class="stepper-step stepper-step--active" data-step="1">
                <div class="stepper-step__circle">1</div>
                <div class="stepper-step__label"><?php esc_html_e('Booking Details', 'travel-forms'); ?></div>
            </div>
            <div class="stepper-step" data-step="2">
                <div class="stepper-step__circle">2</div>
                <div class="stepper-step__label"><?php esc_html_e('Add-ons & Extras', 'travel-forms'); ?></div>
            </div>
            <div class="stepper-step" data-step="3">
                <div class="stepper-step__circle">3</div>
                <div class="stepper-step__label"><?php esc_html_e('Billing Information', 'travel-forms'); ?></div>
            </div>
            <div class="stepper-step" data-step="4">
                <div class="stepper-step__circle">4</div>
                <div class="stepper-step__label"><?php esc_html_e('Order Details', 'travel-forms'); ?></div>
            </div>
        </div>

        <!-- Wizard Content (Scrollable) -->
        <div class="booking-wizard-content">

            <!-- Step 1: Booking Details -->
            <div class="wizard-step wizard-step--active" data-step="1">
                <?php include TRAVEL_FORMS_PATH . 'templates/wizard-steps/step-1-booking-details.php'; ?>
            </div>

            <!-- Step 2: Add-ons & Extras -->
            <div class="wizard-step" data-step="2" style="display: none;">
                <?php include TRAVEL_FORMS_PATH . 'templates/wizard-steps/step-2-addons.php'; ?>
            </div>

            <!-- Step 3: Billing Information -->
            <div class="wizard-step" data-step="3" style="display: none;">
                <?php include TRAVEL_FORMS_PATH . 'templates/wizard-steps/step-3-billing.php'; ?>
            </div>

            <!-- Step 4: Order Details -->
            <div class="wizard-step" data-step="4" style="display: none;">
                <?php include TRAVEL_FORMS_PATH . 'templates/wizard-steps/step-4-order.php'; ?>
            </div>

        </div>

    </aside>

</div>
