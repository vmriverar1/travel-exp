/**
 * Product Gallery Hero Block JavaScript
 *
 * Handles Swiper carousel and GLightbox initialization
 * Note: Main initialization is in template for better compatibility
 *
 * @package Travel\Blocks
 * @since 1.0.0
 */

(function() {
    'use strict';

    /**
     * Initialize all Product Gallery Hero blocks on the page
     */
    function initProductGalleryHero() {
        const blocks = document.querySelectorAll('.product-gallery-hero');

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
        document.addEventListener('DOMContentLoaded', initProductGalleryHero);
    } else {
        initProductGalleryHero();
    }

    // Re-initialize on Gutenberg block updates (editor)
    if (typeof wp !== 'undefined' && wp.data) {
        wp.data.subscribe(() => {
            initProductGalleryHero();
        });
    }
})();
