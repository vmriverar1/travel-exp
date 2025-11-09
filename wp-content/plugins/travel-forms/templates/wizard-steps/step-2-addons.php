<?php
/**
 * Step 2: Add-ons & Extras
 *
 * Optional services and add-ons for the tour
 */

defined('ABSPATH') || exit;
?>

<div class="wizard-step-inner">

    <!-- Intro Text -->
    <p class="wizard-intro-text">
        <?php esc_html_e('Beyond what\'s already included in your tour, you can personalize your experience with these optional add-ons.', 'travel-forms'); ?>
    </p>

    <!-- Add-ons List -->
    <div class="wizard-addons-list">

        <!-- Example Add-on 1 -->
        <div class="wizard-addon-item">
            <div class="wizard-addon-item__header">
                <div class="wizard-addon-item__title">
                    <?php esc_html_e('Cusco City Tour', 'travel-forms'); ?>
                    <span class="wizard-addon-item__day">(<?php esc_html_e('on 2nd Day', 'travel-forms'); ?>)</span>
                </div>
                <div class="wizard-addon-item__controls">
                    <span class="wizard-addon-item__price">USD $ 74</span>
                    <div class="wizard-stepper wizard-stepper--small">
                        <button type="button" class="wizard-stepper__btn wizard-stepper__btn--minus" data-addon="cusco-city-tour" disabled>−</button>
                        <span class="wizard-stepper__value" data-addon="cusco-city-tour">0</span>
                        <button type="button" class="wizard-stepper__btn wizard-stepper__btn--plus" data-addon="cusco-city-tour">+</button>
                    </div>
                    <button type="button" class="wizard-addon-item__toggle" aria-label="<?php esc_attr_e('Toggle details', 'travel-forms'); ?>">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                            <path d="M4 6l4 4 4-4" stroke="#6B6B6B" stroke-width="1.5" stroke-linecap="round"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Example Add-on 2 (with details expanded) -->
        <div class="wizard-addon-item">
            <div class="wizard-addon-item__header">
                <div class="wizard-addon-item__title">
                    <?php esc_html_e('Wayna Picchu', 'travel-forms'); ?>
                    <span class="wizard-addon-item__day">(<?php esc_html_e('on 6th Day', 'travel-forms'); ?>)</span>
                </div>
                <div class="wizard-addon-item__controls">
                    <span class="wizard-addon-item__price">USD $ 79</span>
                    <div class="wizard-stepper wizard-stepper--small">
                        <button type="button" class="wizard-stepper__btn wizard-stepper__btn--minus" data-addon="wayna-picchu">−</button>
                        <span class="wizard-stepper__value" data-addon="wayna-picchu">1</span>
                        <button type="button" class="wizard-stepper__btn wizard-stepper__btn--plus" data-addon="wayna-picchu">+</button>
                    </div>
                    <button type="button" class="wizard-addon-item__toggle wizard-addon-item__toggle--expanded" aria-label="<?php esc_attr_e('Toggle details', 'travel-forms'); ?>">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                            <path d="M4 10l4-4 4 4" stroke="#6B6B6B" stroke-width="1.5" stroke-linecap="round"/>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="wizard-addon-item__details">
                <div class="wizard-addon-details">
                    <div class="wizard-addon-details__text">
                        <h4 class="wizard-addon-details__title">
                            <a href="#" class="wizard-link"><?php esc_html_e('Wayna Picchu', 'travel-forms'); ?></a>
                        </h4>
                        <p><strong><?php esc_html_e('Duration:', 'travel-forms'); ?></strong> 2 Hours</p>
                        <p class="wizard-addon-details__description">
                            <?php esc_html_e('For those people who have booked their Machu Picchu tour package, climbing Wayna Picchu is a once in a lifetime experience...', 'travel-forms'); ?>
                            <a href="#" class="wizard-link">(<?php esc_html_e('Show more', 'travel-forms'); ?>)</a>
                        </p>
                    </div>
                    <div class="wizard-addon-details__image">
                        <img src="https://placehold.co/140x105/2EC4B6/FFFFFF?text=Wayna" alt="Wayna Picchu" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Example Add-on 3 -->
        <div class="wizard-addon-item">
            <div class="wizard-addon-item__header">
                <div class="wizard-addon-item__title">
                    <?php esc_html_e('Alpine Carbon Cork Trekking Poles', 'travel-forms'); ?>
                </div>
                <div class="wizard-addon-item__controls">
                    <span class="wizard-addon-item__price">USD $ 20</span>
                    <div class="wizard-stepper wizard-stepper--small">
                        <button type="button" class="wizard-stepper__btn wizard-stepper__btn--minus" data-addon="trekking-poles">−</button>
                        <span class="wizard-stepper__value" data-addon="trekking-poles">1</span>
                        <button type="button" class="wizard-stepper__btn wizard-stepper__btn--plus" data-addon="trekking-poles">+</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Example Add-on 4 -->
        <div class="wizard-addon-item">
            <div class="wizard-addon-item__header">
                <div class="wizard-addon-item__title">
                    <?php esc_html_e('Personal Porter per belongings (7Kg cap.)', 'travel-forms'); ?>
                </div>
                <div class="wizard-addon-item__controls">
                    <span class="wizard-addon-item__price">USD $ 20</span>
                    <div class="wizard-stepper wizard-stepper--small">
                        <button type="button" class="wizard-stepper__btn wizard-stepper__btn--minus" data-addon="personal-porter" disabled>−</button>
                        <span class="wizard-stepper__value" data-addon="personal-porter">0</span>
                        <button type="button" class="wizard-stepper__btn wizard-stepper__btn--plus" data-addon="personal-porter">+</button>
                    </div>
                </div>
            </div>
        </div>

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

    <!-- Urgency + Support + Benefits (same as step 1) -->
    <div class="wizard-urgency">
        <div class="wizard-urgency__content">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" class="wizard-urgency__icon">
                <circle cx="8" cy="8" r="7" stroke="#666" stroke-width="1.5"/>
                <path d="M8 4v5l3 2" stroke="#666" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
            <span><?php esc_html_e('Prices may go up if you abandon this offer', 'travel-forms'); ?></span>
        </div>
        <div class="wizard-urgency__countdown">
            <span id="wizard-countdown-step2">19:59</span>
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
