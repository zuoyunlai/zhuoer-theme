/**
 * ZHUOER Theme - main.js (Optimized)
 */
(function () {
    'use strict';

    /* ── Scroll-triggered entrance animations ── */
    (function () {
        if (!('IntersectionObserver' in window)) {
            // Fallback: show all immediately
            document.querySelectorAll('.zhuoer-entry, .zhuoer-widget').forEach(function (el) {
                el.classList.add('is-visible');
            });
            return;
        }
        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    // 动画完成后释放 transform，让 hover 微动生效
                    entry.target.addEventListener('animationend', function () {
                        entry.target.style.animation = 'none';
                    }, { once: true });
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.08, rootMargin: '0px 0px -40px 0px' });

        document.querySelectorAll('.zhuoer-entry, .zhuoer-widget, .zhuoer-related-card').forEach(function (el) {
            observer.observe(el);
        });
    })();

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

            if (isOpen) {
                // Dropdown below header: JS inline styles, bypasses CSS cache
                nav.style.setProperty('display', 'block', 'important');
                nav.style.setProperty('position', 'absolute', 'important');
                nav.style.setProperty('top', '100%', 'important');
                nav.style.setProperty('left', '0', 'important');
                nav.style.setProperty('right', '0', 'important');
                nav.style.setProperty('width', '100%', 'important');
                nav.style.setProperty('z-index', '999', 'important');
                nav.style.setProperty('padding', '0.5rem 0', 'important');
                nav.style.setProperty('box-shadow', '0 8px 24px rgba(0,0,0,0.10)', 'important');
                nav.style.setProperty('box-sizing', 'border-box', 'important');
            } else {
                // Close: restore everything
                ['display','position','top','left','right','width','z-index','background','border-bottom','padding','box-shadow','box-sizing','margin'].forEach(function(prop) {
                    nav.style.removeProperty(prop);
                });
            }

            toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            // Swap icon: hamburger ↔ X
            toggle.innerHTML = isOpen ? ICONS.close : ICONS.menu;
        });

        // Close on link click
        nav.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', function () {
                nav.classList.remove('zhuoer-nav--open');
                toggle.setAttribute('aria-expanded', 'false');
                ['display','position','top','left','right','width','z-index','background','border-bottom','padding','box-shadow','box-sizing','margin'].forEach(function(prop) {
                    nav.style.removeProperty(prop);
                });
                toggle.innerHTML = ICONS.menu;
            });
        });

        // Close on outside click
        document.addEventListener('click', function (e) {
            if (!nav.contains(e.target) && !toggle.contains(e.target)) {
                nav.classList.remove('zhuoer-nav--open');
                toggle.setAttribute('aria-expanded', 'false');
                ['display','position','top','left','right','width','z-index','background','border-bottom','padding','box-shadow','box-sizing','margin'].forEach(function(prop) {
                    nav.style.removeProperty(prop);
                });
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
    /* ── Hero Slider ── */
    (function(){
        var track = document.querySelector('.zhuoer-hero-slider__track');
        if (!track) return;

        var slides = Array.from(track.querySelectorAll('.zhuoer-hero-slider__slide'));
        var prevBtn = document.querySelector('.zhuoer-hero-slider__arrow--prev');
        var nextBtn = document.querySelector('.zhuoer-hero-slider__arrow--next');
        var dots    = Array.from(document.querySelectorAll('.zhuoer-hero-slider__dot'));

        if (slides.length < 2) return;

        var current = 0;
        var timer   = null;
        var AUTOPLAY = 5000;

        function goTo(idx, dir) {
            if (idx === current) return;
            var prev = slides[current];
            var next = slides[idx];

            prev.classList.remove('is-active');
            prev.classList.add('is-leaving');

            next.classList.add('is-active');

            dots[current] && dots[current].classList.remove('is-active');
            dots[idx]    && dots[idx].classList.add('is-active');

            setTimeout(function(){
                prev.classList.remove('is-leaving');
            }, 400);

            current = idx;
        }

        function next() {
            goTo((current + 1) % slides.length, 1);
        }

        function prev() {
            goTo((current - 1 + slides.length) % slides.length, -1);
        }

        function startAutoplay() {
            clearInterval(timer);
            timer = setInterval(next, AUTOPLAY);
        }

        if (nextBtn) nextBtn.addEventListener('click', function(){
            next();
            startAutoplay();
        });

        if (prevBtn) prevBtn.addEventListener('click', function(){
            prev();
            startAutoplay();
        });

        dots.forEach(function(dot, i){
            dot.addEventListener('click', function(){
                goTo(i, i > current ? 1 : -1);
                startAutoplay();
            });
        });

        // Pause on hover
        var slider = document.querySelector('.zhuoer-hero-slider');
        slider.addEventListener('mouseenter', function(){ clearInterval(timer); });
        slider.addEventListener('mouseleave', startAutoplay);

        // Touch / swipe support
        var touchStartX = 0;
        slider.addEventListener('touchstart', function(e){ touchStartX = e.changedTouches[0].clientX; }, {passive:true});
        slider.addEventListener('touchend', function(e){
            var dx = e.changedTouches[0].clientX - touchStartX;
            if (Math.abs(dx) > 50) { dx > 0 ? prev() : next(); startAutoplay(); }
        }, {passive:true});

        startAutoplay();

    /* ============================================================
       PRODUCT GALLERY, TABS, QUANTITY
       ============================================================ */
    document.addEventListener('DOMContentLoaded', function() {
        // Gallery
        var mainImg = document.getElementById('zhuoer-main-img');
        var thumbs = document.querySelectorAll('.zhuoer-product-single__thumb');
        if (mainImg && thumbs.length > 0) {
            thumbs.forEach(function(thumb, idx) {
                thumb.addEventListener('click', function() {
                    var full = thumb.getAttribute('data-full');
                    if (full) mainImg.src = full;
                    thumbs.forEach(function(t, i) {
                        t.classList.toggle('is-active', i === idx);
                    });
                });
            });
            var prevBtn = document.getElementById('zhuoer-gallery-prev');
            var nextBtn = document.getElementById('zhuoer-gallery-next');
            if (prevBtn || nextBtn) {
                var current = 0;
                function updateGallery(idx) {
                    if (idx < 0) idx = thumbs.length - 1;
                    if (idx >= thumbs.length) idx = 0;
                    current = idx;
                    var full = thumbs[idx].getAttribute('data-full');
                    if (full) mainImg.src = full;
                    thumbs.forEach(function(t, i) {
                        t.classList.toggle('is-active', i === idx);
                    });
                }
                if (prevBtn) prevBtn.addEventListener('click', function() { updateGallery(current - 1); });
                if (nextBtn) nextBtn.addEventListener('click', function() { updateGallery(current + 1); });
            }
        }

        // Tabs
        var tabNav = document.querySelector('.zhuoer-product-tabs__nav');
        if (tabNav) {
            var tabs = tabNav.querySelectorAll('.zhuoer-product-tabs__tab');
            var panels = document.querySelectorAll('.zhuoer-product-tabs__panel');
            tabs.forEach(function(tab) {
                tab.addEventListener('click', function() {
                    var target = tab.getAttribute('data-tab');
                    tabs.forEach(function(t) { t.classList.remove('is-active'); t.setAttribute('aria-selected', 'false'); });
                    panels.forEach(function(p) { p.classList.remove('is-active'); });
                    tab.classList.add('is-active');
                    tab.setAttribute('aria-selected', 'true');
                    document.getElementById(target).classList.add('is-active');
                });
            });
        }

        // Quantity
        var qtyWraps = document.querySelectorAll('.quantity');
        qtyWraps.forEach(function(wrap) {
            var input = wrap.querySelector('.qty');
            var minus = wrap.querySelector('.zhuoer-qty-btn--minus');
            var plus = wrap.querySelector('.zhuoer-qty-btn--plus');
            if (!input) return;
            function updateQty(change) {
                var val = parseInt(input.value || '1') + change;
                var min = parseInt(input.getAttribute('min')) || 1;
                var max = parseInt(input.getAttribute('max')) || 9999;
                input.value = Math.max(min, Math.min(max, val));
                input.dispatchEvent(new Event('change'));
            }
            if (minus) minus.addEventListener('click', function() { updateQty(-1); });
            if (plus) plus.addEventListener('click', function() { updateQty(1); });
        });
    });

})();
    /* ── Reading Progress Bar ── */
    (function() {
        var progressBar = document.getElementById('zhuoer-reading-progress');
        if (!progressBar) return;

        function updateProgress() {
            var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            var scrollHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
            var progress = scrollHeight > 0 ? (scrollTop / scrollHeight) * 100 : 0;
            progressBar.style.width = progress + '%';
        }

        window.addEventListener('scroll', updateProgress, { passive: true });
        updateProgress();
    })();

