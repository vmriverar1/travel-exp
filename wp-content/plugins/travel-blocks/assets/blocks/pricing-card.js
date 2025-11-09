/**
 * Pricing Card Block JavaScript
 *
 * Handles social share (especially copy link) and modal triggers
 *
 * @package Travel\Blocks
 * @since 1.0.0
 */

(function() {
    'use strict';

    /**
     * Initialize all Pricing Card blocks on the page
     */
    function initPricingCards() {
        const blocks = document.querySelectorAll('.pricing-card');

        blocks.forEach(block => {
            // Skip if already initialized
            if (block.dataset.initialized === 'true') {
                return;
            }

            // Mark as initialized
            block.dataset.initialized = 'true';

            // Initialize copy link functionality
            initCopyLink(block);

            // Initialize modal triggers
            initModalTriggers(block);

            // Initialize currency selector (if present)
            initCurrencySelector(block);
        });
    }

    /**
     * Copy link to clipboard functionality
     */
    function initCopyLink(block) {
        const copyButton = block.querySelector('[data-share-copy]');

        if (!copyButton) {
            return;
        }

        copyButton.addEventListener('click', function(e) {
            e.preventDefault();

            const url = window.location.href;

            // Try modern clipboard API first
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(url)
                    .then(() => {
                        showCopyFeedback(copyButton, 'success');
                    })
                    .catch(() => {
                        fallbackCopyToClipboard(url, copyButton);
                    });
            } else {
                // Fallback for older browsers
                fallbackCopyToClipboard(url, copyButton);
            }
        });
    }

    /**
     * Fallback copy method for older browsers
     */
    function fallbackCopyToClipboard(text, button) {
        const textarea = document.createElement('textarea');
        textarea.value = text;
        textarea.style.position = 'fixed';
        textarea.style.opacity = '0';
        document.body.appendChild(textarea);
        textarea.select();

        try {
            const successful = document.execCommand('copy');
            showCopyFeedback(button, successful ? 'success' : 'error');
        } catch (err) {
            showCopyFeedback(button, 'error');
        }

        document.body.removeChild(textarea);
    }

    /**
     * Show visual feedback when copying
     */
    function showCopyFeedback(button, status) {
        const originalTitle = button.getAttribute('aria-label') || '';
        const feedbackText = status === 'success' ? 'Copied!' : 'Failed to copy';

        // Update button text/icon temporarily
        button.setAttribute('aria-label', feedbackText);
        button.classList.add('pricing-card__share-button--copied');

        // Restore original state after 2 seconds
        setTimeout(() => {
            button.setAttribute('aria-label', originalTitle);
            button.classList.remove('pricing-card__share-button--copied');
        }, 2000);
    }

    /**
     * Initialize modal triggers for booking form
     */
    function initModalTriggers(block) {
        const modalTrigger = block.querySelector('[data-modal-trigger]');

        if (!modalTrigger) {
            return;
        }

        modalTrigger.addEventListener('click', function(e) {
            e.preventDefault();

            const modalId = this.dataset.modalTrigger;

            // Check if a modal library is available
            if (typeof window.openModal === 'function') {
                window.openModal(modalId);
            } else {
                // Fallback: dispatch custom event that other scripts can listen to
                const event = new CustomEvent('travelBlocksOpenModal', {
                    detail: { modalId: modalId },
                    bubbles: true,
                });
                document.dispatchEvent(event);
            }
        });
    }

    /**
     * Initialize currency selector (if present)
     * This is a placeholder for future currency conversion feature
     */
    function initCurrencySelector(block) {
        const currencySelector = block.querySelector('[data-currency-selector]');

        if (!currencySelector) {
            return;
        }

        currencySelector.addEventListener('change', function(e) {
            const selectedCurrency = e.target.value;
            const priceValue = block.querySelector('.pricing-card__price-value');
            const currencySymbol = block.querySelector('.pricing-card__currency');

            if (!priceValue) {
                return;
            }

            // Placeholder: In a real implementation, you would fetch conversion rates
            // from an API and update the price accordingly
            console.log('Currency changed to:', selectedCurrency);

            // Dispatch event for external handlers
            const event = new CustomEvent('travelBlocksCurrencyChange', {
                detail: {
                    currency: selectedCurrency,
                    priceElement: priceValue,
                    currencyElement: currencySymbol,
                },
                bubbles: true,
            });
            block.dispatchEvent(event);
        });
    }

    /**
     * Smooth scroll to booking form when clicking CTA
     * (Alternative to modal for some implementations)
     */
    function initSmoothScrollToCTA() {
        const ctaButtons = document.querySelectorAll('.pricing-card__button[href^="#"]');

        ctaButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                const href = this.getAttribute('href');

                // Only handle hash links
                if (href && href.startsWith('#')) {
                    const target = document.querySelector(href);

                    if (target) {
                        e.preventDefault();
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start',
                        });

                        // Optional: focus the target for accessibility
                        target.focus();
                    }
                }
            });
        });
    }

    // Initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initPricingCards);
    } else {
        initPricingCards();
    }

    // Re-initialize on Gutenberg block updates (editor)
    if (typeof wp !== 'undefined' && wp.data) {
        wp.data.subscribe(() => {
            initPricingCards();
        });
    }

    // Initialize smooth scroll
    initSmoothScrollToCTA();

    // Expose public API for external use
    window.TravelBlocks = window.TravelBlocks || {};
    window.TravelBlocks.PricingCard = {
        init: initPricingCards,
    };
})();
