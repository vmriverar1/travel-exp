/**
 * Nav Footer Column - Accordion (Disabled in Mobile)
 *
 * Según las especificaciones del footer mobile, el accordion debe estar
 * DESACTIVADO en mobile (todo el contenido visible).
 * El accordion solo se activa en tablet/desktop si es necesario en el futuro.
 */

(function() {
    'use strict';

    // DESACTIVADO en mobile según especificaciones
    // El contenido debe estar siempre visible en mobile
    // Solo activaríamos accordion en tablet si fuera necesario (min-width: 768px)

    // Uncomment below to enable accordion for tablet/desktop only:
    /*
    if (window.innerWidth < 768) return;

    const columns = document.querySelectorAll('.nav-footer-column--collapsible');

    columns.forEach(column => {
        const title = column.querySelector('.nav-footer-column__title');
        const content = column.querySelector('.nav-footer-column__content');

        if (!title || !content) return;

        // Toggle accordion
        const toggleAccordion = () => {
            const isExpanded = title.getAttribute('aria-expanded') === 'true';

            title.setAttribute('aria-expanded', !isExpanded);
            content.setAttribute('aria-hidden', isExpanded);
        };

        // Click event
        title.addEventListener('click', toggleAccordion);

        // Keyboard navigation
        title.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                toggleAccordion();
            }
        });
    });
    */

})();
