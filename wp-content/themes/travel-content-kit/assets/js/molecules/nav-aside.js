/**
 * Aside Navigation Menu
 */
(function() {
  'use strict';

  const hamburgerBtn = document.querySelector('.btn-hamburger');
  const closeBtn = document.querySelector('.btn-close');
  const asideMenu = document.getElementById('aside-menu');
  const overlay = document.querySelector('.nav-aside__overlay');
  const body = document.body;

  if (!hamburgerBtn || !closeBtn || !asideMenu || !overlay) {
    console.warn('Nav aside elements not found');
    return;
  }

  function openMenu(trigger) {
    console.log('[Aside] openMenu');
    asideMenu.classList.add('is-open');
    asideMenu.setAttribute('aria-hidden', 'false');
    asideMenu.removeAttribute('hidden');
    body.classList.add('no-scroll');
    if (trigger) trigger.setAttribute('aria-expanded', 'true');
  }

  function closeMenu(trigger) {
    console.log('[Aside] closeMenu');
    asideMenu.classList.remove('is-open');
    asideMenu.setAttribute('aria-hidden', 'true');
    asideMenu.setAttribute('hidden', '');
    body.classList.remove('no-scroll');
    if (trigger) trigger.setAttribute('aria-expanded', 'false');
  }

  function toggleMenu(trigger) {
    const open = asideMenu.classList.contains('is-open');
    if (open) closeMenu(trigger);
    else openMenu(trigger);
  }

  // Listeners
  hamburgerBtn.addEventListener('click', e => toggleMenu(e.currentTarget));
  closeBtn.addEventListener('click', () => closeMenu(hamburgerBtn));
  overlay.addEventListener('click', () => closeMenu(hamburgerBtn));

  // Global hook para el sticky
  window.asideMenuToggle = toggleMenu;

  // Estado inicial cerrado
  closeMenu(hamburgerBtn);
})();
