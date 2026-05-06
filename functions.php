<?php
/**
 * ZHUOER Theme Functions
 *
 * @package ZHUOER
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'ZHUOER_VERSION', '1.0.76' );
define( 'ZHUOER_DIR', get_template_directory() );

/* ─── Theme Setup ─── */
/**
 * Theme Setup
 * 
 * Registers theme features: menus, widgets, thumbnails, HTML5, Gutenberg, WooCommerce.
 * 
 * @since 1.0.0
 */
function zhuoer_setup() {
    load_theme_textdomain( 'zhuoer', ZHUOER_DIR . '/languages' );
    add_theme_support( 'automatic-feed-links' );
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_image_size( 'zhuoer-thumbnail', 1080, 607, true ); // 16:9 ratio

    register_nav_menus(
        array(
            'primary' => __( 'Primary Menu', 'zhuoer' ),
            'footer'  => __( 'Footer Menu', 'zhuoer' ),
        )
    );

    add_theme_support(
        'html5',
        array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' )
    );
    add_theme_support( 'wp-block-styles' );
    add_theme_support( 'align-wide' );
    add_theme_support( 'editor-styles' );
    add_editor_style( 'assets/css/editor-style.css' );
    add_theme_support( 'custom-header', array(
        'default-image' => get_template_directory_uri() . '/assets/images/header-default.jpg',
        'width'        => 1920,
        'height'       => 400,
        'flex-height'  => true,
        'flex-width'   => true,
        'header-text'  => false,
    ) );
    add_theme_support( 'custom-background', array(
        'default-color' => 'f8f9fa',
        'default-image' => '',
    ) );
    add_theme_support( 'responsive-embeds' );
    add_theme_support(
        'custom-logo',
        array( 'height' => 80, 'width' => 300, 'flex-width' => true, 'flex-height' => true )
    );
    add_theme_support( 'customize-selective-refresh-widgets' );
    add_theme_support( 'woocommerce' );
    add_theme_support( 'site-icon' );
    add_theme_support( 'appearance', array( 'customize-selective-refresh-widgets' ) );

    add_theme_support(
        'editor-font-sizes',
        array(
            array( 'name' => __( 'Small',   'zhuoer' ), 'slug' => 'small',   'size' => 14 ),
            array( 'name' => __( 'Normal',  'zhuoer' ), 'slug' => 'normal',  'size' => 16 ),
            array( 'name' => __( 'Medium',  'zhuoer' ), 'slug' => 'medium',  'size' => 18 ),
            array( 'name' => __( 'Large',   'zhuoer' ), 'slug' => 'large',   'size' => 22 ),
            array( 'name' => __( 'Huge',    'zhuoer' ), 'slug' => 'huge',    'size' => 28 ),
        )
    );
}
/* ─── Dynamic Editor CSS Variables (admin head) ─── */
/**
 * Inject --zhuoer-editor-primary CSS variable into admin head.
 * This drives the hardcoded accent elements in editor-style.css
 * and any Gutenberg blocks that don't use the color palette.
 */
/**
 * Inject Dynamic Editor CSS Variables
 * 
 * Outputs --zhuoer-editor-primary CSS variable for Gutenberg editor.
 * Runs on admin_head to style editor elements with theme primary color.
 * 
 * @since 1.0.70
 */
function zhuoer_editor_dynamic_css() {
    if ( ! is_admin() ) return;
    $primary = get_option( 'zhuoer_primary_color', '#1a73e8' );
    $hex = ltrim( $primary, '#' );
    if ( strlen( $hex ) !== 6 ) {
        $primary = '#1a73e8';
        $hex = '1a73e8';
    }
    $colors = zhuoer_compute_colors( $hex );
    if ( ! $colors ) return;
    $var_primary = esc_attr( $colors['primary'] );
    printf(
        '<style id="zhuoer-editor-dynamic-css" type="text/css">
:root{--zhuoer-editor-primary:%s;--wp--preset--color--primary:%s;--wp--preset--color--primary-dark:%s;--wp--preset--color--primary-light:%s;--wp--preset--color--primary-border:%s;}
</style>',
        $var_primary,
        $var_primary,
        esc_attr( $colors['primary-dark'] ),
        esc_attr( $colors['primary-light'] ),
        esc_attr( $colors['primary-border'] )
    );
}
add_action( 'admin_head', 'zhuoer_editor_dynamic_css', 1 );

/* ─── Dynamic Gutenberg Editor Color Palette ─── */
/**
 * Compute HSL-derived colors from a primary hex color.
 * Mirrors the logic in inc/customizer.php so both stay in sync.
 */
