<?php
/**
 * Step 3: Billing Information
 *
 * Customer billing information form
 */

defined('ABSPATH') || exit;
?>

<div class="wizard-step-inner">

    <!-- Billing Form Grid -->
    <div class="wizard-billing-form">

        <!-- Row 1: Title + First Name -->
        <div class="wizard-form-field">
            <label for="billing-title" class="wizard-form-field__label">
                <?php esc_html_e('Title*', 'travel-forms'); ?>
            </label>
            <select id="billing-title" class="wizard-form-field__input" required>
                <option value=""><?php esc_html_e('Select...', 'travel-forms'); ?></option>
                <option value="mr"><?php esc_html_e('Mr.', 'travel-forms'); ?></option>
                <option value="mrs"><?php esc_html_e('Mrs.', 'travel-forms'); ?></option>
                <option value="ms"><?php esc_html_e('Ms.', 'travel-forms'); ?></option>
                <option value="dr"><?php esc_html_e('Dr.', 'travel-forms'); ?></option>
            </select>
        </div>

        <div class="wizard-form-field">
            <label for="billing-first-name" class="wizard-form-field__label">
                <?php esc_html_e('First name*', 'travel-forms'); ?>
            </label>
            <input
                type="text"
                id="billing-first-name"
                class="wizard-form-field__input"
                required
            />
        </div>

        <!-- Row 2: Last Name + Email -->
        <div class="wizard-form-field">
            <label for="billing-last-name" class="wizard-form-field__label">
                <?php esc_html_e('Last name*', 'travel-forms'); ?>
            </label>
            <input
                type="text"
                id="billing-last-name"
                class="wizard-form-field__input"
                required
            />
        </div>

        <div class="wizard-form-field">
            <label for="billing-email" class="wizard-form-field__label">
                <?php esc_html_e('Email Address*', 'travel-forms'); ?>
            </label>
            <input
                type="email"
                id="billing-email"
                class="wizard-form-field__input"
                required
            />
        </div>

        <!-- Row 3: Phone Number -->
        <div class="wizard-form-field wizard-form-field--full">
            <label for="billing-phone" class="wizard-form-field__label">
                <?php esc_html_e('Phone Number*', 'travel-forms'); ?>
            </label>
            <input
                type="tel"
                id="billing-phone"
                class="wizard-form-field__input"
                placeholder="+1 (555) 123-4567"
                required
            />
        </div>

        <!-- Row 4: Document Type + Number -->
        <div class="wizard-form-field">
            <label for="billing-document" class="wizard-form-field__label">
                <?php esc_html_e('Document', 'travel-forms'); ?>
            </label>
            <select id="billing-document" class="wizard-form-field__input">
                <option value="passport"><?php esc_html_e('Passport', 'travel-forms'); ?></option>
                <option value="national_id"><?php esc_html_e('National ID', 'travel-forms'); ?></option>
                <option value="drivers_license"><?php esc_html_e('Driver\'s License', 'travel-forms'); ?></option>
            </select>
        </div>

        <div class="wizard-form-field">
            <label for="billing-document-number" class="wizard-form-field__label">
                <?php esc_html_e('Number', 'travel-forms'); ?>
            </label>
            <input
                type="text"
                id="billing-document-number"
                class="wizard-form-field__input"
            />
        </div>

        <!-- Row 5: Date of Birth + Nationality -->
        <div class="wizard-form-field">
            <label for="billing-dob" class="wizard-form-field__label">
                <?php esc_html_e('Date of birth*', 'travel-forms'); ?>
            </label>
            <div class="wizard-date-input">
                <input
                    type="date"
                    id="billing-dob"
                    class="wizard-form-field__input"
                    placeholder="MM/DD/YYYY"
                    required
                />
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" class="wizard-date-input__icon">
                    <rect x="2" y="3" width="12" height="11" rx="2" stroke="#666" stroke-width="1.5"/>
                    <path d="M2 6h12M5 1v2M11 1v2" stroke="#666" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
            </div>
        </div>

        <div class="wizard-form-field">
            <label for="billing-country" class="wizard-form-field__label">
                <?php esc_html_e('Nationality / Country*', 'travel-forms'); ?>
            </label>
            <select id="billing-country" class="wizard-form-field__input" required>
                <option value=""><?php esc_html_e('Select...', 'travel-forms'); ?></option>
                <option value="US"><?php esc_html_e('United States (US)', 'travel-forms'); ?></option>
                <option value="GB"><?php esc_html_e('United Kingdom (GB)', 'travel-forms'); ?></option>
                <option value="CA"><?php esc_html_e('Canada (CA)', 'travel-forms'); ?></option>
                <option value="AU"><?php esc_html_e('Australia (AU)', 'travel-forms'); ?></option>
                <option value="PE"><?php esc_html_e('Peru (PE)', 'travel-forms'); ?></option>
                <!-- More countries... -->
            </select>
        </div>

        <!-- Row 6: Street Address + State -->
        <div class="wizard-form-field">
            <label for="billing-address" class="wizard-form-field__label">
                <?php esc_html_e('Street Address*', 'travel-forms'); ?>
            </label>
            <input
                type="text"
                id="billing-address"
                class="wizard-form-field__input"
                required
            />
        </div>

        <div class="wizard-form-field">
            <label for="billing-state" class="wizard-form-field__label">
                <?php esc_html_e('State*', 'travel-forms'); ?>
            </label>
            <input
                type="text"
                id="billing-state"
                class="wizard-form-field__input"
                required
            />
        </div>

        <!-- Row 7: City + ZIP Code -->
        <div class="wizard-form-field">
            <label for="billing-city" class="wizard-form-field__label">
                <?php esc_html_e('Town / City*', 'travel-forms'); ?>
            </label>
            <input
                type="text"
                id="billing-city"
                class="wizard-form-field__input"
                required
            />
        </div>

        <div class="wizard-form-field">
            <label for="billing-zip" class="wizard-form-field__label">
                <?php esc_html_e('ZIP Code*', 'travel-forms'); ?>
            </label>
            <input
                type="text"
                id="billing-zip"
                class="wizard-form-field__input"
                required
            />
        </div>

    </div>

    <!-- Terms Checkbox -->
    <div class="wizard-terms">
        <label class="wizard-checkbox">
            <input type="checkbox" id="billing-terms" required />
            <span class="wizard-checkbox__checkmark"></span>
            <span class="wizard-checkbox__label">
                <?php esc_html_e('I have read, understood, and agreed to the', 'travel-forms'); ?>
                <a href="#" class="wizard-link"><?php esc_html_e('booking terms and conditions', 'travel-forms'); ?></a>,
                <?php esc_html_e('which will apply to any confirmed booking.', 'travel-forms'); ?>
            </span>
        </label>
    </div>

    <!-- Navigation Buttons -->
    <div class="wizard-actions wizard-actions--dual">
        <button type="button" class="wizard-btn wizard-btn--outline" data-action="previous">
            ‹ <?php esc_html_e('PREVIOUS', 'travel-forms'); ?>
        </button>
        <button type="button" class="wizard-btn wizard-btn--primary" data-action="continue">
            <?php esc_html_e('CONTINUE', 'travel-forms'); ?> ›
        </button>
    </div>

    <!-- Urgency + Support + Benefits -->
    <div class="wizard-urgency">
        <div class="wizard-urgency__content">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" class="wizard-urgency__icon">
                <circle cx="8" cy="8" r="7" stroke="#666" stroke-width="1.5"/>
                <path d="M8 4v5l3 2" stroke="#666" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
            <span><?php esc_html_e('Prices may go up if you abandon this offer', 'travel-forms'); ?></span>
        </div>
        <div class="wizard-urgency__countdown">
            <span id="wizard-countdown-step3">19:59</span>
        </div>
    </div>

    <div class="wizard-support">
        <span><?php esc_html_e('Questions?', 'travel-forms'); ?>,</span>
        <?php esc_html_e('please call', 'travel-forms'); ?>
        <a href="tel:+18609565858" class="wizard-link">1-(860) 956 5858</a>
        <?php esc_html_e('or', 'travel-forms'); ?>
        <a href="tel:+19179832727" class="wizard-link">1-(917) 983 2727</a>
    </div>

    <div class="wizard-benefits">
        <div class="wizard-benefit-card">
            <div class="wizard-benefit-card__icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="#2EC4B6">
                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="wizard-benefit-card__text">
                <div class="wizard-benefit-card__title"><?php esc_html_e('Price match guarantee', 'travel-forms'); ?></div>
                <div class="wizard-benefit-card__description"><?php esc_html_e('We won\'t be beaten on price', 'travel-forms'); ?></div>
            </div>
        </div>
        <div class="wizard-benefit-card">
            <div class="wizard-benefit-card__icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="#2EC4B6">
                    <path d="M6 18L18 6M6 6l12 12" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </div>
            <div class="wizard-benefit-card__text">
                <div class="wizard-benefit-card__title"><?php esc_html_e('Free cancellation', 'travel-forms'); ?></div>
                <div class="wizard-benefit-card__description">
                    <?php esc_html_e('According booking', 'travel-forms'); ?>
                    <a href="#" class="wizard-link"><?php esc_html_e('terms and conditions', 'travel-forms'); ?></a>
                </div>
            </div>
        </div>
    </div>

    <div class="wizard-qr-card">
        <div class="wizard-qr-card__title"><?php esc_html_e('Save or Share your Booking', 'travel-forms'); ?></div>
        <div class="wizard-qr-card__code">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=https://example.com/booking" alt="QR Code" />
        </div>
        <div class="wizard-qr-card__actions">
            <button type="button" class="wizard-qr-action">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="#2EC4B6">
                    <path d="M8 12L3 7l1.5-1.5L7 8V0h2v8l2.5-2.5L13 7l-5 5zm-6 2h12v2H2v-2z"/>
                </svg>
            </button>
            <button type="button" class="wizard-qr-action">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="#2EC4B6">
                    <circle cx="4" cy="8" r="2"/><circle cx="12" cy="4" r="2"/><circle cx="12" cy="12" r="2"/>
                    <path d="M5.5 7l5-2M5.5 9l5 2" stroke="#2EC4B6" stroke-width="1.5"/>
                </svg>
            </button>
        </div>
    </div>

</div>
