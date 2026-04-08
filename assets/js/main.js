/**
 * ZHUOER Theme - main.js (Optimized)
 */
(function () {
    'use strict';

    /* ── Icon SVGs (centralized to avoid duplication) ── */
    var ICONS = {
        menu: '<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>',
        close: '<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>',
        moon: '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>',
        sun: '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>'
    };

    /* ── Mobile menu toggle ── */
    var toggle = document.getElementById('zhuoer-menu-toggle');
    var nav    = document.getElementById('site-navigation');

    if (toggle && nav) {
        toggle.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            var isOpen = nav.classList.toggle('zhuoer-nav--open');
            toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            toggle.innerHTML = isOpen ? ICONS.close : ICONS.menu;
        });

        // Close on link click
        nav.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', function () {
                nav.classList.remove('zhuoer-nav--open');
                toggle.setAttribute('aria-expanded', 'false');
                toggle.innerHTML = ICONS.menu;
            });
        });

        // Close on outside click
        document.addEventListener('click', function (e) {
            if (!nav.contains(e.target) && !toggle.contains(e.target)) {
                nav.classList.remove('zhuoer-nav--open');
                toggle.setAttribute('aria-expanded', 'false');
                toggle.innerHTML = ICONS.menu;
            }
        });
    }

    /* ── Dark mode toggle ── */
    var themeToggle = document.getElementById('zhuoer-theme-toggle');
    if (themeToggle) {
        var html = document.documentElement;

        if (localStorage.getItem('zhuoer-theme') === 'dark') {
            html.setAttribute('data-theme', 'dark');
            themeToggle.setAttribute('aria-label', '切换到亮色模式');
            themeToggle.innerHTML = ICONS.sun;
        } else {
            themeToggle.setAttribute('aria-label', '切换到暗色模式');
            themeToggle.innerHTML = ICONS.moon;
        }

        themeToggle.addEventListener('click', function () {
            var isDark = html.getAttribute('data-theme') === 'dark';
            if (isDark) {
                html.removeAttribute('data-theme');
                localStorage.setItem('zhuoer-theme', 'light');
                themeToggle.setAttribute('aria-label', '切换到暗色模式');
                themeToggle.innerHTML = ICONS.moon;
            } else {
                html.setAttribute('data-theme', 'dark');
                localStorage.setItem('zhuoer-theme', 'dark');
                themeToggle.setAttribute('aria-label', '切换到亮色模式');
                themeToggle.innerHTML = ICONS.sun;
            }
        });
    }

        /* ── Desktop search: icon → expand left ── */
    (function () {
        var container  = document.getElementById('zhuoer-desktop-search');
        var toggle     = document.getElementById('zhuoer-search-toggle');
        var closeBtn   = document.getElementById('zhuoer-desktop-search__close');
        var form       = container ? container.querySelector('.zhuoer-desktop-search__form') : null;
        var input      = form ? form.querySelector('.zhuoer-desktop-search__input') : null;

        if (!container || !toggle) return;

        function openSearch() {
            container.classList.add('is-open');
            toggle.setAttribute('aria-expanded', 'true');
            if (input) {
                input.style.display = 'block';
                setTimeout(function () { input.focus(); }, 50);
            }
        }

        function closeSearch() {
            container.classList.remove('is-open');
            toggle.setAttribute('aria-expanded', 'false');
            if (input) {
                input.style.display = '';
                if (input.value) input.value = '';
            }
        }

        toggle.addEventListener('click', function () {
            if (container.classList.contains('is-open')) {
                closeSearch();
            } else {
                openSearch();
            }
        });

        if (closeBtn) {
            closeBtn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                closeSearch();
            });
        }

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && container.classList.contains('is-open')) {
                closeSearch();
            }
        });
    })();

    /* ── Mobile search bar popup ── */
    (function () {
        var toggle   = document.getElementById('zhuoer-search-toggle-mobile') || document.querySelector('.zhuoer-search-toggle');
        var bar      = document.getElementById('zhuoer-search-bar');
        var closeBtn = document.getElementById('zhuoer-search-form__close');

        if (!bar) return;

        // Mobile search toggle via dedicated search button
        var mobileToggle = document.getElementById('zhuoer-search-toggle-mobile');
        if (mobileToggle) {
            mobileToggle.addEventListener('click', function () {
                bar.classList.toggle('zhuoer-search-bar--open');
                var inp = bar.querySelector('.zhuoer-mobile-search__input');
                if (bar.classList.contains('zhuoer-search-bar--open') && inp) {
                    setTimeout(function () { inp.focus(); }, 100);
                }
            });
        }

        if (closeBtn) {
            closeBtn.addEventListener('click', function () {
                bar.classList.remove('zhuoer-search-bar--open');
            });
        }

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && bar.classList.contains('zhuoer-search-bar--open')) {
                bar.classList.remove('zhuoer-search-bar--open');
            }
        });
    })();

})();