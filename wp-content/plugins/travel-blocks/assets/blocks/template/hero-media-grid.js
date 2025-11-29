/**

 * Hero Media Grid JavaScript

 *

 * Initializes Swiper carousel and GLightbox for gallery

 *

 * @package Travel\Blocks

 */



(function() {

    'use strict';



    /**

     * Initialize Hero Media Grid block

     */

    function initHeroMediaGrid() {

        const block = document.querySelector('.hero-media-grid');



        // Exit if block not found or already initialized

        if (!block || block.dataset.initialized === 'true') {

            return;

        }



        // Wait for Swiper and GLightbox to be available

        if (typeof Swiper === 'undefined' || typeof GLightbox === 'undefined') {

            setTimeout(initHeroMediaGrid, 100);

            return;

        }



        // Mark as initialized

        block.dataset.initialized = 'true';



        const swiperEl = block.querySelector('.swiper');



        if (!swiperEl) {

            return;

        }



        // Initialize Swiper carousel with fade effect

        const swiper = new Swiper(swiperEl, {

            loop: true,

            effect: 'fade',

            fadeEffect: {

                crossFade: true

            },

            speed: 600,

            autoplay: {

                delay: 5000,

                disableOnInteraction: false,

            },

            lazy: {

                loadPrevNext: true,

            },

        });



        // Initialize GLightbox for gallery

        const lightbox = GLightbox({

            selector: '.glightbox',

            touchNavigation: true,

            loop: true,

            autoplayVideos: false,

        });



        // Attach View Photos button event

        const viewButton = block.querySelector('[data-gallery-trigger]');

        if (viewButton) {

            viewButton.addEventListener('click', function() {

                const firstLink = block.querySelector('.glightbox');

                if (firstLink) {

                    firstLink.click();

                }

            });

        }

    }



    // Initialize when DOM is ready

    if (document.readyState === 'loading') {

        document.addEventListener('DOMContentLoaded', initHeroMediaGrid);

    } else {

        initHeroMediaGrid();

    }

})();

