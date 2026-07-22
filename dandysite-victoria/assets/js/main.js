/**
 * Dandysite Portfolio - Main JavaScript
 * Minimal, modern JavaScript for enhanced functionality
 */

(function() {
    'use strict';

    // DOM ready function
    function ready(fn) {
        if (document.readyState !== 'loading') {
            fn();
        } else {
            document.addEventListener('DOMContentLoaded', fn);
        }
    }

    // Smooth scrolling for anchor links
    function initSmoothScrolling() {
        const links = document.querySelectorAll('a[href^="#"]');
        
        links.forEach(link => {
            link.addEventListener('click', function(e) {
                const targetId = this.getAttribute('href');
                const targetElement = document.querySelector(targetId);
                
                if (targetElement) {
                    e.preventDefault();
                    targetElement.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    }

    // Lazy loading for images (modern browsers)
    function initLazyLoading() {
        if ('IntersectionObserver' in window) {
            const images = document.querySelectorAll('img[loading="lazy"]');
            
            images.forEach(img => {
                if (img.complete) {
                    img.classList.add('loaded');
                } else {
                    img.addEventListener('load', () => {
                        img.classList.add('loaded');
                    });
                }
            });
        }
    }

    // Project filter functionality (if using AJAX)
    function initProjectFilters() {
        const filterButtons = document.querySelectorAll('.filter-btn');
        
        filterButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                // Remove active class from all buttons
                filterButtons.forEach(btn => btn.classList.remove('active'));
                // Add active class to clicked button
                this.classList.add('active');
                
                // Here you could add AJAX functionality if needed
                // For now, we're using standard WordPress navigation
            });
        });
    }

    // Add scroll-based header styling
    function initScrollHeader() {
        const header = document.querySelector('.site-header');
        let lastScrollY = window.scrollY;
        
        if (header) {
            window.addEventListener('scroll', () => {
                const currentScrollY = window.scrollY;
                
                if (currentScrollY > 100) {
                    header.classList.add('scrolled');
                } else {
                    header.classList.remove('scrolled');
                }
                
                // Hide/show header on scroll (optional)
                if (currentScrollY > lastScrollY && currentScrollY > 200) {
                    header.classList.add('header-hidden');
                } else {
                    header.classList.remove('header-hidden');
                }
                
                lastScrollY = currentScrollY;
            });
        }
    }

    // Mailto copy-to-clipboard fallback.
    // mailto: links do nothing — with no visible error — if the visitor's
    // browser/OS has no mail client configured. Any link/button with a
    // data-mailto-copy attribute (set in PHP wherever a URL is a mailto:
    // link) gets its address copied to the clipboard on click, as a
    // silent backup. This does NOT prevent the mailto: navigation —
    // both happen, so users with mail configured see no change.
    function initMailtoCopy() {
        const links = document.querySelectorAll('[data-mailto-copy]');
        if (!links.length) return;

        let toast = null;
        let toastTimer = null;

        function showToast(message) {
            if (!toast) {
                toast = document.createElement('div');
                toast.className = 'dsp-toast';
                toast.setAttribute('role', 'status');
                toast.setAttribute('aria-live', 'polite');
                document.body.appendChild(toast);
            }
            toast.textContent = message;
            toast.classList.add('is-visible');

            clearTimeout(toastTimer);
            toastTimer = setTimeout(() => {
                toast.classList.remove('is-visible');
            }, 2500);
        }

        function fallbackCopy(text) {
            const temp = document.createElement('textarea');
            temp.value = text;
            temp.setAttribute('readonly', '');
            temp.style.position = 'absolute';
            temp.style.left = '-9999px';
            document.body.appendChild(temp);
            temp.select();
            try {
                document.execCommand('copy');
            } catch (err) {
                // Nothing more we can do here — the address is still
                // visible via the title attribute on hover.
            }
            document.body.removeChild(temp);
        }

        function copyText(text) {
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(text).catch(() => fallbackCopy(text));
            } else {
                fallbackCopy(text);
            }
        }

        links.forEach(link => {
            link.addEventListener('click', function() {
                const email = this.getAttribute('data-mailto-copy');
                if (!email) return;
                copyText(email);
                showToast('Copied ' + email + ' to clipboard');
            });
        });
    }

    // Scroll-triggered reveal for homepage sections + news/issue cards.
    // Progressive enhancement: .reveal is ONLY ever added here, so a
    // page looks completely normal with JS disabled or without
    // IntersectionObserver support — nothing is hidden that this
    // script can't also un-hide. Endorsement cards are deliberately
    // excluded: the carousel (endorsements.js) already crossfades
    // them and this would fight that.
    function initScrollReveal() {
        if (!('IntersectionObserver' in window)) return;

        const targets = document.querySelectorAll('section[class^="section-"], .news-card, .issue-card');
        if (!targets.length) return;

        const observer = new IntersectionObserver(function(entries, obs) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    obs.unobserve(entry.target);
                }
            });
        }, { threshold: 0.15, rootMargin: '0px 0px -60px 0px' });

        targets.forEach(el => {
            el.classList.add('reveal');
            observer.observe(el);
        });
    }

    // Initialize all functions
    ready(function() {
        initSmoothScrolling();
        initLazyLoading();
        initProjectFilters();
        initMailtoCopy();
        initScrollReveal();
        // initScrollHeader() is superseded by header.js — do not call here
        
        // Add loaded class to body for CSS animations
        document.body.classList.add('loaded');
    });

})();
