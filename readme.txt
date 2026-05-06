=== Contributors: zuoyunlai
Tags: blog, custom-logo, custom-menu, editor-style, featured-images, footer-widgets, full-width-template, one-column, threaded-comments, translation-ready
Requires at least: 6.0
Tested up to: 6.7
Requires PHP: 8.0
Stable tag: 1.0.75
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

ZHUOER 是一款面向中国用户的简约风格 WordPress 主题。

== Description ==

ZHUOER 主题采用极简设计理念，专为中国博客和企业官网打造。

Features:

* Responsive design (mobile-first)
* Dark/Light mode toggle
* SEO optimized (JSON-LD, Open Graph, Twitter Cards, Baidu Tongji support)
* WeChat share card support (og:image)
* Gutenberg block editor optimized
* Customizable accent color
* Featured post thumbnail
* Related posts section
* Custom 404 page
* Breadcrumb navigation
* Footer with ICP/GA support

== Installation ==

1. Upload the `zhuoer` folder to `/wp-content/themes/`
2. Activate through the 'Themes' menu in WordPress
3. Go to Appearance > ZHUOER Theme Settings to configure

== Frequently Asked Questions ==

= Does this theme support any plugins? =

ZHUOER is designed to work with the Block Editor (Gutenberg). It also provides optional integration with Baidu Tongji and Google Analytics.

= How do I set the homepage OG image? =

Go to Appearance > ZHUOER Theme Settings and upload an image in the "Social Sharing" section.

= How do I enable dark mode? =

Users can toggle dark/light mode using the switch in the header area.

== Changelog ==

= 1.0.75 =
* Fixed: style.css brace balancing (2 extra } removed)
* Fixed: TOC anchor links on pages (is_singular scope)
* Fixed: homepage slider layout (3fr 2fr grid, image 4:3)
* Fixed: duplicate shop sorting dropdown
* Fixed: TOC headings covered by sticky header (scroll-margin-top)
* Fixed: mobile menu overflow (max-height + scroll)
* Fixed: mobile entry card spacing (margin-bottom 1.5rem)
* Fixed: mobile slider image ratio (4:3) + dots visible
* Fixed: mobile hamburger icon border removed
* i18n: comments.php, content-single-product.php, customizer.php, Product Widget
* Optimized: Widget Transient cache (12h)
* Optimized: Service Worker via wp_enqueue_script
* Optimized: Google Fonts preload
* Optimized: content_width 720 → 900
* Clean: search.php dead code, enhanced.css dedup

= 1.0.70 =
* Color system rewrite — primary color generates complete derived palette via HSL→RGB (links, hover, backgrounds, borders, shadows)
* Hero gradient now dynamically follows primary color
* Dark mode links use theme-derived bright tints (was: hardcoded fallbacks)
* Category pills now use light theme-derived backgrounds
* Calendar widget post dates use transparent bg with theme-colored borders (was: black solid circles)
* Read more button hover uses proper theme-derived colors
* Fixed hero slideshow image uploader (wp_enqueue_media)
* Added aria-label to mobile search input

= 1.0.69 =
* Fixed: Google Fonts switched to fonts.googleapis.cn (China CDN)
* Fixed: related posts `orderby => rand` performance issue — now cached via Transient (12h)
* Fixed: related posts cache auto-clear on post save/delete/trash
* Fixed: front-page pagination base URL hardcoded — now dynamic via get_pagenum_link()
* Fixed: breadcrumb Schema missing itemListElement/itemscope on category li
* Fixed: style.css / functions.php version mismatch
* Added: Organization JSON-LD Schema with logo on homepage
* Added: Table of Contents (TOC) for articles with 3+ headings
* Added: preload + preconnect hints for critical resources in header
* Added: fetchpriority="high" for singular post featured images
* Added: main.js defer loading to avoid render-blocking
* Added: WebP upload and srcset support (WordPress 5.8+)
* Improved: dark mode transition animation (0.3s smooth)

= 1.0.68 =
* Fixed: pagination clicking all links to page 1 (front-page.php query var)
* Fixed: article element nested inside article (singular.php related posts)
* Fixed: desktop search button missing ARIA toggle attributes
* Fixed: Gutenberg editor color palette now dynamic from theme primary color
* Fixed: editor-style.css blockquote border hardcoded color removed
* Added: zhuoer_editor_color_palette() with 8 slots from theme color
* Added: zhuoer_editor_dynamic_css() for admin head CSS variables
* Added: editor-font-sizes now includes Medium (18px)
* Added: social links footer icons redesigned (circular, no pill)
* Added: social icons updated (Weibo, Zhihu brand-correct SVGs)
* Improved: CSS color system uses HSL derivation (customizer.php)
* Improved: dark mode Hero gradient updated per theme color
* Improved: desktop search form input alignment fixed
* Improved: Google Fonts preconnect optimization in admin
* Improved: footer social links redesigned (circular icons)
* Improved: full theme code audit — W3C/WCAG/WordPress standards compliance

= 1.0.59 =
* Fixed code block styling in light mode
* Improved content block aesthetics (blockquote, tables, lists, images)
* Code quality audit and cleanup
* PHP syntax validation pass

= 1.0.58 =
* Enhanced breadcrumb navigation styling
* Content block improvements

= 1.0.57 =
* Fixed author bio get_option/get_theme_mod mismatch
* Version sync with style.css

== Resources ==

* Noto Sans SC font - SIL Open Font License 1.1
* Google Fonts - SIL Open Font License 1.1
* SVG icons - Custom, GPLv2 compatible
* screenshot.png - Created for this theme, GPLv2