function zhuoer_compute_colors( string $hex ): ?array {
    // Transient cache for color computation (12 hours)
    $cache_key = 'zhuoer_colors_v2_' . sanitize_hex_color_no_hash( $hex );
    $cached = get_transient( $cache_key );
    if ( $cached ) {
        return $cached;
    }
    
    $hex = ltrim( $hex, '#' );
    if ( strlen( $hex ) !== 6 ) {
        return null;
    }

    $rp = hexdec( substr( $hex, 0, 2 ) ) / 255;
    $gp = hexdec( substr( $hex, 2, 2 ) ) / 255;
    $bp = hexdec( substr( $hex, 4, 2 ) ) / 255;
    $max = max( $rp, $gp, $bp );
    $min = min( $rp, $gp, $bp );
    $l   = ( $max + $min ) / 2;
    $s   = 0;
    if ( $max !== $min ) {
        $d = $max - $min;
        $s = $l > 0.5 ? $d / ( 2 - $max - $min ) : $d / ( $max + $min );
        if      ( $max === $rp ) $h = fmod( ( $gp - $bp ) / $d + ( $gp < $bp ? 6.0 : 0.0 ), 6.0 );
        elseif ( $max === $gp ) $h = ( $bp - $rp ) / $d + 2;
        else                    $h = ( $rp - $gp ) / $d + 4;
        $h /= 6;
    }
    $h360 = $h * 360;
    $s100 = $s * 100;
    $l100 = $l * 100;

    $h2rgb = function( $p2, $q2, $t ) {
        if ( $t < 0 ) $t++;
        if ( $t > 1 ) $t--;
        if ( $t < 1/6 ) return $p2 + ( $q2 - $p2 ) * 6 * $t;
        if ( $t < 1/2 ) return $q2;
        if ( $t < 2/3 ) return $p2 + ( $q2 - $p2 ) * ( 2/3 - $t ) * 6;
        return $p2;
    };
    $hsl2h = function( $hh, $ss, $ll ) use ( $h2rgb ) {
        $s2 = $ss / 100;
        $l2 = $ll / 100;
        $q2 = $l2 < 0.5 ? $l2 * ( 1 + $s2 ) : $l2 + $s2 - $l2 * $s2;
        $p2 = 2 * $l2 - $q2;
        $r  = round( $h2rgb( $p2, $q2, $h2rgb( $p2, $q2, $h2rgb( $p2, $q2, $hh / 360 + 1/3 ) ) ) * 255 );
        $g  = round( $h2rgb( $p2, $q2, $h2rgb( $p2, $q2, $hh / 360 ) ) * 255 );
        $b  = round( $h2rgb( $p2, $q2, $h2rgb( $p2, $q2, $hh / 360 - 1/3 ) ) * 255 );
        return sprintf( '#%02x%02x%02x', max( 0, min( 255, $r ) ), max( 0, min( 255, $g ) ), max( 0, min( 255, $b ) ) );
    };

    // Derived colors — single source of truth for all theme CSS variables
    // Customizer CSS + Editor CSS both read from this one function.
    $colors = array(
        // ── Core (used by editor palette + customizer) ──
        'primary'            => sprintf( '#%s', $hex ),
        'primary-dark'       => $hsl2h( $h360, $s100,           max( 0, $l100 - 12 ) ),
        'primary-light'      => $hsl2h( $h360, max( 0, $s100 - 25 ), min( 100, $l100 + 40 ) ),
        'primary-border'     => $hsl2h( $h360, max( 0, $s100 - 35 ), min( 100, $l100 + 22 ) ),

        // ── Frontend CSS variables (legacy customizer keys kept for compat) ──
        'link'               => sprintf( '#%s', $hex ),
        'link-hover'         => $hsl2h( $h360, max( 0, $s100 - 15 ), max( 0, $l100 - 10 ) ),
        'primary-bg'         => $hsl2h( $h360, max( 0, $s100 - 25 ), min( 100, $l100 + 38 ) ),
        'border'             => $hsl2h( $h360, max( 0, $s100 - 25 ), max( 0, $l100 - 20 ) ),
        'light-bg'           => $hsl2h( $h360, max( 0, $s100 - 25 ), min( 100, $l100 + 38 ) ),
        'light-border'       => $hsl2h( $h360, max( 0, $s100 - 35 ), min( 100, $l100 + 20 ) ),
        'dark-bg'            => $hsl2h( $h360, max( 0, $s100 - 20 ), max( 0, $l100 - 55 ) ),
        'dark-text'          => $hsl2h( $h360, max( 0, $s100 - 15 ), min( 100, $l100 + 30 ) ),
        'dark-border'        => $hsl2h( $h360, max( 0, $s100 - 25 ), max( 0, $l100 - 45 ) ),
        'alpha'              => sprintf( 'rgba(%d,%d,%d,0.25)', $rp * 255, $gp * 255, $bp * 255 ),
        'alpha-2'            => sprintf( 'rgba(%d,%d,%d,0.08)', $rp * 255, $gp * 255, $bp * 255 ),
        'hero-grad-1'        => $hsl2h( $h360, $s100, max( 5, $l100 - 22 ) ),
        'hero-grad-2'        => sprintf( '#%s', $hex ),
        'hero-grad-3'        => $hsl2h( $h360, max( 0, $s100 - 15 ), min( 90, $l100 + 35 ) ),
        'cta-shadow'         => sprintf( 'rgba(%d,%d,%d,0.30)', $rp * 255, $gp * 255, $bp * 255 ),
        'cta-shadow-hover'   => sprintf( 'rgba(%d,%d,%d,0.40)', $rp * 255, $gp * 255, $bp * 255 ),

        // ── Dark-mode specific ──
        'hero-grad-dark-1'   => $hsl2h( $h360, max( 0, $s100 - 15 ), max( 5, $l100 - 65 ) ),
        'hero-grad-dark-2'   => $hsl2h( $h360, $s100, max( 8, $l100 - 35 ) ),
        'hero-grad-dark-3'   => $hsl2h( $h360, max( 0, $s100 - 20 ), min( 80, $l100 - 10 ) ),
        'link-dark'          => $hsl2h( $h360, max( 0, $s100 - 20 ), min( 100, $l100 + 25 ) ),
        'link-hover-dark'    => $hsl2h( $h360, max( 0, $s100 - 15 ), min( 100, $l100 + 35 ) ),
        'dark-primary-bg'    => sprintf( 'rgba(%d,%d,%d,0.15)', $rp * 255, $gp * 255, $bp * 255 ),
        'dark-primary-border'=> sprintf( 'rgba(%d,%d,%d,0.30)', $rp * 255, $gp * 255, $bp * 255 ),
        'dark-alpha'         => sprintf( 'rgba(%d,%d,%d,0.28)', $rp * 255, $gp * 255, $bp * 255 ),
        'dark-alpha-2'       => sprintf( 'rgba(%d,%d,%d,0.10)', $rp * 255, $gp * 255, $bp * 255 ),
    );

    set_transient( $cache_key, $colors, 12 * HOUR_IN_SECONDS );
    return $colors;
}

