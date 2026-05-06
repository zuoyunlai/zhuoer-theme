<?php
/**
 * SEO Module — China-optimized (Baidu, WeChat, no Google)
 *
 * @package ZHUOER
 */

if ( ! defined( 'ABSPATH' ) ) {
    die;
}

// 移除 WordPress 自带的 canonical，避免重复输出
remove_action( 'wp_head', 'rel_canonical' );

/* ── Meta Tags — Homepage ── */
add_action( 'wp_head', 'zhuoer_meta_tags', 1 );
function zhuoer_meta_tags() {
    if ( ! is_front_page() ) {
        return;
    }

    $title       = get_option( 'zhuoer_home_title', '' );
    $description = get_option( 'zhuoer_home_description', '' );
    $og_image    = get_option( 'zhuoer_og_image', '' );

    if ( ! $title )       { $title       = get_bloginfo( 'name' ); }
    if ( ! $description ) { $description = get_bloginfo( 'description' ); }

    $site_name   = get_bloginfo( 'name' );
    $og_home_url    = home_url( '/' );
    ?>
    <meta name="description" content="<?php echo esc_attr( $description ); ?>" />
    <!-- SEO -->
    <meta name="robots" content="index, follow" />
    <meta name="author" content="<?php echo esc_attr( $site_name ); ?>" />

    <!-- Baidu Site Verification -->
    <?php
    $baidu_verify = get_option( 'zhuoer_baidu_verify', '' );
    if ( $baidu_verify ) {
        echo '<meta name="baidu-site-verification" content="' . esc_attr( $baidu_verify ) . '" />' . "\n";
    }
    ?>

    <!-- Open Graph (WeChat & Social) -->
    <meta property="og:title" content="<?php echo esc_attr( $title ); ?>" />
    <meta property="og:description" content="<?php echo esc_attr( $description ); ?>" />
    <meta property="og:site_name" content="<?php echo esc_attr( $site_name ); ?>" />
    <meta property="og:url" content="<?php echo esc_url( $og_home_url ); ?>" />
    <?php if ( $og_image ) : ?>
    <meta property="og:image" content="<?php echo esc_url( $og_image ); ?>" />
    <meta property="og:image:width" content="1200" />
    <meta property="og:image:height" content="630" />
    <?php endif; ?>

    <!-- WeChat-specific OG -->
    <meta property="og:type" content="website" />
    <meta property="og:locale" content="<?php echo esc_attr( str_replace( '_', '-', get_locale() ) ); ?>" />
    <meta property="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="<?php echo esc_attr( $title ); ?>" />
    <meta name="twitter:description" content="<?php echo esc_attr( $description ); ?>" />
    <?php if ( $og_image ) : ?>
    <meta name="twitter:image" content="<?php echo esc_url( $og_image ); ?>" />
    <?php endif; ?>

    <!-- WeChat Card (WeChat sharing) -->
    <?php if ( $og_image ) : ?>
    <meta itemprop="image" content="<?php echo esc_url( $og_image ); ?>" />
    <?php endif; ?>

    <!-- Mobile QQ Sharing -->
    <meta property="qzone.title" content="<?php echo esc_attr( $title ); ?>" />
    <meta property="qzone.desc" content="<?php echo esc_attr( $description ); ?>" />
    <?php if ( $og_image ) : ?>
    <meta property="qzone.image" content="<?php echo esc_url( $og_image ); ?>" />
    <?php endif; ?>
    <?php
}

/* ── Meta Tags — Single Post (WeChat optimized) ── */
add_action( 'wp_head', 'zhuoer_single_meta_tags', 1 );
function zhuoer_single_meta_tags() {
    if ( ! is_singular( array( 'post', 'page' ) ) ) {
        return;
    }

    global $post;
    $title       = get_the_title();
    $description = get_the_excerpt() ?: wp_strip_all_tags( get_the_content() );
    $categories  = get_the_category();
    $description = mb_substr( $description, 0, 150, 'utf-8' );

    // 添加标准的 meta description
    if ( $description ) {
        echo '<meta name="description" content="' . esc_attr( $description ) . '" />' . "\n";
    }
    $og_image    = '';
    if ( has_post_thumbnail() ) {
        $thumb_src = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );
        if ( $thumb_src && isset( $thumb_src[0] ) ) {
            $og_image = $thumb_src[0];
        }
    }
    if ( ! $og_image ) {
        $og_image = get_option( 'zhuoer_og_image', '' );
    }

    $site_name = get_bloginfo( 'name' );
    $post_url  = get_permalink();
    ?>
    <meta property="og:title" content="<?php echo esc_attr( $title ); ?>" />
    <meta property="og:description" content="<?php echo esc_attr( $description ); ?>" />
    <meta property="og:site_name" content="<?php echo esc_attr( $site_name ); ?>" />
    <meta property="og:url" content="<?php echo esc_url( $post_url ); ?>" />
    <meta property="og:type" content="article" />
    <meta property="article:section" content="<?php echo esc_attr( ! empty( $categories[0] ) ? $categories[0]->name : '' ); ?>" />
    <meta property="article:published_time" content="<?php echo esc_attr( get_the_date( 'c' ) ); ?>" />
    <meta property="article:modified_time" content="<?php echo esc_attr( get_the_modified_date( 'c' ) ); ?>" />
    <?php if ( $og_image ) : ?>
    <meta property="og:image" content="<?php echo esc_url( $og_image ); ?>" />
    <meta property="og:image:width" content="1200" />
    <meta property="og:image:height" content="630" />
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:image" content="<?php echo esc_url( $og_image ); ?>" />
    <?php else : ?>
    <meta name="twitter:card" content="summary" />
    <?php endif; ?>
    <meta name="twitter:title" content="<?php echo esc_attr( $title ); ?>" />
    <meta name="twitter:description" content="<?php echo esc_attr( $description ); ?>" />
    <?php
}

