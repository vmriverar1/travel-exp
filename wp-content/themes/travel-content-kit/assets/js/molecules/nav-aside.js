/**
 * Aside Navigation Menu
 * Handles open/close behavior, overlay, focus trap, and accessibility
 */

(function() {
    'use strict';

    // Elements
    const hamburgerBtn = document.querySelector('.btn-hamburger');
    const closeBtn = document.querySelector('.btn-close');
    const asideMenu = document.getElementById('aside-menu');
    const overlay = document.querySelector('.nav-aside__overlay');
    const body = document.body;

    // Early return if elements don't exist
    if (!hamburgerBtn || !closeBtn || !asideMenu || !overlay) {
        console.warn('Nav aside elements not found');
        return;
    }

    // State
    let isOpen = false;
    let focusableElements = [];
    let firstFocusable = null;
    let lastFocusable = null;
    let triggerElement = null; // Track which element opened the menu

    /**
     * Get all focusable elements within aside menu
     */
    function updateFocusableElements() {
        const selector = 'a[href], button:not([disabled]), textarea, input[type="text"], input[type="radio"], input[type="checkbox"], select';
        focusableElements = Array.from(asideMenu.querySelectorAll(selector)).filter(el => {
            return el.offsetWidth > 0 || el.offsetHeight > 0 || el === document.activeElement;
        });
        firstFocusable = focusableElements[0];
        lastFocusable = focusableElements[focusableElements.length - 1];
    }

    /**
     * Open aside menu
     * @param {HTMLElement} trigger - The element that triggered the menu opening
     */
    function openMenu(trigger) {
        console.log('[Nav Aside] openMenu called, isOpen:', isOpen);
        if (isOpen) {
            console.log('[Nav Aside] Menu already open, returning');
            return;
        }

        // Store which element opened the menu
        triggerElement = trigger || hamburgerBtn;
        console.log('[Nav Aside] Setting triggerElement:', triggerElement);

        // Use class instead of hidden attribute for better CSS control
        console.log('[Nav Aside] Adding is-open class');
        asideMenu.classList.add('is-open');
        asideMenu.setAttribute('aria-hidden', 'false');
        asideMenu.removeAttribute('hidden');

        hamburgerBtn.setAttribute('aria-expanded', 'true');
        body.classList.add('no-scroll');
        isOpen = true;

        console.log('[Nav Aside] Menu should now be visible. Classes:', asideMenu.className);

        // Update focusable elements
        updateFocusableElements();

        // DON'T auto-focus the close button - it causes issues
        // Users can tab to it naturally

        // Trap focus
        document.addEventListener('keydown', handleFocusTrap);
    }

    /**
     * Close aside menu
     */
    function closeMenu() {
        console.log('[Nav Aside] closeMenu called, isOpen:', isOpen);
        if (!isOpen) {
            console.log('[Nav Aside] Menu already closed, returning');
            return;
        }

        // Use class instead of hidden attribute
        console.log('[Nav Aside] Removing is-open class');
        asideMenu.classList.remove('is-open');
        asideMenu.setAttribute('aria-hidden', 'true');
        asideMenu.setAttribute('hidden', '');

        hamburgerBtn.setAttribute('aria-expanded', 'false');
        body.classList.remove('no-scroll');
        isOpen = false;

        // Remove focus trap listener first
        document.removeEventListener('keydown', handleFocusTrap);

        // Return focus to the element that opened the menu (with preventScroll to avoid jump)
        setTimeout(() => {
            if (triggerElement && typeof triggerElement.focus === 'function') {
                triggerElement.focus({ preventScroll: true });
            }
            // Reset trigger element
            triggerElement = null;
        }, 50);
    }

    /**
     * Toggle menu state
     * @param {HTMLElement} trigger - The element that triggered the toggle
     */
    function toggleMenu(trigger) {
        console.log('[Nav Aside] toggleMenu called, isOpen:', isOpen, 'trigger:', trigger);
        if (isOpen) {
            console.log('[Nav Aside] Closing menu');
            closeMenu();
        } else {
            console.log('[Nav Aside] Opening menu');
            openMenu(trigger);
        }
    }

    /**
     * Handle focus trap inside menu
     */
    function handleFocusTrap(e) {
        if (e.key !== 'Tab' || !isOpen) return;

        if (e.shiftKey) {
            // Shift + Tab (backward)
            if (document.activeElement === firstFocusable) {
                e.preventDefault();
                lastFocusable.focus();
            }
        } else {
            // Tab (forward)
            if (document.activeElement === lastFocusable) {
                e.preventDefault();
                firstFocusable.focus();
            }
        }
    }

    /**
     * Handle Escape key
     */
    function handleEscapeKey(e) {
        if (e.key === 'Escape' && isOpen) {
            closeMenu();
        }
    }

    /**
     * Handle window resize
     * Close menu automatically on desktop
     */
    let resizeTimeout;
    function handleResize() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(function() {
            if (window.innerWidth >= 1024 && isOpen) {
                closeMenu();
            }
        }, 250);
    }

    /**
     * Prevent scroll when menu is open (iOS fix)
     */
    function preventScroll(e) {
        if (isOpen && !asideMenu.contains(e.target)) {
            e.preventDefault();
        }
    }

    // Event Listeners
    hamburgerBtn.addEventListener('click', function(e) {
        toggleMenu(e.currentTarget);
    });
    closeBtn.addEventListener('click', closeMenu);
    overlay.addEventListener('click', closeMenu);
    document.addEventListener('keydown', handleEscapeKey);
    window.addEventListener('resize', handleResize);

    // Prevent scroll on body when menu is open (mobile)
    document.addEventListener('touchmove', preventScroll, { passive: false });

    // Ensure menu is closed on page load
    asideMenu.classList.remove('is-open');
    asideMenu.setAttribute('hidden', '');
    hamburgerBtn.setAttribute('aria-expanded', 'false');
    asideMenu.setAttribute('aria-hidden', 'true');

    // Expose toggleMenu function globally for external use (e.g., sticky side menu)
    window.asideMenuToggle = toggleMenu;

})();