/**
 * Register Gutenberg editor color palette dynamically from theme color option.
 * Hooked at priority 20 so it runs after zhuoer_setup() (priority 10).
 */
/**
 * Register Gutenberg Editor Color Palette
 * 
 * Dynamically generates color palette from theme primary color option.
 * Includes primary, primary-dark, primary-light, primary-border variants.
 * 
 * @since 1.0.70
 */
function zhuoer_editor_color_palette() {
    $primary = get_option( 'zhuoer_primary_color', '#1a73e8' );
    $hex     = ltrim( $primary, '#' );
    if ( strlen( $hex ) !== 6 ) {
        $primary = '#1a73e8';
        $hex     = '1a73e8';
    }

    $colors = zhuoer_compute_colors( $hex );
    if ( ! $colors ) return;

    add_theme_support(
        'editor-color-palette',
        array(
            array(
                'name' => __( 'Primary',   'zhuoer' ),
                'slug' => 'primary',
                'color' => $colors['primary'],
            ),
            array(
                'name' => __( 'Primary Dark',   'zhuoer' ),
                'slug' => 'primary-dark',
                'color' => $colors['primary-dark'],
            ),
            array(
                'name' => __( 'Primary Light',  'zhuoer' ),
                'slug' => 'primary-light',
                'color' => $colors['primary-light'],
            ),
            array(
                'name' => __( 'Primary Border', 'zhuoer' ),
                'slug' => 'primary-border',
                'color' => $colors['primary-border'],
            ),
            array(
                'name' => __( 'Text Dark',  'zhuoer' ),
                'slug' => 'dark',
                'color' => '#202124',
            ),
            array(
                'name' => __( 'Text Muted', 'zhuoer' ),
                'slug' => 'muted',
                'color' => '#5f6368',
            ),
            array(
                'name' => __( 'Surface',    'zhuoer' ),
                'slug' => 'surface',
                'color' => '#fafafa',
            ),
            array(
                'name' => __( 'Border',     'zhuoer' ),
                'slug' => 'border',
                'color' => '#e8e8e8',
            ),
        )
    );
}
add_action( 'after_setup_theme', 'zhuoer_editor_color_palette', 20 );

add_action( 'after_setup_theme', 'zhuoer_setup' );

