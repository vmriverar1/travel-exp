/**
 * FAQ Accordion - Interactive JavaScript
 *
 * @package Travel\Blocks
 */

(function() {
    'use strict';

    // Wait for DOM to be ready
    document.addEventListener('DOMContentLoaded', initFAQAccordions);

    /**
     * Initialize all FAQ accordions on the page
     */
    function initFAQAccordions() {
        const accordions = document.querySelectorAll('.faq-accordion');

        accordions.forEach(function(accordion) {
            const items = accordion.querySelectorAll('[data-faq-item]');

            items.forEach(function(item) {
                const trigger = item.querySelector('[data-faq-trigger]');

                if (trigger) {
                    trigger.addEventListener('click', function() {
                        toggleAccordionItem(item);
                    });

                    // Keyboard accessibility
                    trigger.addEventListener('keydown', function(e) {
                        if (e.key === 'Enter' || e.key === ' ') {
                            e.preventDefault();
                            toggleAccordionItem(item);
                        }
                    });
                }
            });
        });
    }

    /**
     * Toggle accordion item open/closed
     */
    function toggleAccordionItem(item) {
        const trigger = item.querySelector('[data-faq-trigger]');
        const content = item.querySelector('[data-faq-content]');
        const isOpen = item.classList.contains('is-open');

        if (isOpen) {
            // Close the item
            closeAccordionItem(item, trigger, content);
        } else {
            // Open the item
            openAccordionItem(item, trigger, content);
        }
    }

    /**
     * Open accordion item
     */
    function openAccordionItem(item, trigger, content) {
        item.classList.add('is-open');
        trigger.setAttribute('aria-expanded', 'true');
        content.removeAttribute('hidden');

        // Smooth expand animation
        const contentHeight = content.scrollHeight;
        content.style.maxHeight = '0px';
        content.style.overflow = 'hidden';

        // Force reflow
        content.offsetHeight;

        // Animate
        content.style.transition = 'max-height 0.3s ease-out';
        content.style.maxHeight = contentHeight + 'px';

        // Clean up after animation
        setTimeout(function() {
            content.style.maxHeight = 'none';
            content.style.overflow = 'visible';
        }, 300);
    }

    /**
     * Close accordion item
     */
    function closeAccordionItem(item, trigger, content) {
        const contentHeight = content.scrollHeight;

        // Set current height before animating
        content.style.maxHeight = contentHeight + 'px';
        content.style.overflow = 'hidden';

        // Force reflow
        content.offsetHeight;

        // Animate to closed
        content.style.transition = 'max-height 0.3s ease-out';
        content.style.maxHeight = '0px';

        // Clean up after animation
        setTimeout(function() {
            item.classList.remove('is-open');
            trigger.setAttribute('aria-expanded', 'false');
            content.setAttribute('hidden', '');
            content.style.maxHeight = '';
            content.style.overflow = '';
            content.style.transition = '';
        }, 300);
    }

})();
