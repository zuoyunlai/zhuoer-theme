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

define( 'ZHUOER_VERSION', '1.0.67' );
define( 'ZHUOER_DIR', get_template_directory() );

/* ─── Theme Setup ─── */
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
        array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' )
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

    add_theme_support(
        'editor-color-palette',
        array(
            array( 'name' => __( 'Primary Blue', 'zhuoer' ), 'slug' => 'primary', 'color' => '#1a73e8' ),
            array( 'name' => __( 'Text Dark',   'zhuoer' ), 'slug' => 'dark',   'color' => '#202124' ),
            array( 'name' => __( 'Text Muted',  'zhuoer' ), 'slug' => 'muted',  'color' => '#5f6368' ),
            array( 'name' => __( 'Surface',     'zhuoer' ), 'slug' => 'surface','color' => '#fafafa' ),
        )
    );

    add_theme_support(
        'editor-font-sizes',
        array(
            array( 'name' => __( 'Small',  'zhuoer' ), 'slug' => 'small',  'size' => 14 ),
            array( 'name' => __( 'Normal', 'zhuoer' ), 'slug' => 'normal', 'size' => 16 ),
            array( 'name' => __( 'Large',  'zhuoer' ), 'slug' => 'large',  'size' => 20 ),
            array( 'name' => __( 'Huge',   'zhuoer' ), 'slug' => 'huge',   'size' => 28 ),
        )
    );
}
add_action( 'after_setup_theme', 'zhuoer_setup' );

function zhuoer_content_width() {
    $GLOBALS['content_width'] = apply_filters( 'zhuoer_content_width', 720 );
}
add_action( 'after_setup_theme', 'zhuoer_content_width', 0 );

/* ─── Widget Areas ─── */
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

/* ─── Enqueue ─── */
function zhuoer_scripts() {
    /* Google Fonts */
    $fonts_url = 'https://fonts.googleapis.com/css2?family=Noto+Sans+SC:wght@400;500;700&display=block';
    wp_enqueue_style( 'zhuoer-google-fonts', $fonts_url, array(), null );

    /* Main theme stylesheet */
    wp_enqueue_style( 'zhuoer-style', get_stylesheet_uri(), array( 'zhuoer-google-fonts' ), ZHUOER_VERSION );
    wp_enqueue_style( 'zhuoer-print', get_template_directory_uri() . '/assets/css/print.css', array( 'zhuoer-style' ), ZHUOER_VERSION, 'print' );
    wp_enqueue_style( 'zhuoer-mobile', get_template_directory_uri() . '/assets/css/mobile.css', array( 'zhuoer-style' ), ZHUOER_VERSION );
    wp_enqueue_style( 'zhuoer-header', get_template_directory_uri() . '/assets/css/header.css', array( 'zhuoer-style' ), ZHUOER_VERSION );
    wp_enqueue_script( 'zhuoer-main', get_template_directory_uri() . '/assets/js/main.js', array(), ZHUOER_VERSION, true );

    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
        wp_enqueue_script( 'comment-reply' );
    }
}
add_action( 'wp_enqueue_scripts', 'zhuoer_scripts' );

function zhuoer_editor_assets() {
    wp_enqueue_style( 'zhuoer-editor-style', get_template_directory_uri() . '/assets/css/editor-style.css', array(), ZHUOER_VERSION );
}
add_action( 'enqueue_block_editor_assets', 'zhuoer_editor_assets' );

/* ─── SVG Icons ─── */
function zhuoer_icon( $icon ) {
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
function zhuoer_excerpt_length( $length ) {
    return 35;
}
add_filter( 'excerpt_length', 'zhuoer_excerpt_length' );

function zhuoer_excerpt_more( $more ) {
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
add_action( 'after_setup_theme', 'zhuoer_register_block_styles' );

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
add_action( 'after_setup_theme', 'zhuoer_register_block_patterns' );
