/**
 * Inclusions & Exclusions Block JavaScript
 *
 * Handles accordion functionality for mobile-friendly layouts
 *
 * @package Travel\Blocks
 * @since 1.0.0
 */

(function() {
    'use strict';

    /**
     * Initialize all Inclusions & Exclusions blocks on the page
     */
    function initInclusionsBlocks() {
        const blocks = document.querySelectorAll('.inclusions-exclusions');

        blocks.forEach(block => {
            // Skip if already initialized
            if (block.dataset.initialized === 'true') {
                return;
            }

            // Mark as initialized
            block.dataset.initialized = 'true';

            // Initialize accordion (only for accordion layout)
            if (block.classList.contains('inclusions-exclusions--accordion')) {
                initAccordion(block);
            }
        });
    }

    /**
     * Initialize accordion functionality
     */
    function initAccordion(block) {
        const accordionItems = block.querySelectorAll('.inclusions-exclusions__accordion-item');

        accordionItems.forEach(item => {
            const header = item.querySelector('.inclusions-exclusions__accordion-header');
            const content = item.querySelector('.inclusions-exclusions__accordion-content');

            if (!header || !content) {
                return;
            }

            // Set initial state
            const isOpen = header.getAttribute('aria-expanded') === 'true';
            setAccordionState(item, header, content, isOpen);

            // Add click event listener
            header.addEventListener('click', function(e) {
                e.preventDefault();

                const currentlyOpen = item.classList.contains('inclusions-exclusions__accordion-item--open');
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
        });
    }

    /**
     * Toggle accordion open/close
     */
    function toggleAccordion(item, header, content, shouldOpen) {
        setAccordionState(item, header, content, shouldOpen);
    }

    /**
     * Set accordion state
     */
    function setAccordionState(item, header, content, isOpen) {
        if (isOpen) {
            item.classList.add('inclusions-exclusions__accordion-item--open');
            header.setAttribute('aria-expanded', 'true');
            content.removeAttribute('hidden');
        } else {
            item.classList.remove('inclusions-exclusions__accordion-item--open');
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

        const items = block.querySelectorAll('.inclusions-exclusions__accordion-item');
        items.forEach(item => {
            const header = item.querySelector('.inclusions-exclusions__accordion-header');
            const content = item.querySelector('.inclusions-exclusions__accordion-content');
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

        const items = block.querySelectorAll('.inclusions-exclusions__accordion-item');
        items.forEach(item => {
            const header = item.querySelector('.inclusions-exclusions__accordion-header');
            const content = item.querySelector('.inclusions-exclusions__accordion-content');
            if (header && content) {
                setAccordionState(item, header, content, false);
            }
        });
    }

    /**
     * Print-friendly view: expand all before printing
     */
    function handlePrint() {
        window.addEventListener('beforeprint', function() {
            const blocks = document.querySelectorAll('.inclusions-exclusions--accordion');
            blocks.forEach(block => {
                const items = block.querySelectorAll('.inclusions-exclusions__accordion-item');
                items.forEach(item => {
                    const header = item.querySelector('.inclusions-exclusions__accordion-header');
                    const content = item.querySelector('.inclusions-exclusions__accordion-content');
                    if (header && content) {
                        setAccordionState(item, header, content, true);
                    }
                });
            });
        });
    }

    // Initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initInclusionsBlocks);
    } else {
        initInclusionsBlocks();
    }

    // Re-initialize on Gutenberg block updates (editor)
    if (typeof wp !== 'undefined' && wp.data) {
        wp.data.subscribe(() => {
            initInclusionsBlocks();
        });
    }

    // Handle print
    handlePrint();

    // Expose public API for external use
    window.TravelBlocks = window.TravelBlocks || {};
    window.TravelBlocks.InclusionsExclusions = {
        init: initInclusionsBlocks,
        expandAll: expandAll,
        collapseAll: collapseAll,
    };
})();
