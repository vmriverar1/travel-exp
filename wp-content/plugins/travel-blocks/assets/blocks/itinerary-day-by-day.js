/**
 * Itinerary Day-by-Day Block JavaScript
 *
 * Handles accordion functionality for expandable day items
 *
 * @package Travel\Blocks
 * @since 1.0.0
 */

(function() {
    'use strict';

    /**
     * Initialize all Itinerary Day-by-Day blocks on the page
     */
    function initItineraryBlocks() {
        const blocks = document.querySelectorAll('.itinerary-day-by-day');

        blocks.forEach(block => {
            // Skip if already initialized
            if (block.dataset.initialized === 'true') {
                return;
            }

            // Mark as initialized
            block.dataset.initialized = 'true';

            // Get default state
            const defaultState = block.dataset.defaultState || 'first_open';

            // Initialize accordion items
            const items = block.querySelectorAll('.itinerary-day__item');
            items.forEach((item, index) => {
                initAccordionItem(item, index, defaultState);
            });
        });
    }

    /**
     * Initialize individual accordion item
     */
    function initAccordionItem(item, index, defaultState) {
        const header = item.querySelector('.itinerary-day__header');
        const content = item.querySelector('.itinerary-day__content');

        if (!header || !content) {
            return;
        }

        // Set initial state based on defaultState
        let isOpen = false;
        if (defaultState === 'all_open') {
            isOpen = true;
        } else if (defaultState === 'first_open' && index === 0) {
            isOpen = true;
        }

        // Apply initial state
        setAccordionState(item, header, content, isOpen);

        // Add click event listener
        header.addEventListener('click', function(e) {
            e.preventDefault();

            const currentlyOpen = item.classList.contains('itinerary-day__item--open');
            toggleAccordion(item, header, content, !currentlyOpen);
        });

        // Keyboard accessibility
        header.addEventListener('keydown', function(e) {
            // Enter or Space key
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                header.click();
            }
        });
    }

    /**
     * Toggle accordion open/close
     */
    function toggleAccordion(item, header, content, shouldOpen) {
        setAccordionState(item, header, content, shouldOpen);

        // Smooth scroll into view if opening
        if (shouldOpen) {
            setTimeout(() => {
                const rect = item.getBoundingClientRect();
                const isVisible = rect.top >= 0 && rect.bottom <= window.innerHeight;

                if (!isVisible) {
                    item.scrollIntoView({
                        behavior: 'smooth',
                        block: 'nearest',
                    });
                }
            }, 100);
        }
    }

    /**
     * Set accordion state
     */
    function setAccordionState(item, header, content, isOpen) {
        if (isOpen) {
            item.classList.add('itinerary-day__item--open');
            header.setAttribute('aria-expanded', 'true');
            content.removeAttribute('hidden');
        } else {
            item.classList.remove('itinerary-day__item--open');
            header.setAttribute('aria-expanded', 'false');
            content.setAttribute('hidden', '');
        }
    }

    /**
     * Expand all accordion items
     */
    function expandAll(blockId) {
        const block = document.getElementById(blockId);
        if (!block) return;

        const items = block.querySelectorAll('.itinerary-day__item');
        items.forEach(item => {
            const header = item.querySelector('.itinerary-day__header');
            const content = item.querySelector('.itinerary-day__content');
            if (header && content) {
                setAccordionState(item, header, content, true);
            }
        });
    }

    /**
     * Collapse all accordion items
     */
    function collapseAll(blockId) {
        const block = document.getElementById(blockId);
        if (!block) return;

        const items = block.querySelectorAll('.itinerary-day__item');
        items.forEach(item => {
            const header = item.querySelector('.itinerary-day__header');
            const content = item.querySelector('.itinerary-day__content');
            if (header && content) {
                setAccordionState(item, header, content, false);
            }
        });
    }

    /**
     * Navigate to specific day and open it
     */
    function navigateToDay(blockId, dayIndex) {
        const block = document.getElementById(blockId);
        if (!block) return;

        const items = block.querySelectorAll('.itinerary-day__item');
        const targetItem = items[dayIndex];

        if (!targetItem) return;

        // Close all items
        items.forEach(item => {
            const header = item.querySelector('.itinerary-day__header');
            const content = item.querySelector('.itinerary-day__content');
            if (header && content) {
                setAccordionState(item, header, content, false);
            }
        });

        // Open target item
        const targetHeader = targetItem.querySelector('.itinerary-day__header');
        const targetContent = targetItem.querySelector('.itinerary-day__content');
        if (targetHeader && targetContent) {
            setAccordionState(targetItem, targetHeader, targetContent, true);

            // Scroll into view
            setTimeout(() => {
                targetItem.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start',
                });
            }, 100);
        }
    }

    /**
     * Print-friendly view: expand all before printing
     */
    function handlePrint() {
        window.addEventListener('beforeprint', function() {
            const blocks = document.querySelectorAll('.itinerary-day-by-day');
            blocks.forEach(block => {
                const items = block.querySelectorAll('.itinerary-day__item');
                items.forEach(item => {
                    const header = item.querySelector('.itinerary-day__header');
                    const content = item.querySelector('.itinerary-day__content');
                    if (header && content) {
                        setAccordionState(item, header, content, true);
                    }
                });
            });
        });
    }

    // Initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initItineraryBlocks);
    } else {
        initItineraryBlocks();
    }

    // Re-initialize on Gutenberg block updates (editor)
    if (typeof wp !== 'undefined' && wp.data) {
        wp.data.subscribe(() => {
            initItineraryBlocks();
        });
    }

    // Handle print
    handlePrint();

    // Expose public API for external use
    window.TravelBlocks = window.TravelBlocks || {};
    window.TravelBlocks.Itinerary = {
        init: initItineraryBlocks,
        expandAll: expandAll,
        collapseAll: collapseAll,
        navigateToDay: navigateToDay,
    };
})();
