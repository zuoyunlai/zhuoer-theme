# ZHUOER WordPress Theme

> A minimalist, SEO-optimized, fully responsive WordPress theme with a clean Google-like aesthetic.

## Features

- **Minimalist Design** — Clean white aesthetic inspired by Google, no clutter
- **Fully Responsive** — Mobile-first, looks perfect on all devices
- **SEO Optimized** — Meta tags, Open Graph, JSON-LD Schema, canonical URLs, auto sitemap.xml
- **Customizer** — Logo, colors, hero section, SEO fields, social links — no code needed
- **Performance** — Lazy loading images, disabled emojis, print styles, lightweight CSS
- **WooCommerce Ready** — Basic WooCommerce support built-in
- **Translation Ready** — Full i18n with text domain `zhuoer`

## Requirements

- WordPress 6.0+
- PHP 8.0+

## Installation

1. Upload the `zhuoer` folder to `/wp-content/themes/`
2. Go to **Appearance → Themes** and activate ZHUOER
3. Go to **Appearance → Customize** to configure:
   - Site Identity (logo, title, description)
   - Colors & Appearance
   - Homepage Settings (hero title, subtitle, CTA button)
   - SEO Settings (meta title, description, OG image, GA4 ID)
   - Social Links
   - Post Settings

## SEO

ZHUOER includes built-in SEO features:

- **Meta Tags** — Title & description for homepage
- **Open Graph** — OG tags for social sharing (Facebook, WeChat, etc.)
- **Twitter Card** — Summary card with large image
- **JSON-LD Schema** — WebSite + Article schema
- **Canonical URLs** — Prevents duplicate content issues
- **Auto Sitemap** — Visit `/sitemap.xml` to generate sitemap
- **Breadcrumbs** — Call `zhuoer_breadcrumbs()` in templates
- **Google Analytics** — Enter GA4 ID in Customizer → SEO Settings

## Template Hierarchy

```
front-page.php   → Static front page (or blog list if no front-page.php)
singular.php     → Single post or page
single.php       → Single post (blog articles)
page.php         → Static pages
index.php        → Blog archive / category / tag / date / author archives
archive.php      → Category / tag / author / date archives
search.php       → Search results
404.php          → Not found page
```

## Customizer Options

| Section | Options |
|---------|---------|
| Site Identity | Logo, Site Title, Tagline |
| Colors | Primary accent color |
| Homepage | Hero title, subtitle, CTA button text & URL |
| SEO | Meta title, meta description, OG image, GA4 ID |
| Social Links | Twitter, Weibo, GitHub, Zhihu, Bilibili, 小红书, Facebook, Instagram, LinkedIn |
| Post Settings | Featured image display, author bio box |
| Performance | Lazy loading toggle |

## Hooks & Filters

```php
// Add custom body classes
add_filter('body_class', function($classes) {
    $classes[] = 'my-custom-class';
    return $classes;
});

// Modify excerpt length
add_filter('excerpt_length', function($length) {
    return 50;
});

// Add custom styles
add_action('wp_head', function() {
    echo '<style>body { /* your styles */ }</style>';
});
```

## Child Theme

Create a child theme with:

```php
/*
 Theme Name:   ZHUOER Child
 Template:     zhuoer
*/

@import url('../zhuoer/style.css');

/* Your custom styles here */
```

## Sitemap

Automatically generated at: `yoursite.com/sitemap.xml`

## License

GNU General Public License v2 or later
