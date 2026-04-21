/**
 * Dandysite Jane - Header Behavior
 * Vanilla JS — no jQuery dependency.
 *
 * Reads config from dspHeader (localized via PHP):
 *   dspHeader.style          'overlay' | 'solid'
 *   dspHeader.scrollReveal   'solid' | 'transparent'
 *   dspHeader.navBreakpoint  px value (e.g. 1024)
 *   dspHeader.scrollThreshold px before hiding (e.g. 80)
 */

(function () {
    'use strict';

    const config = window.dspHeader || {
        style:           'solid',
        scrollReveal:    'solid',
        navBreakpoint:   1024,
        scrollThreshold: 80,
    };

    // ================================================================
    // STATE
    // ================================================================
    let lastScrollY     = window.scrollY;
    let rafPending      = false;
    let mobileMenuOpen  = false;
    let header          = null;
    let menuToggle      = null;
    let nav             = null;

    // ================================================================
    // INIT
    // ================================================================
    function init() {
        header     = document.querySelector('.site-header');
        menuToggle = document.querySelector('.header-hamburger');
        nav        = document.querySelector('.header-nav');

        if (!header) return;

        // Set initial nav visibility based on breakpoint
        updateNavMode();

        // Scroll behavior
        window.addEventListener('scroll', onScroll, { passive: true });

        // Resize — re-evaluate nav mode and close mobile menu if now desktop
        window.addEventListener('resize', onResize, { passive: true });

        // Hamburger toggle
        if (menuToggle) {
            menuToggle.addEventListener('click', toggleMobileMenu);
        }

        // Close mobile menu on nav link click
        if (nav) {
            nav.querySelectorAll('a').forEach(link => {
                link.addEventListener('click', closeMobileMenu);
            });
        }

        // Close mobile menu on Escape
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && mobileMenuOpen) {
                closeMobileMenu();
            }
        });

        // Run once immediately to set correct initial state
        applyScrollState();

        // Search overlay
        initSearchOverlay();
    }

    // ================================================================
    // SCROLL HANDLER — throttled via rAF
    // ================================================================
    function onScroll() {
        if (!rafPending) {
            rafPending = true;
            requestAnimationFrame(function () {
                applyScrollState();
                rafPending = false;
            });
        }
    }

    function applyScrollState() {
        const currentY   = window.scrollY;
        const threshold  = config.scrollThreshold;
        const scrollingDown = currentY > lastScrollY;

        // --- Has the user scrolled past the threshold? ---
        if (currentY > threshold) {
            header.classList.add('header--scrolled');
        } else {
            // Back near top — restore to initial state
            header.classList.remove('header--scrolled');
            header.classList.remove('header--hidden');
            header.classList.remove('header--revealed');
            lastScrollY = currentY;
            return;
        }

        // --- Hide on scroll down, reveal on scroll up ---
        if (scrollingDown) {
            // Only hide if not already hidden and menu is closed
            if (!mobileMenuOpen) {
                header.classList.add('header--hidden');
                header.classList.remove('header--revealed');
            }
        } else {
            // Scrolling up — reveal
            if (header.classList.contains('header--hidden')) {
                header.classList.remove('header--hidden');
                header.classList.add('header--revealed');

                // Apply scroll-reveal style
                if (config.scrollReveal === 'transparent') {
                    header.classList.add('header--reveal-transparent');
                    header.classList.remove('header--reveal-solid');
                } else {
                    header.classList.add('header--reveal-solid');
                    header.classList.remove('header--reveal-transparent');
                }
            }
        }

        lastScrollY = currentY;
    }

    // ================================================================
    // NAV MODE (hamburger vs full)
    // ================================================================
    function updateNavMode() {
        const isMobile = window.innerWidth <= config.navBreakpoint;
        header.classList.toggle('header--mobile-nav', isMobile);
        header.classList.toggle('header--desktop-nav', !isMobile);

        // If switching to desktop, close any open mobile menu
        if (!isMobile && mobileMenuOpen) {
            closeMobileMenu();
        }

        // Update hamburger visibility via aria
        if (menuToggle) {
            menuToggle.setAttribute('aria-hidden', String(!isMobile));
        }
    }

    function onResize() {
        updateNavMode();
    }

    // ================================================================
    // MOBILE MENU
    // ================================================================
    function toggleMobileMenu() {
        mobileMenuOpen ? closeMobileMenu() : openMobileMenu();
    }

    function openMobileMenu() {
        mobileMenuOpen = true;

        // header--hidden carries transform:translateY(-100%), and will-change:transform
        // (set in CSS) makes the header a containing block for position:fixed children.
        // Both must be neutralised before opening so the hamburger button resolves
        // to the viewport rather than the off-screen header position.
        // We only touch what's strictly needed — header--revealed is left alone.
        header.classList.remove('header--hidden');
        header.style.transform  = 'none';
        header.style.willChange = 'auto';
        header.style.transition = 'none';

        // Re-enable transitions next frame so overlay fade-in still animates
        requestAnimationFrame(function() {
            header.style.transform  = '';
            header.style.willChange = '';
            header.style.transition = '';
        });

        header.classList.add('mobile-menu-open');
        document.body.classList.add('mobile-menu-open');
        if (menuToggle) menuToggle.setAttribute('aria-expanded', 'true');
        if (nav) nav.setAttribute('aria-hidden', 'false');
    }

    function closeMobileMenu() {
        mobileMenuOpen = false;
        header.classList.remove('mobile-menu-open');
        document.body.classList.remove('mobile-menu-open');
        if (menuToggle) menuToggle.setAttribute('aria-expanded', 'false');
        if (nav) nav.setAttribute('aria-hidden', 'true');
    }

    // ================================================================
    // SEARCH OVERLAY
    // ================================================================
    function initSearchOverlay() {
        const toggleBtn = document.querySelector('.header-search-toggle');
        const overlay   = document.getElementById('search-overlay');
        const closeBtn  = overlay ? overlay.querySelector('.search-overlay__close') : null;
        const input     = overlay ? overlay.querySelector('.search-overlay__input') : null;

        if (!toggleBtn || !overlay) return;

        function openSearch() {
            overlay.hidden = false;
            // rAF so the transition fires after display change
            requestAnimationFrame(function () {
                overlay.classList.add('is-open');
            });
            document.body.classList.add('search-overlay-open');
            toggleBtn.setAttribute('aria-expanded', 'true');
            if (input) {
                input.focus();
                input.select();
            }
        }

        function closeSearch() {
            overlay.classList.remove('is-open');
            document.body.classList.remove('search-overlay-open');
            toggleBtn.setAttribute('aria-expanded', 'false');
            toggleBtn.focus();
            // Hide after transition completes
            overlay.addEventListener('transitionend', function handler() {
                if (!overlay.classList.contains('is-open')) {
                    overlay.hidden = true;
                }
                overlay.removeEventListener('transitionend', handler);
            });
        }

        // Toggle button opens
        toggleBtn.addEventListener('click', openSearch);

        // Close button
        if (closeBtn) {
            closeBtn.addEventListener('click', closeSearch);
        }

        // Click on backdrop (overlay itself, not the card) closes
        overlay.addEventListener('click', function (e) {
            if (e.target === overlay) {
                closeSearch();
            }
        });

        // Escape key closes
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && overlay.classList.contains('is-open')) {
                closeSearch();
            }
        });
    }

    // ================================================================
    // START
    // ================================================================
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();
