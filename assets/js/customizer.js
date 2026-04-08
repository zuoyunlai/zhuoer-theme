/**
 * ZHUOER Customizer Preview — vanilla JS, no jQuery dependency
 */
(function () {
    'use strict';

    wp.customize('zhuoer_primary_color', function (value) {
        value.bind(function (to) {
            if (!to || '#1a73e8' === to.toLowerCase()) {
                to = '#1a73e8';
            }
            var root = document.documentElement;
            root.style.setProperty('--zhuoer-color-link', to);
            root.style.setProperty('--zhuoer-color-accent', to);
            // darken by 12
            root.style.setProperty('--zhuoer-color-link-hover', '#1765d4');
        });
    });

})();
