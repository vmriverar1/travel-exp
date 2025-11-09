/**
 * Itinerary Day-by-Day - Swiper Gallery Initialization
 *
 * Initializes Swiper sliders for day galleries
 *
 * @package Travel\Blocks
 * @since 1.0.0
 */

(function() {
    'use strict';

    /**
     * Initialize all gallery sliders in itinerary blocks
     */
    function initItineraryGalleries() {
        // Check if Swiper is loaded
        if (typeof Swiper === 'undefined') {
            console.error('Itinerary Gallery: Swiper is not loaded');
            return;
        }

        // Find all itinerary blocks
        const itineraryBlocks = document.querySelectorAll('.itinerary-day-by-day');

        itineraryBlocks.forEach(function(block) {
            const blockId = block.getAttribute('id');

            // Find all galleries in this block
            const galleries = block.querySelectorAll('.itinerary-gallery-slider');

            galleries.forEach(function(gallery, index) {
                // Skip if already initialized
                if (gallery.swiper) {
                    return;
                }

                // Create unique ID if not exists
                if (!gallery.id) {
                    gallery.id = blockId + '-gallery-' + index;
                }

                // Initialize Swiper
                new Swiper(gallery, {
                    loop: true,
                    slidesPerView: 1,
                    grabCursor: true,
                    touchEventsTarget: 'container',
                    pagination: {
                        el: gallery.querySelector('.swiper-pagination'),
                        clickable: true,
                        dynamicBullets: false,
                    },
                    autoHeight: false,
                    spaceBetween: 0,
                    speed: 400,
                    effect: 'slide',
                    // Enable keyboard navigation
                    keyboard: {
                        enabled: true,
                        onlyInViewport: true,
                    },
                    // Enable mousewheel control
                    mousewheel: {
                        forceToAxis: true,
                    },
                });

                console.log('Itinerary Gallery: Initialized slider', gallery.id);
            });
        });
    }

    /**
     * Initialize when DOM is ready and Swiper is loaded
     */
    function init() {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initItineraryGalleries);
        } else {
            initItineraryGalleries();
        }
    }

    // Wait for Swiper to be available
    if (typeof Swiper !== 'undefined') {
        init();
    } else {
        // Poll for Swiper availability (in case CDN is slow)
        let attempts = 0;
        const maxAttempts = 50; // 5 seconds max
        const checkSwiper = setInterval(function() {
            attempts++;
            if (typeof Swiper !== 'undefined') {
                clearInterval(checkSwiper);
                init();
            } else if (attempts >= maxAttempts) {
                clearInterval(checkSwiper);
                console.error('Itinerary Gallery: Swiper library failed to load after 5 seconds');
            }
        }, 100);
    }

    // Re-initialize on Gutenberg block updates (editor)
    if (typeof wp !== 'undefined' && wp.data) {
        wp.data.subscribe(function() {
            setTimeout(initItineraryGalleries, 100);
        });
    }

    // Expose public API
    window.TravelBlocks = window.TravelBlocks || {};
    window.TravelBlocks.ItineraryGallery = {
        init: initItineraryGalleries
    };
})();
