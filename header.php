<?php
/**
 * Header template
 * Zhuoer Theme
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<!-- Skip link for keyboard users (W3C WCAG 2.4.1) -->
<a class="zhuoer-skip-link screen-reader-text" href="#main-content">
    跳到主要内容
</a>

<header class="zhuoer-header" id="masthead" role="banner">
    <div class="zhuoer-header__inner">

        <div class="zhuoer-header__brand">
            <?php if ( has_custom_logo() ) : ?>
                <?php the_custom_logo(); ?>
            <?php else : ?>
                <div class="zhuoer-site-branding">
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="zhuoer-site-title-link">
                        <?php if ( is_front_page() ) : ?>
                            <h1 class="zhuoer-site-title"><?php bloginfo( 'name' ); ?></h1>
                        <?php else : ?>
                            <p class="zhuoer-site-title"><?php bloginfo( 'name' ); ?></p>
                        <?php endif; ?>
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <nav id="site-navigation" class="zhuoer-nav" role="navigation" aria-label="<?php esc_attr_e( 'Primary Menu', 'zhuoer' ); ?>">
            <?php
            wp_nav_menu( array(
                'theme_location' => 'primary',
                'menu_id'        => 'primary-menu',
                'menu_class'     => 'zhuoer-nav__menu',
                'container'     => false,
                'fallback_cb'    => '__return_false',
                'depth'          => 2,
            ) );
            ?>
        </nav>

    </div><!-- /.zhuoer-header__inner -->

    <!-- Desktop search: Google-style pill, placed BEFORE theme toggle in DOM -->
    <div class="zhuoer-desktop-search" id="zhuoer-desktop-search">
        <button class="zhuoer-desktop-search__toggle" id="zhuoer-search-toggle" aria-label="<?php esc_attr_e( '搜索', 'zhuoer' ); ?>" type="button">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        </button>
        <?php
        $desktop_search = get_search_form( false );
        $desktop_search = str_replace(
            '<form',
            '<form class="zhuoer-desktop-search__form" aria-label="全站搜索"',
            $desktop_search
        );
        $desktop_search = str_replace(
            '</form>',
            '<button class="zhuoer-desktop-search__close" id="zhuoer-desktop-search__close" type="button" aria-label="关闭搜索"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button></form>',
            $desktop_search
        );
        echo str_replace(
            '<input',
            '<svg class="zhuoer-desktop-search__icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg><input class="zhuoer-desktop-search__input" type="search"',
            $desktop_search
        );
        ?>
    </div>
    <!-- Theme toggle + mobile buttons -->
    <div class="zhuoer-header__actions">
        <button class="zhuoer-theme-toggle" id="zhuoer-theme-toggle" aria-label="<?php esc_attr_e( '切换暗色模式', 'zhuoer' ); ?>" title="<?php esc_attr_e( '切换暗色模式', 'zhuoer' ); ?>">
            <svg class="zhuoer-theme-toggle__icon-sun" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>
            <svg class="zhuoer-theme-toggle__icon-moon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
        </button>
        <button id="zhuoer-search-toggle-mobile" class="zhuoer-search-toggle-mobile" aria-label="<?php esc_attr_e( '搜索', 'zhuoer' ); ?>" type="button">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        </button>
        <button id="zhuoer-menu-toggle" class="zhuoer-menu-toggle" aria-label="<?php esc_attr_e( 'Toggle menu', 'zhuoer' ); ?>" aria-controls="primary-menu" aria-expanded="false">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
        </button>
    </div>

    <!-- Mobile search popup -->
    <div id="zhuoer-search-bar" class="zhuoer-search-bar">
        <div class="zhuoer-container zhuoer-container--wide">
            <button class="zhuoer-mobile-search__close" id="zhuoer-search-form__close" type="button" aria-label="关闭搜索">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
            <div class="zhuoer-mobile-search__inner">
                <svg class="zhuoer-mobile-search__icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <?php get_search_form(); ?>
            </div>
        </div>
    </div>

</header>
