/**
 * Endorsements Carousel — Dandysite Victoria
 *
 * Cycles endorsement cards one at a time with a crossfade.
 * Only activates on .endorsements-grid--carousel (set via Homepage Settings).
 *
 * - No dependencies, ~1.5KB
 * - Auto-advances (interval from data-interval attr, default 7000ms)
 * - Pauses on hover and keyboard focus
 * - Dot navigation, keyboard accessible
 * - Respects prefers-reduced-motion (dots still work, no auto-advance)
 * - No-JS fallback: cards display as a simple stacked list (CSS gates
 *   the carousel styles behind the .is-ready class added here)
 */
(function () {
    'use strict';

    document.querySelectorAll('.endorsements-grid--carousel').forEach(initCarousel);

    function initCarousel(grid) {
        var cards = Array.prototype.slice.call(grid.querySelectorAll('.endorsement-card'));
        if (cards.length < 2) return;

        var current = 0;
        var interval = parseInt(grid.getAttribute('data-interval'), 10) || 7000;
        var reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        var timer = null;

        // Build dot navigation
        var dots = document.createElement('div');
        dots.className = 'endorsements-dots';
        dots.setAttribute('role', 'tablist');
        dots.setAttribute('aria-label', 'Endorsements');

        cards.forEach(function (card, i) {
            var dot = document.createElement('button');
            dot.type = 'button';
            dot.className = 'endorsements-dot';
            dot.setAttribute('aria-label', 'Show endorsement ' + (i + 1) + ' of ' + cards.length);
            dot.addEventListener('click', function () {
                goTo(i);
                restart();
            });
            dots.appendChild(dot);
        });
        grid.parentNode.insertBefore(dots, grid.nextSibling);
        var dotEls = dots.children;

        function goTo(i) {
            cards[current].classList.remove('is-active');
            dotEls[current].classList.remove('is-active');
            dotEls[current].removeAttribute('aria-current');
            current = i;
            cards[current].classList.add('is-active');
            dotEls[current].classList.add('is-active');
            dotEls[current].setAttribute('aria-current', 'true');
        }

        function next() {
            goTo((current + 1) % cards.length);
        }

        function start() {
            if (reducedMotion || timer) return;
            timer = setInterval(next, interval);
        }

        function stop() {
            clearInterval(timer);
            timer = null;
        }

        function restart() {
            stop();
            start();
        }

        // Pause while the visitor is reading / interacting
        grid.addEventListener('mouseenter', stop);
        grid.addEventListener('mouseleave', start);
        grid.addEventListener('focusin', stop);
        grid.addEventListener('focusout', start);
        dots.addEventListener('mouseenter', stop);
        dots.addEventListener('mouseleave', start);

        // Activate: CSS carousel styles are gated behind .is-ready
        grid.classList.add('is-ready');
        cards[0].classList.add('is-active');
        dotEls[0].classList.add('is-active');
        dotEls[0].setAttribute('aria-current', 'true');
        start();
    }
})();