/* ── Meta Tags — Archive Pages (Category, Tag, Author, Date) ── */
add_action( 'wp_head', 'zhuoer_archive_meta_tags', 1 );
function zhuoer_archive_meta_tags() {
    if ( ! is_archive() && ! is_search() && ! is_404() ) {
        return;
    }

    $title = '';
    $description = '';

    if ( is_category() ) {
        $title = single_cat_title( '', false );
        $description = category_description();
    } elseif ( is_tag() ) {
        $title = single_tag_title( '', false );
        $description = tag_description();
    } elseif ( is_author() ) {
        $title = get_the_author();
        $description = get_the_author_meta( 'description' );
    } elseif ( is_date() ) {
        if ( is_year() ) {
            $title = get_the_date( _x( 'Y', 'yearly archives date format', 'zhuoer' ) );
        } elseif ( is_month() ) {
            $title = get_the_date( _x( 'F Y', 'monthly archives date format', 'zhuoer' ) );
        } else {
            $title = get_the_date( _x( 'F j, Y', 'daily archives date format', 'zhuoer' ) );
        }
    } elseif ( is_search() ) {
        $title = get_search_query();
        $description = sprintf( __( '%s 的搜索结果', 'zhuoer' ), $title );
    } elseif ( is_404() ) {
        $title = __( '页面未找到', 'zhuoer' );
        $description = __( '您访问的页面不存在或已被删除。', 'zhuoer' );
    }

    // 添加分页信息
    if ( is_paged() ) {
        $page = get_query_var( 'paged' );
        $title .= sprintf( __( ' - 第 %d 页', 'zhuoer' ), $page );
    }

    $site_name = get_bloginfo( 'name' );
    ?>
    <meta property="og:title" content="<?php echo esc_attr( $title ); ?>" />
    <meta property="og:site_name" content="<?php echo esc_attr( $site_name ); ?>" />
    <meta property="og:type" content="website" />
    <?php if ( $description ) : ?>
    <meta name="description" content="<?php echo esc_attr( $description ); ?>" />
    <meta property="og:description" content="<?php echo esc_attr( $description ); ?>" />
    <?php endif; ?>
    <meta property="og:url" content="<?php echo esc_url( get_self_link() ); ?>" />
    <meta name="twitter:card" content="summary" />
    <meta name="twitter:title" content="<?php echo esc_attr( $title ); ?>" />
    <?php
}

/* ── Canonical ── */
add_action( 'wp_head', 'zhuoer_canonical', 5 );
function zhuoer_canonical() {
    // 分页页面添加 noindex，避免重复内容分散权重
    if ( is_paged() && ( is_archive() || is_home() || is_search() ) ) {
        echo '<meta name="robots" content="noindex, follow" />' . "\n";
    }

    if ( is_singular() ) {
        echo '<link rel="canonical" href="' . esc_url( get_permalink() ) . '" />' . "\n";
    } elseif ( is_front_page() || is_home() ) {
        echo '<link rel="canonical" href="' . esc_url( home_url( '/' ) ) . '" />' . "\n";
    } elseif ( is_archive() || is_search() ) {
        // 归档页面 canonical
        $page = get_query_var( 'paged' );
        if ( $page > 1 ) {
            echo '<link rel="canonical" href="' . esc_url( get_self_link() ) . '" />' . "\n";
        } else {
            echo '<link rel="canonical" href="' . esc_url( get_self_link() ) . '" />' . "\n";
        }
    }
}

