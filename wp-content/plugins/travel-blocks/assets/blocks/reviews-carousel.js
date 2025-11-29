/**
 * Reviews Carousel Block JavaScript
 *
 * Additional functionality for reviews carousel
 *
 * @package Travel\Blocks
 * @since 1.0.0
 */

(function() {
    'use strict';

    /**
     * Initialize all Reviews Carousel blocks on the page
     */
    function initReviewsCarousels() {
        const blocks = document.querySelectorAll('.reviews-carousel');

        blocks.forEach(block => {
            // Skip if already initialized
            if (block.dataset.initialized === 'true') {
                return;
            }

            // Mark as initialized
            block.dataset.initialized = 'true';

            // Additional enhancements can be added here
            // Main initialization happens in template for better control
        });
    }

    // Initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initReviewsCarousels);
    } else {
        initReviewsCarousels();
    }

    // Re-initialize on Gutenberg block updates (editor)
    if (typeof wp !== 'undefined' && wp.data) {
        wp.data.subscribe(() => {
            initReviewsCarousels();
        });
    }

    // Expose public API for external use
    window.TravelBlocks = window.TravelBlocks || {};
    window.TravelBlocks.ReviewsCarousel = {
        init: initReviewsCarousels,
    };
})();