function zhuoer_content_width() {
    $GLOBALS['content_width'] = apply_filters( 'zhuoer_content_width', 900 );
}
add_action( 'after_setup_theme', 'zhuoer_content_width', 0 );
add_action( 'after_setup_theme', 'zhuoer_register_block_styles' );
add_action( 'after_setup_theme', 'zhuoer_register_block_patterns' );

/* ─── 修复首页分页 404 问题 ─── */
add_action( 'pre_get_posts', function ( $query ) {
    if ( is_admin() || ! $query->is_main_query() ) {
        return;
    }
    // 当 /page/N/ 被访问时，is_front_page() 和 is_home() 在 pre_get_posts 里都返回 true
    //（show_on_front = posts 时）。统一设置 posts_per_page 与后台 "每页文章数" 一致。
    if ( $query->is_front_page() || $query->is_home() ) {
        $query->set( 'posts_per_page', get_option( 'posts_per_page', 10 ) );
    }
} );

/* ─── 修复首页分页超出范围时 404 → 重定向到末页 ─── */
add_action( 'template_redirect', function () {
    if ( is_admin() ) {
        return;
    }
    $paged = get_query_var( 'paged', 0 );
    if ( $paged <= 1 ) {
        return;
    }
    global $wp_query;
    $posts_per_page = $wp_query->get( 'posts_per_page', 0 );
    if ( $posts_per_page <= 0 ) {
        return;
    }
    // max_num_pages = 0 且 found_posts = 0（WordPress set_found_posts() 在 LIMIT 返回 0 行时提前退出）
    // 用直接 COUNT 查询获取真实文章总数
    $max = $wp_query->max_num_pages;
    $total = $wp_query->found_posts;
    if ( $max <= 0 || $total <= 0 ) {
        global $wpdb;
        $total = (int) $wpdb->get_var(
            "SELECT COUNT(*) FROM $wpdb->posts WHERE post_type = 'post' AND post_status = 'publish'"
        );
        $max = $total > 0 ? (int) ceil( $total / $posts_per_page ) : 1;
    }
    if ( $paged > $max ) {
        $redirect_url = $max > 1 ? get_pagenum_link( $max, false ) : home_url( '/' );
        wp_redirect( esc_url_raw( $redirect_url ), 302 );
        exit;
    }
} );

/* ─── Widget Areas ─── */
/**
 * Register Widget Areas
 * 
 * Creates sidebar and footer widget regions.
 * 
 * @since 1.0.0
 */
function zhuoer_widgets_init() {
    register_sidebar(
        array(
            'name'          => __( 'Sidebar', 'zhuoer' ),
            'id'            => 'sidebar-1',
            'description'   => __( 'Add widgets here to appear in your sidebar.', 'zhuoer' ),
            'before_widget' => '<section id="%1$s" class="zhuoer-widget widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h3 class="zhuoer-widget__title">',
            'after_title'   => '</h3>',
        )
    );
    register_sidebar(
        array(
            'name'          => __( 'Footer Widgets', 'zhuoer' ),
            'id'            => 'footer-1',
            'description'   => __( 'Add widgets here to appear in the footer.', 'zhuoer' ),
            'before_widget' => '<div id="%1$s" class="zhuoer-widget widget %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h4 class="zhuoer-widget__title">',
            'after_title'   => '</h4>',
        )
    );
}
add_action( 'widgets_init', 'zhuoer_widgets_init' );

function zhuoer_register_widgets() {
    register_widget( 'ZHUOER_Recent_Comments_Widget' );
}
add_action( 'widgets_init', 'zhuoer_register_widgets' );

/* ─── 辅助函数：自动使用 .min 文件（生产环境） ─── */
/**
 * Get Asset URL with Minified Fallback
 * 
 * Returns .min version path if available in production (WP_DEBUG off).
 * Falls back to original file if .min doesn't exist.
 * 
 * @param string $relative_path Path relative to theme root
 * @return string Full URL to asset
 * @since 1.0.70
 */
function zhuoer_asset_url( string $relative_path ): string {
    $base = get_template_directory_uri();
    $dir  = get_template_directory();
    // WP_DEBUG 开启时优先用原始文件，关闭时尝试 .min
    if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) {
        $min_path = str_replace( array( '.css', '.js' ), array( '.min.css', '.min.js' ), $relative_path );
        if ( file_exists( $dir . $min_path ) ) {
            return $base . $min_path;
        }
    }
    return $base . $relative_path;
}

/**
 * Get Asset Version (filemtime or theme version)
 * 
 * Uses file modification time for cache busting when file exists.
 * Falls back to ZHUOER_VERSION constant.
 * 
 * @param string $relative_path Path relative to theme root
 * @return string Version string for wp_enqueue
 * @since 1.0.70
 */