/* ── JSON-LD Schema ── */
add_action( 'wp_head', 'zhuoer_json_ld', 1 );
function zhuoer_json_ld() {
    if ( is_front_page() ) {
        $site_name = get_bloginfo( 'name' );
        $logo_url  = '';
        $custom_logo_id = get_theme_mod( 'custom_logo' );
        if ( $custom_logo_id ) {
            $logo_data = wp_get_attachment_image_src( $custom_logo_id, 'full' );
            if ( $logo_data ) {
                $logo_url = $logo_data[0];
            }
        }

        $website = array(
            '@context'    => 'https://schema.org',
            '@type'       => 'WebSite',
            'name'        => $site_name,
            'description' => get_bloginfo( 'description' ),
            'url'         => home_url( '/' ),
        );

        $organization = array(
            '@context' => 'https://schema.org',
            '@type'    => 'Organization',
            'name'     => $site_name,
            'url'      => home_url( '/' ),
        );
        if ( $logo_url ) {
            $organization['logo'] = array(
                '@type' => 'ImageObject',
                'url'   => $logo_url,
            );
        }

        echo '<script type="application/ld+json">' . wp_json_encode( $website, JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
        echo '<script type="application/ld+json">' . wp_json_encode( $organization, JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
        return;
    }

    if ( is_singular( 'post' ) ) {
        global $post;
        $author_name = get_the_author_meta( 'display_name', $post->post_author );
        $categories  = get_the_category();
        $cat_name    = ! empty( $categories[0] ) ? $categories[0]->name : '';
        $cat_url     = ! empty( $categories[0] ) ? get_category_link( $categories[0]->term_id ) : '';

        $content    = get_post_field( 'post_content', $post->ID );
        $plain      = wp_strip_all_tags( $content );
        $chinese    = mb_strlen( preg_replace( '/[^\x{4e00}-\x{9fff}]/u', '', $plain ), 'utf-8' );
        $english    = str_word_count( $plain );
        $word_count = $chinese + $english;
        // timeRequired in ISO 8601 duration: PTxM
        $minutes    = ceil( $word_count / 300 );
        $hours      = intdiv( $minutes, 60 );
        $mins       = $minutes % 60;
        if ( $hours > 0 ) {
            $time_required = 'PT' . $hours . 'H' . $mins . 'M';
        } else {
            $time_required = 'PT' . $mins . 'M';
        }

        $schema = array(
            '@context'       => 'https://schema.org',
            '@type'          => 'Article',
            'headline'       => get_the_title(),
            'datePublished'  => get_the_date( 'c' ),
            'dateModified'   => get_the_modified_date( 'c' ),
            'author'         => array( '@type' => 'Person', 'name' => $author_name ),
            'publisher'       => array( '@type' => 'Organization', 'name' => get_bloginfo( 'name' ) ),
            'url'            => get_permalink(),
            'wordCount'     => $word_count,
            'timeRequired'   => $time_required,
        );

        if ( has_post_thumbnail() ) {
            $thumb_src = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );
            if ( $thumb_src && isset( $thumb_src[0] ) ) {
                $schema['image'] = array( '@type' => 'ImageObject', 'url' => $thumb_src[0] );
            }
        }

        echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";

        // Breadcrumb JSON-LD (Baidu & Google both love this)
        if ( ! empty( $cat_name ) ) {
            $breadcrumb = array(
                '@context'        => 'https://schema.org',
                '@type'          => 'BreadcrumbList',
                'itemListElement' => array(
                    0 => array(
                        '@type'    => 'ListItem',
                        'position' => 1,
                        'name'     => get_bloginfo( 'name' ),
                        'item'     => home_url( '/' ),
                    ),
                    1 => array(
                        '@type'    => 'ListItem',
                        'position' => 2,
                        'name'     => $cat_name,
                        'item'     => $cat_url,
                    ),
                    2 => array(
                        '@type'    => 'ListItem',
                        'position' => 3,
                        'name'     => get_the_title(),
                        'item'     => get_permalink(),
                    ),
                ),
            );
            echo '<script type="application/ld+json">' . wp_json_encode( $breadcrumb, JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
        }
    }
}

/* ── Baidu Tongji (China analytics) ── */
add_action( 'wp_head', 'zhuoer_baidu_tongji', 99 );
function zhuoer_baidu_tongji() {
    $baidu_id = get_option( 'zhuoer_baidu_tongji_id', '' );
    if ( empty( $baidu_id ) ) {
        return;
    }
    ?>
    <script>
    var _hmt = _hmt || [];
    (function() {
      var hm = document.createElement("script");
      hm.src = "https://hm.baidu.com/hm.js?<?php echo esc_attr( $baidu_id ); ?>";
      var s = document.getElementsByTagName("script")[0];
      s.parentNode.insertBefore(hm, s);
    })();
    </script>
    <?php
}

/* ── Optional Google Analytics (keep for overseas visitors) ── */
add_action( 'wp_head', 'zhuoer_google_analytics', 100 );
function zhuoer_google_analytics() {
    $ga_id = get_option( 'zhuoer_ga_id', '' );
    if ( empty( $ga_id ) ) {
        return;
    }
    ?>
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo esc_attr( $ga_id ); ?>"></script>
    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', <?php echo wp_json_encode( $ga_id ); ?>);
    </script>
    <?php
}
