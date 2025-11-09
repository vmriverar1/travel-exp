/**
 * Sticky Side Menu - JavaScript
 * Handles sticky behavior and triggers the existing aside menu
 */

(function () {
    'use strict';

    /**
     * Initialize all sticky side menus on the page
     */
    function initStickySideMenus() {
        const menus = document.querySelectorAll('[data-sticky-menu]');

        menus.forEach(menu => {
            // ===== STICKY BEHAVIOR =====
            const wrapper = menu.closest('.wp-block-travel-sticky-side-menu');

            if (wrapper) {
                // Check if we're in editor preview mode
                const isEditorPreview = wrapper.closest('.block-editor-block-list__layout') !== null;

                // In editor, always show the menu for preview
                if (isEditorPreview) {
                    menu.classList.add('is-visible');
                    return; // Don't attach scroll listeners in editor
                }

                // Get configured offset from CSS variable
                const offsetTopValue = getComputedStyle(menu).getPropertyValue('--offset-top').trim() || '20vh';

                // Sticky behavior:
                // - The configured offset (from ACF) determines WHERE the menu appears
                // - When scrolled past that point, menu shows at top: 0
                function handleStickyBehavior() {
                    const scrollPosition = window.scrollY;

                    // Parse offset value to pixels (could be vh, px, %)
                    let offsetPixels = 0;
                    if (offsetTopValue.includes('vh')) {
                        offsetPixels = (parseFloat(offsetTopValue) / 100) * window.innerHeight;
                    } else if (offsetTopValue.includes('px')) {
                        offsetPixels = parseFloat(offsetTopValue);
                    } else if (offsetTopValue.includes('%')) {
                        // % relative to document height
                        offsetPixels = (parseFloat(offsetTopValue) / 100) * document.documentElement.scrollHeight;
                    }

                    // Check if we've scrolled past the configured offset
                    const hasReachedOffset = scrollPosition >= offsetPixels;

                    if (hasReachedOffset) {
                        // Show menu stuck at top
                        menu.classList.add('is-visible');
                    } else {
                        // Haven't reached offset yet, hide menu
                        menu.classList.remove('is-visible');
                    }
                }

                // Initial check
                handleStickyBehavior();

                // Listen to scroll with throttle for performance
                let ticking = false;
                window.addEventListener('scroll', function() {
                    if (!ticking) {
                        window.requestAnimationFrame(function() {
                            handleStickyBehavior();
                            ticking = false;
                        });
                        ticking = true;
                    }
                }, { passive: true });
            }

            // ===== HAMBURGER MENU - TRIGGER EXISTING HEADER ASIDE MENU =====
            const hamburger = menu.querySelector('[data-ssm-hamburger]');

            if (hamburger) {
                hamburger.addEventListener('click', function(e) {
                    e.preventDefault();

                    // Try to use the global toggle function first
                    if (typeof window.asideMenuToggle === 'function') {
                        window.asideMenuToggle(hamburger);
                        return;
                    }

                    // Fallback: Toggle aside menu directly
                    const asideMenu = document.getElementById('aside-menu');
                    const headerHamburger = document.querySelector('.btn-hamburger');

                    if (!asideMenu) {
                        console.warn('[Sticky Side Menu] Aside menu not found (#aside-menu)');
                        return;
                    }

                    // Check if menu is currently open
                    const isOpen = asideMenu.classList.contains('is-open');

                    if (isOpen) {
                        // Close menu
                        asideMenu.classList.remove('is-open');
                        asideMenu.setAttribute('aria-hidden', 'true');
                        asideMenu.setAttribute('hidden', '');
                        document.body.classList.remove('no-scroll');
                        if (headerHamburger) {
                            headerHamburger.setAttribute('aria-expanded', 'false');
                        }
                    } else {
                        // Open menu
                        asideMenu.classList.add('is-open');
                        asideMenu.setAttribute('aria-hidden', 'false');
                        asideMenu.removeAttribute('hidden');
                        document.body.classList.add('no-scroll');
                        if (headerHamburger) {
                            headerHamburger.setAttribute('aria-expanded', 'true');
                        }
                    }
                });
            }
        });
    }

    /**
     * Initialize on DOM ready
     */
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initStickySideMenus);
    } else {
        initStickySideMenus();
    }

    /**
     * Re-initialize on dynamic content load (for SPAs or AJAX)
     */
    window.addEventListener('load', initStickySideMenus);

})();
