/**
 * CTA Banner Block JavaScript
 *
 * @package Travel\Blocks
 * @since 1.0.0
 */

(function() {
    'use strict';

    function initCTABanners() {
        const blocks = document.querySelectorAll('.cta-banner');

        blocks.forEach(block => {
            if (block.dataset.initialized === 'true') return;
            block.dataset.initialized = 'true';
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCTABanners);
    } else {
        initCTABanners();
    }

    if (typeof wp !== 'undefined' && wp.data) {
        wp.data.subscribe(() => initCTABanners());
    }

    window.TravelBlocks = window.TravelBlocks || {};
    window.TravelBlocks.CTABanner = { init: initCTABanners };
})();
