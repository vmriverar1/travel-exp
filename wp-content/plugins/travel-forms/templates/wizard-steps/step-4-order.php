<?php
/**
 * Step 4: Order Details
 *
 * Order summary, payment options, and payment method selection
 */

defined('ABSPATH') || exit;
?>

<div class="wizard-step-inner">

    <!-- Package Summary -->
    <div class="wizard-order-summary">
        <div class="wizard-order-summary__header">
            <h3 class="wizard-order-summary__title">
                <a href="#" class="wizard-link"><?php esc_html_e('Trek Along the Inca Trail To Machu Picchu', 'travel-forms'); ?></a>
            </h3>
            <span class="wizard-order-summary__duration">7 <?php esc_html_e('Days', 'travel-forms'); ?> / 6 <?php esc_html_e('nights', 'travel-forms'); ?></span>
        </div>
        <div class="wizard-order-summary__dates">
            <strong><?php esc_html_e('From:', 'travel-forms'); ?></strong> Fri. Oct 03 2025
            <strong><?php esc_html_e('To:', 'travel-forms'); ?></strong> Fri. Oct 03 2025
        </div>
    </div>

    <!-- Cost Breakdown -->
    <div class="wizard-cost-breakdown">

        <!-- Adult Price -->
        <div class="wizard-cost-item">
            <div class="wizard-cost-item__label">
                <?php esc_html_e('Adult price:', 'travel-forms'); ?> USD 929 × <span class="pax-count">2</span> pax
            </div>
            <div class="wizard-cost-item__amount">$ 1,858</div>
        </div>

        <!-- Add-ons -->
        <div class="wizard-cost-section">
            <div class="wizard-cost-section__title"><?php esc_html_e('Tour Add-ons / Extra Services', 'travel-forms'); ?></div>
            <div class="wizard-cost-item wizard-cost-item--addon">
                <div class="wizard-cost-item__label">• Wayna Picchu × 2 pax</div>
                <div class="wizard-cost-item__amount">$ 158</div>
            </div>
            <div class="wizard-cost-item wizard-cost-item--addon">
                <div class="wizard-cost-item__label">• Alpine Carbon Cork Trekking Poles × 1 pax</div>
                <div class="wizard-cost-item__amount">$ 20</div>
            </div>
        </div>

        <!-- Promo Code -->
        <div class="wizard-promo-code">
            <div class="wizard-promo-code__label"><?php esc_html_e('Redeem Your Promo Code:', 'travel-forms'); ?></div>
            <div class="wizard-promo-code__input-wrap">
                <input
                    type="text"
                    id="promo-code"
                    class="wizard-promo-code__input"
                    placeholder="<?php esc_attr_e('Enter code', 'travel-forms'); ?>"
                    value="MAPCH258"
                />
                <button type="button" class="wizard-promo-code__btn" aria-label="<?php esc_attr_e('Apply promo code', 'travel-forms'); ?>">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                        <path d="M6 12l6-6M6 6l6 6" stroke="#333" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                </button>
            </div>
            <div class="wizard-promo-code__discount">( − $ 20 )</div>
        </div>

        <!-- Total -->
        <div class="wizard-cost-total">
            <div class="wizard-cost-total__label"><?php esc_html_e('Total Trip Cost (USD):', 'travel-forms'); ?></div>
            <div class="wizard-cost-total__amount">$ 2,016</div>
        </div>

    </div>

    <!-- Payment Options -->
    <div class="wizard-payment-options">
        <div class="wizard-section-title"><?php esc_html_e('Payment Options', 'travel-forms'); ?></div>

        <div class="wizard-payment-cards">
            <!-- Pay in Full -->
            <label class="wizard-payment-card wizard-payment-card--selected">
                <input type="radio" name="payment_option" value="full" checked />
                <div class="wizard-payment-card__content">
                    <div class="wizard-payment-card__header">
                        <span class="wizard-payment-card__title"><?php esc_html_e('Pay in Full', 'travel-forms'); ?></span>
                        <span class="wizard-payment-card__amount">$ 2,016</span>
                    </div>
                    <p class="wizard-payment-card__description">
                        <?php esc_html_e('Make a single payment covering the entire cost of your trip.', 'travel-forms'); ?>
                    </p>
                </div>
            </label>

            <!-- Deposit in Advance -->
            <label class="wizard-payment-card">
                <input type="radio" name="payment_option" value="deposit" />
                <div class="wizard-payment-card__content">
                    <div class="wizard-payment-card__header">
                        <span class="wizard-payment-card__title"><?php esc_html_e('Deposit in Advance', 'travel-forms'); ?></span>
                        <span class="wizard-payment-card__amount">$ 400</span>
                    </div>
                    <p class="wizard-payment-card__description">
                        <?php esc_html_e('Secure your booking now by paying a', 'travel-forms'); ?>
                        <strong>$200 <?php esc_html_e('deposit per traveler', 'travel-forms'); ?></strong>.
                    </p>
                </div>
            </label>
        </div>

        <p class="wizard-payment-note">
            * <?php esc_html_e('A 4% fee is added for payments made by credit card (on total or deposit).', 'travel-forms'); ?>
        </p>
    </div>

    <!-- Select Payment Method -->
    <div class="wizard-payment-methods">
        <div class="wizard-section-title"><?php esc_html_e('Final Step: Select Payment Method', 'travel-forms'); ?></div>

        <div class="wizard-payment-buttons">
            <!-- PayPal -->
            <button type="button" class="wizard-payment-btn wizard-payment-btn--paypal" data-method="paypal">
                <span class="wizard-payment-btn__logo">
                    <svg width="80" height="24" viewBox="0 0 80 24" fill="#003087">
                        <text x="0" y="18" font-size="18" font-weight="bold">PayPal</text>
                    </svg>
                </span>
                <span class="wizard-payment-btn__label"><?php esc_html_e('CHECK OUT', 'travel-forms'); ?></span>
                <span class="wizard-payment-btn__arrows">»»»</span>
            </button>

            <!-- Flywire -->
            <button type="button" class="wizard-payment-btn wizard-payment-btn--flywire" data-method="flywire">
                <span class="wizard-payment-btn__logo">F</span>
                <span class="wizard-payment-btn__label"><?php esc_html_e('Pay by Flywire', 'travel-forms'); ?></span>
                <span class="wizard-payment-btn__arrows">»»»</span>
            </button>

            <!-- Stripe -->
            <button type="button" class="wizard-payment-btn wizard-payment-btn--stripe" data-method="stripe">
                <span class="wizard-payment-btn__logo">
                    <svg width="60" height="24" viewBox="0 0 60 24" fill="#FFF">
                        <text x="0" y="18" font-size="16" font-weight="600">stripe</text>
                    </svg>
                </span>
                <span class="wizard-payment-btn__label"><?php esc_html_e('Pay by stripe', 'travel-forms'); ?></span>
                <span class="wizard-payment-btn__arrows">»»»</span>
            </button>
        </div>

        <hr class="wizard-divider" />
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
            <span id="wizard-countdown-step4">19:59</span>
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