function zhuoer_asset_version( string $relative_path ): string {
    $dir = get_template_directory();
    $file = $dir . $relative_path;
    if ( file_exists( $file ) ) {
        return filemtime( $file );
    }
    return ZHUOER_VERSION;
}

/* ─── 禁用 WordPress 经典主题默认块样式（避免黑底按钮等） ─── */
add_action( 'wp_enqueue_scripts', function() {
    wp_dequeue_style( 'classic-theme-styles' );
}, 20 );

/* ─── Enqueue ─── */
/**
 * Enqueue Theme Scripts and Styles
 * 
 * Loads Google Fonts (China CDN), main stylesheet, print/mobile/header CSS,
 * and main.js with defer for performance.
 * 
 * @since 1.0.0
 */
function zhuoer_scripts() {
    /* Google Fonts — 使用国内镜像加速 */
    add_action( 'wp_head', function () {
        echo '<link rel="preconnect" href="https://fonts.googleapis.cn" />';
        echo '<link rel="preconnect" href="https://fonts.gstatic.cn" crossorigin />';
        echo '<link rel="preload" as="style" href="https://fonts.googleapis.cn/css2?family=Noto+Sans+SC:wght@400;500;700&display=swap" />';
    }, 1 );
    $fonts_url = 'https://fonts.googleapis.cn/css2?family=Noto+Sans+SC:wght@400;500;700&display=swap';
    wp_enqueue_style( 'zhuoer-google-fonts', $fonts_url, array(), null );

    /* Main theme stylesheet */
    wp_enqueue_style( 'zhuoer-style', get_stylesheet_uri(), array( 'zhuoer-google-fonts' ), ZHUOER_VERSION );
    wp_enqueue_style( 'zhuoer-print', zhuoer_asset_url( '/assets/css/print.css' ), array( 'zhuoer-style' ), zhuoer_asset_version( '/assets/css/print.css' ), 'print' );
    wp_enqueue_style( 'zhuoer-mobile', zhuoer_asset_url( '/assets/css/mobile.css' ), array( 'zhuoer-style' ), zhuoer_asset_version( '/assets/css/mobile.css' ) );
    wp_enqueue_style( 'zhuoer-header', zhuoer_asset_url( '/assets/css/header.css' ), array( 'zhuoer-style' ), zhuoer_asset_version( '/assets/css/header.css' ) );
    // WooCommerce custom styles — only load on WooCommerce pages
    if ( function_exists( 'is_woocommerce' ) && ( is_woocommerce() || is_cart() || is_checkout() || is_account_page() ) ) {
        wp_enqueue_style( 'zhuoer-woocommerce', zhuoer_asset_url( '/woocommerce/css/woocommerce.css' ), array( 'zhuoer-style' ), ZHUOER_VERSION );
    }
    // Enhanced visual styles — refines typography, shadows, micro-interactions
    wp_enqueue_style( 'zhuoer-design-tokens', zhuoer_asset_url( '/assets/css/design-tokens.css' ), array( 'zhuoer-style' ), zhuoer_asset_version( '/assets/css/design-tokens.css' ) );
    wp_enqueue_style( 'zhuoer-enhanced', zhuoer_asset_url( '/assets/css/enhanced.css' ), array( 'zhuoer-design-tokens' ), zhuoer_asset_version( '/assets/css/enhanced.css' ) );
    wp_enqueue_script( 'zhuoer-main', zhuoer_asset_url( '/assets/js/main.js' ), array(), zhuoer_asset_version( '/assets/js/main.js' ), true );
    // main.js 加 defer 避免阻塞解析
    add_filter( 'script_loader_tag', function ( $tag, $handle ) {
        if ( 'zhuoer-main' === $handle ) {
            return str_replace( ' src=', ' defer src=', $tag );
        }
        return $tag;
    }, 10, 2 );

    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
        wp_enqueue_script( 'comment-reply' );
    }
}
add_action( 'wp_enqueue_scripts', 'zhuoer_scripts' );

function zhuoer_editor_assets() {
    wp_enqueue_style( 'zhuoer-editor-style', get_template_directory_uri() . '/assets/css/editor-style.css', array(), ZHUOER_VERSION );
}
add_action( 'enqueue_block_editor_assets', 'zhuoer_editor_assets' );

/* ─── PWA / Service Worker ─── */
function zhuoer_register_service_worker() {
    wp_enqueue_script( 'zhuoer-sw', zhuoer_asset_url( '/assets/js/sw-register.js' ), array(), ZHUOER_VERSION, true );
    wp_add_inline_script( 'zhuoer-sw',
        '"serviceWorker" in navigator && navigator.serviceWorker.register("'
        . esc_url( get_template_directory_uri() . '/assets/js/sw.js' )
        . '", { scope: "/" }).catch(function(e) { console.log("SW:", e); });'
    );
}
add_action( 'wp_footer', 'zhuoer_register_service_worker', 100 );

