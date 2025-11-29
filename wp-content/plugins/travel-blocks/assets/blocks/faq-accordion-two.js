/**
 * FAQ Accordion Two Columns - Interactive Functionality
 *
 * Handles click events to expand/collapse FAQ items
 */

(function() {
  'use strict';

  /**
   * Initialize all FAQ accordions on the page
   */
  function initFAQAccordions() {
    const accordions = document.querySelectorAll('.faq-accordion-two');

    accordions.forEach(accordion => {
      const triggers = accordion.querySelectorAll('[data-faq-trigger]');

      triggers.forEach(trigger => {
        trigger.addEventListener('click', handleAccordionClick);
      });
    });
  }

  /**
   * Handle accordion item click
   * @param {Event} event - Click event
   */
  function handleAccordionClick(event) {
    event.preventDefault();

    const trigger = event.currentTarget;
    const item = trigger.closest('[data-faq-item]');
    const content = item.querySelector('[data-faq-content]');
    const accordion = item.closest('.faq-accordion-two');
    const isOpen = item.classList.contains('is-open');

    if (isOpen) {
      // Close item
      item.classList.remove('is-open');
      trigger.setAttribute('aria-expanded', 'false');
      content.setAttribute('hidden', '');
    } else {
      // Close all other items in this accordion
      const allItems = accordion.querySelectorAll('[data-faq-item]');
      allItems.forEach(otherItem => {
        if (otherItem !== item && otherItem.classList.contains('is-open')) {
          const otherTrigger = otherItem.querySelector('[data-faq-trigger]');
          const otherContent = otherItem.querySelector('[data-faq-content]');
          otherItem.classList.remove('is-open');
          otherTrigger.setAttribute('aria-expanded', 'false');
          otherContent.setAttribute('hidden', '');
        }
      });

      // Open this item
      item.classList.add('is-open');
      trigger.setAttribute('aria-expanded', 'true');
      content.removeAttribute('hidden');
    }
  }

  /**
   * Initialize on DOM ready
   */
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initFAQAccordions);
  } else {
    initFAQAccordions();
  }

  /**
   * Re-initialize when Gutenberg editor updates
   * (for block preview in editor)
   */
  if (window.acf) {
    window.acf.addAction('render_block_preview/type=faq-accordion-two', initFAQAccordions);
  }

})();
