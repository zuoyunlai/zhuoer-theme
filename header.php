<?php
/**
 * Unified Header Template
 * Zhuoer Theme v1.0.70 - 合并 header + shop header
 */
defined( 'ABSPATH' ) || exit;

$is_shop = function_exists( 'WC' ) && ( is_woocommerce() || is_cart() || is_checkout() || is_account_page() );
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.cn" crossorigin />
    <link rel="preload" href="<?php echo esc_url( get_stylesheet_uri() ); ?>" as="style" onload="this.onload=null;this.rel='stylesheet'" />
    <script>
    (function() {
        var html = document.documentElement;
        var stored = localStorage.getItem('zhuoer-theme');
        if ( stored === 'dark' || ( stored !== 'light' && window.matchMedia('(prefers-color-scheme: dark)').matches ) ) {
            html.setAttribute('data-theme', 'dark');
        }
    })();
    </script>
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div id="zhuoer-theme-announce" aria-live="polite" class="screen-reader-text"></div>
<a class="zhuoer-skip-link screen-reader-text" href="#main-content"><?php esc_html_e( '跳到主要内容', 'zhuoer' ); ?></a>

<header class="zhuoer-header" id="masthead" role="banner">
    <div class="zhuoer-header__inner">
        <div class="zhuoer-header__brand">
            <?php if ( has_custom_logo() ) : ?>
                <?php if ( $is_shop ) : ?>
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="zhuoer-header__logo-link" rel="home" aria-label="<?php bloginfo( 'name' ); ?>">
                    <?php the_custom_logo(); ?>
                </a>
                <?php else : ?>
                    <?php the_custom_logo(); ?>
                <?php endif; ?>
            <?php else : ?>
                <div class="zhuoer-site-branding">
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="zhuoer-site-title-link">
                        <?php if ( is_front_page() && ! $is_shop ) : ?>
                            <h1 class="zhuoer-site-title"><?php bloginfo( 'name' ); ?></h1>
                        <?php else : ?>
                            <p class="zhuoer-site-title"><?php bloginfo( 'name' ); ?></p>
                        <?php endif; ?>
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <nav id="site-navigation" class="zhuoer-nav" role="navigation" aria-label="<?php esc_attr_e( 'Primary Menu', 'zhuoer' ); ?>">
            <?php wp_nav_menu( array(
                'theme_location' => 'primary',
                'menu_id'        => 'primary-menu',
                'menu_class'     => 'zhuoer-nav__menu',
                'container'      => false,
                'fallback_cb'    => '__return_false',
                'depth'          => 2,
            ) ); ?>
        </nav>
    </div>

    <!-- Desktop search -->
    <div class="zhuoer-desktop-search" id="zhuoer-desktop-search">
        <button class="zhuoer-desktop-search__toggle" id="zhuoer-search-toggle" aria-label="<?php esc_attr_e( '搜索', 'zhuoer' ); ?>" aria-expanded="false" type="button">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        </button>
        <?php
        $desktop_search = get_search_form( false );
        $desktop_search = str_replace( '<form', '<form class="zhuoer-desktop-search__form" aria-label="全站搜索"', $desktop_search );
        $desktop_search = str_replace( '</form>', '<button class="zhuoer-desktop-search__close" id="zhuoer-desktop-search__close" type="button" aria-label="关闭搜索"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button></form>', $desktop_search );
        echo preg_replace( '/(<input)([^>]*type="search"[^>]*)(>)/', '<svg class="zhuoer-desktop-search__icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg><input class="zhuoer-desktop-search__input"$2>', $desktop_search, 1 );
        ?>
    </div>

    <div class="zhuoer-header__actions">
        <button class="zhuoer-theme-toggle" id="zhuoer-theme-toggle" aria-label="<?php esc_attr_e( '切换暗色模式', 'zhuoer' ); ?>">
            <svg class="zhuoer-theme-toggle__icon-sun" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>
            <svg class="zhuoer-theme-toggle__icon-moon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
        </button>
        
        <?php if ( $is_shop && function_exists( 'WC' ) && WC()->cart ) : ?>
        <!-- Cart icon (WooCommerce pages only) -->
        <a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="zhuoer-cart-link" aria-label="<?php esc_attr_e( '购物车', 'zhuoer' ); ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
            <?php $cart_count = WC()->cart->get_cart_contents_count(); ?>
            <?php if ( $cart_count > 0 ) : ?>
            <span class="zhuoer-cart-count"><?php echo esc_html( $cart_count ); ?></span>
            <?php endif; ?>
        </a>
        <?php endif; ?>
        
        <button id="zhuoer-search-toggle-mobile" class="zhuoer-search-toggle-mobile" aria-label="<?php esc_attr_e( '搜索', 'zhuoer' ); ?>" type="button">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        </button>
        <button id="zhuoer-menu-toggle" class="zhuoer-menu-toggle" aria-label="<?php esc_attr_e( 'Toggle menu', 'zhuoer' ); ?>" aria-controls="primary-menu" aria-expanded="false">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
        </button>
    </div>

    <!-- Mobile search popup -->
    <div id="zhuoer-search-bar" class="zhuoer-search-bar">
        <div class="zhuoer-container zhuoer-container--wide">
            <form role="search" method="get" class="zhuoer-mobile-search" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                <input type="search" name="s" class="zhuoer-mobile-search__input" aria-label="<?php esc_attr_e( '搜索', 'zhuoer' ); ?>" placeholder="<?php esc_attr_e( '搜索…', 'zhuoer' ); ?>" required />
                <button type="submit" class="zhuoer-mobile-search__submit" aria-label="<?php esc_attr_e( '搜索', 'zhuoer' ); ?>"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg></button>
                <button type="button" class="zhuoer-mobile-search__close" id="zhuoer-search-form__close" aria-label="<?php esc_attr_e( '关闭搜索', 'zhuoer' ); ?>"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
            </form>
        </div>
    </div>
</header>
<div class="zhuoer-reading-progress" id="zhuoer-reading-progress" aria-hidden="true" role="presentation"></div>
