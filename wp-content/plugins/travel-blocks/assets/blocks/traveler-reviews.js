/**
 * Traveler Reviews - JavaScript
 *
 * Handles platform filtering and pagination
 *
 * @package Travel\Blocks
 * @since 1.0.0
 */

(function() {
    'use strict';

    // Initialize all traveler reviews blocks
    function initTravelerReviews() {
        const blocks = document.querySelectorAll('.traveler-reviews');

        blocks.forEach(block => {
            initFilters(block);
            initPagination(block);
        });
    }

    /**
     * Initialize platform filters
     */
    function initFilters(block) {
        const filterButtons = block.querySelectorAll('.traveler-reviews__filter-button');
        if (!filterButtons.length) return;

        filterButtons.forEach(button => {
            button.addEventListener('click', function() {
                const platform = this.dataset.platform;

                // Update active button
                filterButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');

                // Filter cards
                filterCards(block, platform);
            });
        });
    }

    /**
     * Filter cards by platform
     */
    function filterCards(block, platform) {
        const cards = block.querySelectorAll('.traveler-reviews__card');
        const noResults = block.querySelector('.traveler-reviews__no-results');
        const showMoreButton = block.querySelector('.traveler-reviews__show-more');
        const reviewsPerPage = parseInt(block.dataset.reviewsPerPage) || 9;

        let visibleCount = 0;
        let matchingCount = 0;

        cards.forEach((card, index) => {
            const cardPlatform = card.dataset.platform;
            const matches = platform === 'all' || cardPlatform === platform;

            if (matches) {
                matchingCount++;
                // Show only first reviewsPerPage matching cards
                if (visibleCount < reviewsPerPage) {
                    card.classList.remove('hidden');
                    visibleCount++;
                } else {
                    card.classList.add('hidden');
                }
            } else {
                card.classList.add('hidden');
            }
        });

        // Update "Show more" button visibility
        if (showMoreButton) {
            if (matchingCount > reviewsPerPage) {
                showMoreButton.classList.remove('hidden');
                updateShowingCount(block, visibleCount, matchingCount);
            } else {
                showMoreButton.classList.add('hidden');
            }
        }

        // Show/hide no results message
        if (noResults) {
            noResults.style.display = matchingCount === 0 ? 'block' : 'none';
        }

        // Hide grid if no results
        const grid = block.querySelector('.traveler-reviews__grid');
        if (grid) {
            grid.style.display = matchingCount === 0 ? 'none' : 'grid';
        }
    }

    /**
     * Initialize pagination
     */
    function initPagination(block) {
        const showMoreButton = block.querySelector('.traveler-reviews__show-more');
        if (!showMoreButton) return;

        showMoreButton.addEventListener('click', function() {
            showMoreReviews(block);
        });
    }

    /**
     * Show more reviews
     */
    function showMoreReviews(block) {
        const cards = block.querySelectorAll('.traveler-reviews__card');
        const activeFilter = block.querySelector('.traveler-reviews__filter-button.active');
        const platform = activeFilter ? activeFilter.dataset.platform : 'all';
        const reviewsPerPage = parseInt(block.dataset.reviewsPerPage) || 9;

        let visibleCount = 0;
        let hiddenCount = 0;
        let totalMatching = 0;

        // Count current state
        cards.forEach(card => {
            const cardPlatform = card.dataset.platform;
            const matches = platform === 'all' || cardPlatform === platform;

            if (matches) {
                totalMatching++;
                if (!card.classList.contains('hidden')) {
                    visibleCount++;
                } else {
                    hiddenCount++;
                }
            }
        });

        // Show next batch
        let shown = 0;
        cards.forEach(card => {
            if (shown >= reviewsPerPage) return;

            const cardPlatform = card.dataset.platform;
            const matches = platform === 'all' || cardPlatform === platform;

            if (matches && card.classList.contains('hidden')) {
                card.classList.remove('hidden');
                shown++;
                visibleCount++;
            }
        });

        // Update showing count
        updateShowingCount(block, visibleCount, totalMatching);

        // Hide button if all shown
        if (visibleCount >= totalMatching) {
            const showMoreButton = block.querySelector('.traveler-reviews__show-more');
            if (showMoreButton) {
                showMoreButton.classList.add('hidden');
            }
        }
    }

    /**
     * Update "showing X of Y" text
     */
    function updateShowingCount(block, visible, total) {
        const showingElement = block.querySelector('.traveler-reviews__showing');
        if (!showingElement) return;

        // Get translated string template
        const template = showingElement.textContent.includes('Showing') ?
            'Showing %d of %d reviews' :
            'Mostrando %d de %d reseñas';

        showingElement.textContent = template
            .replace('%d', visible)
            .replace('%d', total);
    }

    // Initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initTravelerReviews);
    } else {
        initTravelerReviews();
    }

})();