/* ─── Web App Manifest ─── */
/**
 * Generate PWA Web App Manifest
 * 
 * Outputs manifest.json inline (base64) for PWA support.
 * Uses site_icon for app icons.
 * 
 * @since 1.0.70
 */
function zhuoer_web_app_manifest() {
    $manifest = array(
        'name'        => get_bloginfo( 'name' ),
        'short_name'  => mb_substr( get_bloginfo( 'name' ), 0, 12 ),
        'start_url'   => home_url( '/' ),
        'display'     => 'standalone',
        'background_color' => '#ffffff',
        'theme_color' => get_option( 'zhuoer_primary_color', '#1a73e8' ),
        'orientation' => 'portrait',
        'icons'       => array(),
    );
    $icon_id = get_option( 'site_icon' );
    if ( $icon_id ) {
        $sizes = array( 192, 512 );
        foreach ( $sizes as $size ) {
            $icon_url = wp_get_attachment_image_url( $icon_id, array( $size, $size ) );
            if ( $icon_url ) {
                $manifest['icons'][] = array(
                    'src'   => $icon_url,
                    'sizes' => $size . 'x' . $size,
                    'type'  => 'image/png',
                );
            }
        }
    }
    echo '<link rel="manifest" href="data:application/json;base64,' . esc_attr( base64_encode( wp_json_encode( $manifest ) ) ) . '" />';
    echo '<meta name="theme-color" content="' . esc_attr( get_option( 'zhuoer_primary_color', '#1a73e8' ) ) . '" />';
}
add_action( 'wp_head', 'zhuoer_web_app_manifest', 1 );

/* ─── SVG Icons ─── */
/**
 * Get SVG Icon HTML
 * 
 * Returns inline SVG markup for common icons (menu, close, search, cart, etc.)
 * 
 * @param string $icon Icon name (menu, close, search, cart, sun, moon, etc.)
 * @return string SVG HTML markup
 * @since 1.0.0
 */
function zhuoer_icon( string $icon ): string {
    $icons = array(
        'menu'   => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>',
        'close'  => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>',
        'clock'  => '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>',
        'user'   => '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>',
        'folder' => '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>',
        'arrow'  => '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>',
        'book'   => '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>',
        'tag'    => '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>',
        'eye'    => '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>',
    );
    return isset( $icons[ $icon ] ) ? $icons[ $icon ] : '';
}

/* ─── Include Modules ─── */
require ZHUOER_DIR . '/inc/customizer.php';
require ZHUOER_DIR . '/inc/seo.php';
require ZHUOER_DIR . '/inc/template-tags.php';
require ZHUOER_DIR . '/inc/class-zhuoer-recent-comments-widget.php';

/* ─── Excerpt ─── */
function zhuoer_excerpt_length( int $length ): int {
    return 35;
}
add_filter( 'excerpt_length', 'zhuoer_excerpt_length' );

function zhuoer_excerpt_more( string $more ): string {
    return '…';
}
add_filter( 'excerpt_more', 'zhuoer_excerpt_more' );


function zhuoer_disable_emojis() {
    remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
    remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
    remove_action( 'wp_print_styles', 'print_emoji_styles' );
    remove_action( 'admin_print_styles', 'print_emoji_styles' );
    remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
    remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
    remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
}
add_action( 'init', 'zhuoer_disable_emojis' );

/* ─── Gravatar 头像国内加速 — cravatar.cn ─── */
function zhuoer_gravatar_cdn( $url ) {
    $cdn = 'https://cravatar.cn';
    $url = str_replace(
        array(
            'https://www.gravatar.com', 'https://0.gravatar.com', 'https://1.gravatar.com',
            'https://2.gravatar.com',   'https://secure.gravatar.com',
            'http://www.gravatar.com',  'http://0.gravatar.com',  'http://1.gravatar.com',
            'http://2.gravatar.com',    'http://secure.gravatar.com',
        ),
        $cdn,
        $url
    );
    return $url;
}
add_filter( 'get_avatar',             'zhuoer_gravatar_cdn' );
add_filter( 'get_avatar_url',         'zhuoer_gravatar_cdn' );

/* ─── WebP 支持：优先输出 WebP srcset（WordPress 5.8+） ─── */
add_filter( 'wp_upload_image_mime_transforms', function ( $transforms ) {
    $transforms['image/jpeg'] = array( 'image/webp', 'image/jpeg' );
    $transforms['image/png']  = array( 'image/webp', 'image/png' );
    return $transforms;
} );

