<?php
/**
 * Step 1: Booking Details
 *
 * - Tour/Package info
 * - Travel dates
 * - Number of travellers
 * - Room type selection
 */

defined('ABSPATH') || exit;
?>

<div class="wizard-step-inner">

    <!-- Tour / Package Info -->
    <div class="wizard-package-info">
        <div class="wizard-package-info__label">
            <?php esc_html_e('Tour / Package name:', 'travel-forms'); ?>
        </div>
        <div class="wizard-package-info__content">
            <div class="wizard-package-info__text">
                <h3 class="wizard-package-info__title">
                    <a href="#" class="wizard-package-link" data-package-id="">
                        <?php esc_html_e('Loading...', 'travel-forms'); ?>
                    </a>
                </h3>
                <div class="wizard-package-info__meta">
                    <span class="wizard-package-duration" data-duration="">
                        <span class="duration-days">7</span> <?php esc_html_e('Days', 'travel-forms'); ?> /
                        <span class="duration-nights">6</span> <?php esc_html_e('nights', 'travel-forms'); ?>
                    </span>
                    <span class="wizard-package-price">
                        <span class="price-currency">USD $</span>
                        <span class="price-amount">1,023</span>
                    </span>
                </div>
                <div class="wizard-package-rating">
                    <div class="rating-stars">
                        <svg width="80" height="16" viewBox="0 0 80 16" fill="#FFC107">
                            <path d="M8 0l2.163 4.38 4.837.705-3.5 3.41.826 4.815L8 11.055l-4.326 2.255.826-4.815-3.5-3.41 4.837-.705L8 0z"/>
                            <path d="M24 0l2.163 4.38 4.837.705-3.5 3.41.826 4.815L24 11.055l-4.326 2.255.826-4.815-3.5-3.41 4.837-.705L24 0z"/>
                            <path d="M40 0l2.163 4.38 4.837.705-3.5 3.41.826 4.815L40 11.055l-4.326 2.255.826-4.815-3.5-3.41 4.837-.705L40 0z"/>
                            <path d="M56 0l2.163 4.38 4.837.705-3.5 3.41.826 4.815L56 11.055l-4.326 2.255.826-4.815-3.5-3.41 4.837-.705L56 0z"/>
                            <path d="M72 0l2.163 4.38 4.837.705-3.5 3.41.826 4.815L72 11.055l-4.326 2.255.826-4.815-3.5-3.41 4.837-.705L72 0z"/>
                        </svg>
                    </div>
                    <a href="#" class="rating-link">
                        <span class="rating-count">2,047</span> <?php esc_html_e('reviews', 'travel-forms'); ?>
                    </a>
                </div>
            </div>
            <div class="wizard-package-info__thumbnail">
                <img src="https://placehold.co/140x80/2EC4B6/FFFFFF?text=Tour" alt="" />
            </div>
        </div>
    </div>

    <!-- Travel Dates -->
    <div class="wizard-field">
        <label for="wizard-travel-dates" class="wizard-field__label">
            <?php esc_html_e('Travel Dates:', 'travel-forms'); ?>
        </label>
        <div class="wizard-date-range">
            <input
                type="text"
                id="wizard-travel-dates"
                class="wizard-date-range-input"
                placeholder="From: Fri. Oct 03 2025  >  To: Fri. Oct 03 2025"
                readonly
            />
            <svg class="wizard-date-range-icon" width="16" height="16" viewBox="0 0 16 16" fill="none">
                <rect x="2" y="3" width="12" height="11" rx="2" stroke="#2EC4B6" stroke-width="1.5"/>
                <path d="M2 6h12M5 1v2M11 1v2" stroke="#2EC4B6" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
        </div>
    </div>

    <!-- Number of Travellers -->
    <div class="wizard-field">
        <label for="wizard-travellers" class="wizard-field__label">
            <?php esc_html_e('Number of Travellers*', 'travel-forms'); ?>
            <svg width="16" height="16" viewBox="0 0 16 16" fill="#2EC4B6">
                <path d="M8 8a3 3 0 100-6 3 3 0 000 6zM8 10c-3.33 0-6 2-6 4v1h12v-1c0-2-2.67-4-6-4z"/>
            </svg>
        </label>
        <div class="wizard-stepper">
            <button type="button" class="wizard-stepper__btn wizard-stepper__btn--minus" data-action="decrement">−</button>
            <input
                type="number"
                id="wizard-travellers"
                class="wizard-stepper__value"
                value="2"
                min="1"
                max="12"
                readonly
            />
            <button type="button" class="wizard-stepper__btn wizard-stepper__btn--plus" data-action="increment">+</button>
        </div>
    </div>

    <!-- Room Type Selection -->
    <div class="wizard-field">
        <label class="wizard-field__label">
            <?php esc_html_e('Room Type Selection*', 'travel-forms'); ?>
        </label>
        <div class="wizard-room-types">

            <!-- TWIN Room -->
            <label class="wizard-room-card wizard-room-card--selected" data-room="twin">
                <input type="radio" name="room_type" value="twin" checked class="wizard-room-card__radio" />
                <div class="wizard-room-card__content">
                    <div class="wizard-room-card__text">
                        <div class="wizard-room-card__title"><?php esc_html_e('TWIN Room', 'travel-forms'); ?></div>
                        <div class="wizard-room-card__description">
                            <?php esc_html_e('Single travelers will be assigned a same-gender roommate.', 'travel-forms'); ?>
                        </div>
                    </div>
                    <div class="wizard-room-card__price">
                        <span class="price-amount">USD $ 1,023</span>
                        <button type="button" class="price-info-btn" title="<?php esc_attr_e('Price information', 'travel-forms'); ?>">
                            <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                                <circle cx="7" cy="7" r="6" stroke="#999" stroke-width="1"/>
                                <text x="7" y="10" text-anchor="middle" font-size="10" fill="#999">i</text>
                            </svg>
                        </button>
                    </div>
                </div>
            </label>

            <!-- SOLO Private Room -->
            <label class="wizard-room-card" data-room="solo">
                <input type="radio" name="room_type" value="solo" class="wizard-room-card__radio" />
                <div class="wizard-room-card__content">
                    <div class="wizard-room-card__text">
                        <div class="wizard-room-card__title"><?php esc_html_e('SOLO Private Room', 'travel-forms'); ?></div>
                        <div class="wizard-room-card__description">
                            <?php esc_html_e('Perfect for solo travelers who prefer a private space.', 'travel-forms'); ?>
                        </div>
                    </div>
                    <div class="wizard-room-card__price">
                        <span class="price-amount">USD $ 1,233</span>
                        <button type="button" class="price-info-btn" title="<?php esc_attr_e('Price information', 'travel-forms'); ?>">
                            <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                                <circle cx="7" cy="7" r="6" stroke="#999" stroke-width="1"/>
                                <text x="7" y="10" text-anchor="middle" font-size="10" fill="#999">i</text>
                            </svg>
                        </button>
                    </div>
                </div>
            </label>

        </div>
        <p class="wizard-field__note">
            * <?php esc_html_e('If you require a different room configuration, please', 'travel-forms'); ?>
            <a href="#" class="wizard-link"><?php esc_html_e('contact us', 'travel-forms'); ?></a>.
        </p>
    </div>

    <!-- Continue Button -->
    <div class="wizard-actions">
        <button type="button" class="wizard-btn wizard-btn--primary" data-action="continue">
            <?php esc_html_e('CONTINUE', 'travel-forms'); ?> ›
        </button>
    </div>

    <!-- Urgency Warning + Countdown -->
    <div class="wizard-urgency">
        <div class="wizard-urgency__content">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" class="wizard-urgency__icon">
                <circle cx="8" cy="8" r="7" stroke="#666" stroke-width="1.5"/>
                <path d="M8 4v5l3 2" stroke="#666" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
            <span><?php esc_html_e('Prices may go up if you abandon this offer', 'travel-forms'); ?></span>
        </div>
        <div class="wizard-urgency__countdown">
            <span id="wizard-countdown">19:59</span>
        </div>
    </div>

    <!-- Support Line -->
    <div class="wizard-support">
        <span><?php esc_html_e('Questions?', 'travel-forms'); ?>,</span>
        <?php esc_html_e('please call', 'travel-forms'); ?>
        <a href="tel:+18609565858" class="wizard-link">1-(860) 956 5858</a>
        <?php esc_html_e('or', 'travel-forms'); ?>
        <a href="tel:+19179832727" class="wizard-link">1-(917) 983 2727</a>
    </div>

    <!-- Benefits -->
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

    <!-- QR Card -->
    <div class="wizard-qr-card">
        <div class="wizard-qr-card__title"><?php esc_html_e('Save or Share your Booking', 'travel-forms'); ?></div>
        <div class="wizard-qr-card__code">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=https://example.com/booking" alt="QR Code" />
        </div>
        <div class="wizard-qr-card__actions">
            <button type="button" class="wizard-qr-action" title="<?php esc_attr_e('Save', 'travel-forms'); ?>">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="#2EC4B6">
                    <path d="M8 12L3 7l1.5-1.5L7 8V0h2v8l2.5-2.5L13 7l-5 5zm-6 2h12v2H2v-2z"/>
                </svg>
            </button>
            <button type="button" class="wizard-qr-action" title="<?php esc_attr_e('Share', 'travel-forms'); ?>">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="#2EC4B6">
                    <circle cx="4" cy="8" r="2"/><circle cx="12" cy="4" r="2"/><circle cx="12" cy="12" r="2"/>
                    <path d="M5.5 7l5-2M5.5 9l5 2" stroke="#2EC4B6" stroke-width="1.5"/>
                </svg>
            </button>
        </div>
    </div>

</div>