/* ─── 清除相关文章缓存（文章更新/删除时） ─── */
/**
 * Clear Related Posts Transient Cache
 * 
 * Deletes transient cache when post is updated/deleted/trashed.
 * Hooked to save_post, deleted_post, trashed_post, switch_theme.
 * 
 * @param int $post_id Post ID
 * @since 1.0.70
 */
function zhuoer_clear_related_cache( $post_id ) {
    global $wpdb;
    $pid = absint( $post_id );
    if ( $pid <= 0 ) return;
    // Clean related transient for this specific post only
    $wpdb->query( $wpdb->prepare(
        "DELETE FROM {$wpdb->options} WHERE option_name = %s",
        '_transient_zhuoer_related_' . $pid
    ) );
    // Also clean timeout counterpart
    $wpdb->query( $wpdb->prepare(
        "DELETE FROM {$wpdb->options} WHERE option_name = %s",
        '_transient_timeout_zhuoer_related_' . $pid
    ) );
}
add_action( 'save_post',      'zhuoer_clear_related_cache' );
add_action( 'deleted_post',   'zhuoer_clear_related_cache' );
add_action( 'trashed_post',   'zhuoer_clear_related_cache' );
add_action( 'switch_theme',   'zhuoer_clear_related_cache' );

/* ─── Custom Logo alt text (W3C要求有alt属性) ─── */
add_filter( 'get_custom_logo', function ( $html ) {
    if ( empty( $html ) ) return $html;
    $site_name = get_bloginfo( 'name' );
    if ( preg_match( '/alt="[^"]*"/', $html ) ) {
        $html = preg_replace( '/alt="[^"]*"/', 'alt="' . esc_attr( $site_name ) . '"', $html );
    } else {
        $html = str_replace( '<img ', '<img alt="' . esc_attr( $site_name ) . '" ', $html );
    }
    return $html;
} );


/* ── Block Styles & Patterns ── */
function zhuoer_register_block_styles() {
    register_block_style(
        'core/image',
        array(
            'name'  => 'rounded',
            'label' => __( 'Rounded', 'zhuoer' ),
        )
    );
    register_block_style(
        'core/quote',
        array(
            'name'  => 'zhuoer-accent',
            'label' => __( 'ZHUOER Accent', 'zhuoer' ),
        )
    );
}


function zhuoer_register_block_patterns() {
    register_block_pattern(
        'zhuoer/hero-section',
        array(
            'title'       => __( 'Hero Section', 'zhuoer' ),
            'description' => __( 'A full-width hero with title and CTA.', 'zhuoer' ),
            'content'     => '<!-- wp:cover {"dimRatio":50,"overlayColor":"accent","minHeight":400} --><div class="wp-block-cover has-accent-background-color has-background-dim" style="min-height:400px"><div class="wp-block-cover__inner-container"><!-- wp:heading --><h2>Your Heading Here</h2><!-- /wp:heading --><!-- wp:paragraph --><p>Add a compelling description.</p><!-- /wp:paragraph --></div></div><!-- /wp:cover -->',
        )
    );
}

/* ============================================================
   ZHUOER WOOCOMMERCE ENHANCEMENTS
   ============================================================ */

/**
 * Disable default WooCommerce breadcrumb (we show our own)
 */
add_action( 'init', 'zhuoer_disable_default_breadcrumb' );
function zhuoer_disable_default_breadcrumb() {
    remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
}

/**
 * Add "Buy Now" button after Add to Cart
 */
add_action( 'woocommerce_after_add_to_cart_button', 'zhuoer_buy_now_button' );
function zhuoer_buy_now_button() {
    global $product;
    if ( !$product || !$product->is_in_stock() ) return;
    $url = add_query_arg( 'add-to-cart', $product->get_id(), wc_get_checkout_url() );
    echo '<a href="' . esc_url( $url ) . '" class="zhuoer-btn-buy-now">' . esc_html__( '立即购买', 'zhuoer' ) . '</a>';
}

/**
 * Quantity +/- buttons
 */
add_action( 'woocommerce_before_quantity_input_field', 'zhuoer_qty_minus' );
function zhuoer_qty_minus() {
    echo '<button type="button" class="zhuoer-qty-btn zhuoer-qty-btn--minus" aria-label="' . esc_attr__( '减少', 'zhuoer' ) . '">−</button>';
}

add_action( 'woocommerce_after_quantity_input_field', 'zhuoer_qty_plus' );
function zhuoer_qty_plus() {
    echo '<button type="button" class="zhuoer-qty-btn zhuoer-qty-btn--plus" aria-label="' . esc_attr__( '增加', 'zhuoer' ) . '">+</button>';
}

/**
 * Load share-buttons.php (for product sharing)
 */
require_once ZHUOER_DIR . '/inc/share-buttons.php';

/* ============================================
   内置小工具：产品列表（卡片式）
   ============================================ */
class ZHUOER_Product_Widget extends WP_Widget {
    public function __construct() {
        parent::__construct( 'zhuoer_product_widget', __( '【ZHUOER】产品列表', 'zhuoer' ),
            array( 'description' => __( '卡片式产品列表：上图/品名/评分/价格/加入购物车', 'zhuoer' ) ) );
    }
    private function stars( $r ) {
        $f=floor($r); $h=($r-$f>=0.5)?1:0; $e=5-$f-$h; $o='';
        for($i=0;$i<$f;$i++)$o.='<i class="zpw-s zpw-s-f"></i>';
        if($h)$o.='<i class="zpw-s zpw-s-h"></i>';
        for($i=0;$i<$e;$i++)$o.='<i class="zpw-s zpw-s-e"></i>';
        return "<span class=\"zpw-stars\">$o</span>";
    }
    public function widget( $args, $instance ) {
        echo $args['before_widget'];
        $title = ! empty( $instance['title'] ) ? $instance['title'] : '';
        if ( $title ) echo $args['before_title'] . esc_html( $title ) . $args['after_title'];
        $n = ! empty( $instance['count'] ) ? absint( $instance['count'] ) : 6;
        $cat = $instance['category'] ?? '';
        $q = array( 'post_type'=>'product','posts_per_page'=>$n,'post_status'=>'publish','orderby'=>'date','order'=>'DESC' );
        if($cat) $q['tax_query']=array(array('taxonomy'=>'product_cat','field'=>'slug','terms'=>$cat));
        $cache_key = 'zhuoer_prod_widget_' . md5(serialize($q));
        $html = get_transient($cache_key);
        if (false === $html) {
            ob_start();
            $p = new WP_Query($q);
            if($p->have_posts()){
            echo '<div class="zpw-grid">';
            while($p->have_posts()): $p->the_post();
                global $product;
                $s = $product->get_short_description() ? wp_trim_words($product->get_short_description(),10,'…') : '';
                $r = $product->get_average_rating();
                $img = get_the_post_thumbnail(get_the_ID(),'medium',array('class'=>'zpw-img'));
                ?>
                <div class="zpw-card">
                    <a href="<?=esc_url(get_permalink())?>" class="zpw-thumb-wrap"><?=$img ?: '<div class="zpw-noimg"></div>'?></a>
                    <div class="zpw-body">
                        <div class="zpw-head">
                            <a href="<?=esc_url(get_permalink())?>" class="zpw-name"><?=wp_kses_post(get_the_title())?></a>
                            <?php if($r>0) echo $this->stars($r); ?>
                        </div>
                        <?php if($s): ?><p class="zpw-desc"><?=esc_html($s)?></p><?php endif; ?>
                        <div class="zpw-foot">
                            <span class="zpw-price"><?=$product->get_price_html()?></span>
                            <a href="<?=esc_url(get_permalink())?>" class="zpw-cart-btn"><?php esc_html_e( '立即购买', 'zhuoer' ); ?></a>
                        </div>
                    </div>
                </div>
                <?php
            endwhile;
            echo '</div>'; wp_reset_postdata();
        }
        echo $args['after_widget'];
            $html = ob_get_clean();
            set_transient($cache_key, $html, 12 * HOUR_IN_SECONDS);
        }
        echo $html;
    }
    public function form($i){
        $t=$i['title']??''; $c=!empty($i['count'])?absint($i['count']):4; $cat=$i['category']??'';
        ?><p><label><?php esc_html_e( '标题（留空隐藏）：', 'zhuoer' ); ?></label><input class="widefat" name="<?=esc_attr($this->get_field_name('title'))?>" value="<?=esc_attr($t)?>"></p>
        <p><label><?php esc_html_e( '数量：', 'zhuoer' ); ?></label><input class="widefat" type="number" min="1" max="8" name="<?=esc_attr($this->get_field_name('count'))?>" value="<?=esc_attr($c)?>"></p>
        <p><label><?php esc_html_e( '分类别名（留空=全部）：', 'zhuoer' ); ?></label><input class="widefat" name="<?=esc_attr($this->get_field_name('category'))?>" value="<?=esc_attr($cat)?>"></p><?php
    }
    public function update($n,$o){
        return array('title'=>sanitize_text_field($n['title']),'count'=>absint($n['count']),'category'=>sanitize_text_field($n['category']));
    }
}
add_action('widgets_init',function(){register_widget('ZHUOER_Product_Widget');});
