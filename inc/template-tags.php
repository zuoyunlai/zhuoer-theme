<?php
/**
 * Template Tags — Helper functions for templates
 *
 * @package ZHUOER
 */

if ( ! defined( 'ABSPATH' ) ) {
    die;
}

// =============================================
// POST META
// =============================================
if ( ! function_exists( 'zhuoer_posted_on' ) ) :
    function zhuoer_posted_on( $echo = true ) {
        $time = '<time datetime="' . esc_attr( get_the_date( 'c' ) ) . '">' . esc_html( get_the_date() ) . '</time>';
        $icon = zhuoer_icon( 'clock' );
        $html = '<span class="posted-on">' . $icon . $time . '</span>';
        if ( $echo ) {
            echo $html;
        }
        return $html;
    }
endif;

if ( ! function_exists( 'zhuoer_posted_by' ) ) :
    function zhuoer_posted_by( $echo = true ) {
        $author_id = get_post_field( 'post_author', get_the_ID() );
        $name      = get_the_author_meta( 'display_name', $author_id );
        $url       = get_author_posts_url( $author_id );
        $icon      = zhuoer_icon( 'user' );
        $html      = '<span class="posted-by">' . $icon . '<a href="' . esc_url( $url ) . '">' . esc_html( $name ) . '</a></span>';
        if ( $echo ) {
            echo $html;
        }
        return $html;
    }
endif;

if ( ! function_exists( 'zhuoer_posted_in' ) ) :
    function zhuoer_posted_in( $echo = true ) {
        $categories = get_the_category();
        if ( empty( $categories ) ) {
            return '';
        }
        $icon   = zhuoer_icon( 'folder' );
        $links  = array();
        foreach ( $categories as $cat ) {
            $links[] = '<a href="' . esc_url( get_category_link( $cat->term_id ) ) . '">' . esc_html( $cat->name ) . '</a>';
        }
        $html = '<span class="posted-in">' . $icon . implode( ', ', $links ) . '</span>';
        if ( $echo ) {
            echo $html;
        }
        return $html;
    }
endif;

// =============================================
// POST THUMBNAIL
// =============================================
if ( ! function_exists( 'zhuoer_post_thumbnail' ) ) :
    function zhuoer_post_thumbnail() {
        if ( ! has_post_thumbnail() ) {
            return;
        }
        $show = get_option( 'zhuoer_show_featured_image', '1' ) === '1';
        if ( ! $show && is_singular( 'post' ) ) {
            return;
        }
        // 首图优先加载，其余懒加载
        $attr = array(
            'alt'        => the_title_attribute( 'echo=0' ),
            'srcset'     => wp_get_attachment_image_srcset( get_post_thumbnail_id(), 'zhuoer-thumbnail' ),
            'sizes'      => '(max-width: 768px) 100vw, (max-width: 1024px) 50vw, 540px',
            'loading'    => 'lazy',
        );
        if ( is_singular() ) {
            $attr['fetchpriority'] = 'high';
            $attr['loading']       = 'eager';
        }
        ?>
        <div class="zhuoer-container">
            <div class="zhuoer-thumbnail">
                <?php the_post_thumbnail( 'zhuoer-thumbnail', $attr ); ?>
            </div>
        </div>
        <?php
    }
endif;

// =============================================
// READ TIME
// =============================================
if ( ! function_exists( 'zhuoer_reading_time' ) ) :
    function zhuoer_reading_time( $post_id = null ) {
        $post_id   = $post_id ? $post_id : get_the_ID();
        $content   = get_post_field( 'post_content', $post_id );
        $plain     = wp_strip_all_tags( $content );
        // 中文字符按字数算，英文单词独立统计，合并计算阅读时间
        $chinese_chars = mb_strlen( preg_replace( '/[^\x{4e00}-\x{9fff}]/u', '', $plain ), 'utf-8' );
        $english_words = str_word_count( $plain );
        $total_units   = $chinese_chars + $english_words;
        $minutes       = ceil( $total_units / 300 );
        $text      = $minutes <= 1 ? __( '1 min read', 'zhuoer' ) : sprintf( __( '%d min read', 'zhuoer' ), $minutes );
        return '<span class="reading-time">' . zhuoer_icon( 'clock' ) . esc_html( $text ) . '</span>';
    }
endif;

// =============================================
// TAG LINKS
// =============================================
if ( ! function_exists( 'zhuoer_entry_tags' ) ) :
    function zhuoer_entry_tags( $before = '', $sep = '', $after = '', $echo = true ) {
        $tags = get_the_tags();
        if ( ! $tags ) {
            return '';
        }
        $links = array();
        foreach ( $tags as $tag ) {
            $links[] = '<li><a href="' . esc_url( get_tag_link( $tag->term_id ) ) . '" rel="tag">' . esc_html( $tag->name ) . '</a></li>';
        }
        $html = $before . '<ul class="zhuoer-entry__tags">' . implode( $sep, $links ) . '</ul>' . $after;
        if ( $echo ) {
            echo $html;
        }
        return $html;
    }
endif;

// =============================================
// PAGINATION
// =============================================
if ( ! function_exists( 'zhuoer_posts_pagination' ) ) :
    function zhuoer_posts_pagination() {
        global $wp_query;
        if ( $wp_query->max_num_pages < 2 ) {
            return;
        }
        $total   = $wp_query->max_num_pages;
        $current = max( 1, get_query_var( 'paged' ) );
        $base    = esc_url_raw( str_replace( 999999999, '%#%', get_pagenum_link( 999999999, false ) ) );

        $pagination = paginate_links(
            array(
                'base'     => $base,
                'format'   => '?paged=%#%',
                'current'  => $current,
                'total'    => $total,
                'mid_size' => 2,
                'end_size' => 1,
                'type'     => 'plain',
                'prev_text' => '&laquo;',
                'next_text' => '&raquo;',
            )
        );

        if ( $pagination ) {
            echo '<nav class="zhuoer-pagination" aria-label="Pagination">' . $pagination . '</nav>';
        }
    }
endif;

// =============================================
// POST NAVIGATION
// =============================================
if ( ! function_exists( 'zhuoer_post_navigation' ) ) :
    function zhuoer_post_navigation() {
        $prev = get_previous_post();
        $next = get_next_post();
        if ( ! $prev && ! $next ) {
            return;
        }
        ?>
        <nav class="zhuoer-post-nav" aria-label="Post navigation">
            <?php if ( $prev ) : ?>
                <div class="zhuoer-post-nav__item">
                    <a href="<?php echo esc_url( get_permalink( $prev ) ); ?>" rel="prev">
                        <span class="zhuoer-post-nav__label"><?php esc_html_e( 'Previous', 'zhuoer' ); ?></span>
                        <span class="zhuoer-post-nav__title"><?php echo esc_html( get_the_title( $prev ) ); ?></span>
                    </a>
                </div>
            <?php endif; ?>
            <?php if ( $next ) : ?>
                <div class="zhuoer-post-nav__item zhuoer-post-nav__next">
                    <a href="<?php echo esc_url( get_permalink( $next ) ); ?>" rel="next">
                        <span class="zhuoer-post-nav__label"><?php esc_html_e( 'Next', 'zhuoer' ); ?></span>
                        <span class="zhuoer-post-nav__title"><?php echo esc_html( get_the_title( $next ) ); ?></span>
                    </a>
                </div>
            <?php endif; ?>
        </nav>
        <?php
    }
endif;

// =============================================
// AUTHOR BIO BOX
// =============================================
if ( ! function_exists( 'zhuoer_author_bio' ) ) :
    function zhuoer_author_bio() {
        if ( ! is_singular( 'post' ) ) {
            return;
        }
        $show      = get_option( 'zhuoer_show_author_bio', '1' ) === '1';
        if ( ! $show ) {
            return;
        }
        $author_id = get_post_field( 'post_author', get_the_ID() );
        $name      = get_the_author_meta( 'display_name', $author_id );
        $bio       = get_the_author_meta( 'description', $author_id );
        $url       = get_author_posts_url( $author_id );
        $avatar    = get_avatar( $author_id, 80, '', $name, array( 'class' => 'zhuoer-author-bio__avatar' ) );

        if ( ! $bio ) {
            return;
        }
        ?>
        <div class="zhuoer-author-bio">
            <?php echo $avatar; ?>
            <div class="zhuoer-author-bio__content">
                <p class="zhuoer-author-bio__name">
                    <a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $name ); ?></a>
                </p>
                <p class="zhuoer-author-bio__text"><?php echo esc_html( $bio ); ?></p>
            </div>
        </div>
        <?php
    }
endif;

// =============================================
// TABLE OF CONTENTS (文章目录)
// =============================================
if ( ! function_exists( 'zhuoer_toc' ) ) :
    function zhuoer_toc( $post_id = null ) {
        $post_id = $post_id ? $post_id : get_the_ID();
        $content = get_post_field( 'post_content', $post_id );
        if ( empty( $content ) ) {
            return '';
        }
        // 提取 h2、h3（支持有/无 id 属性）
        preg_match_all(
            '/<h([23])(?:[^>]*\sid=["\']([^"\']+)["\'])?[^>]*>(.*?)<\/h\1>/si',
            $content, $matches, PREG_SET_ORDER
        );
        if ( count( $matches ) < 2 ) {
            return ''; // 标题少于2个不显示目录
        }
        $html  = '<nav class="zhuoer-toc" aria-label="文章目录">';
        $html .= '<details open>';
        $html .= '<summary class="zhuoer-toc__title">📑 目录</summary>';
        $html .= '<ol class="zhuoer-toc__list">';
        $idx  = 0;
        foreach ( $matches as $m ) {
            $idx++;
            $level = $m[1];
            $text  = wp_strip_all_tags( $m[3] );
            $id    = ! empty( $m[2] ) ? $m[2] : 'zhuoer-toc-' . $idx;
            $class = $level === '2' ? 'zhuoer-toc__item zhuoer-toc__item--h2' : 'zhuoer-toc__item zhuoer-toc__item--h3';
            $html .= '<li class="' . esc_attr( $class ) . '"><a href="#' . esc_attr( $id ) . '">' . esc_html( $text ) . '</a></li>';
        }
        $html .= '</ol></details></nav>';
        return $html;
    }
endif;

// 给文章 h2/h3 自动补 ID（与目录锚点对应）
add_filter( 'the_content', 'zhuoer_add_heading_ids', 1 );
if ( ! function_exists( 'zhuoer_add_heading_ids' ) ) :
    function zhuoer_add_heading_ids( $content ) {
        if ( ! is_singular() ) return $content;
        $idx = 0;
        return preg_replace_callback(
            '/<h([23])([^>]*)>(.*?)<\/h\1>/si',
            function ( $m ) use ( &$idx ) {
                $idx++;
                $attrs = $m[2];
                // 已有 id 则保留
                if ( preg_match( '/\bid\s*=\s*["\']/i', $attrs ) ) {
                    return $m[0];
                }
                return '<h' . $m[1] . $attrs . ' id="zhuoer-toc-' . $idx . '">' . $m[3] . '</h' . $m[1] . '>';
            },
            $content
        );
    }
endif;

// =============================================
// SOCIAL LINKS
// =============================================
if ( ! function_exists( 'zhuoer_social_links' ) ) :
    function zhuoer_social_links( $echo = true ) {
        // 用 get_option 读取后台设置（与 customizer.php 的 register_setting 配套）
        $social_fields = array(
            'weibo'       => array(
                'label' => '微博',
                'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M10.098 20.323c-3.977.391-7.414-1.406-7.672-4.02-.259-2.609 2.759-5.047 6.74-5.441 3.979-.394 7.413 1.404 7.671 4.018.259 2.6-2.759 5.049-6.739 5.443zM9.05 14.845c-1.24.125-2.238.727-2.228 1.346.012.619.998 1.117 2.236 1.238 1.24.12 2.238-.374 2.224-1.052-.013-.677-1.001-1.161-2.232-1.532zm.372-2.224c-1.786.176-3.398 1.094-3.379 2.048.019.952 1.633 1.659 3.416 1.575 1.784-.086 3.392-.999 3.371-1.951-.02-.952-1.616-1.672-3.408-1.672zm.165-2.448c-2.639.242-4.912 1.61-4.906 3.03.006 1.42 2.295 2.48 4.942 2.24 2.643-.24 4.912-1.61 4.906-3.03-.006-1.42-2.295-2.48-4.942-2.24z"/></svg>',
            ),
            'zhihu'       => array(
                'label' => '知乎',
                'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M7.86 2h8.28L22 7.86v8.28L16.14 22H7.86L2 16.14V7.86L7.86 2zm4.14 12.64l-2.43 2.43 1.06 1.06 2.43-2.43-1.06-1.06zm0-4.28l-2.43-2.43-1.06 1.06 2.43 2.43 1.06-1.06zm0-4.28l-2.43-2.43-1.06 1.06 2.43 2.43 1.06-1.06zm5.32 4.28l1.45 1.45 1.06-1.06-1.45-1.45-1.06 1.06zm0 4.28l1.45 1.45 1.06-1.06-1.45-1.45-1.06 1.06z"/></svg>',
            ),
            'bilibili'    => array(
                'label' => 'B站',
                'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M17.813 4.653h.854c1.51.054 2.769.578 3.773 1.574 1.004.995 1.524 2.249 1.56 3.76v7.36c-.036 1.51-.556 2.769-1.56 3.773s-2.262 1.524-3.773 1.56H5.333c-1.51-.036-2.769-.556-3.773-1.56S.036 18.858 0 17.347v-7.36c.036-1.511.556-2.765 1.56-3.76 1.004-.996 2.262-1.52 3.773-1.574h.774l-1.174-1.12a1.234 1.234 0 0 1-.373-.906c0-.356.124-.658.373-.907l.027-.027c.267-.249.573-.373.92-.373.347 0 .653.124.92.373L9.653 4.44c.071.071.134.142.187.213h4.267a.94.94 0 0 1 .16-.213l2.853-2.747c.267-.249.573-.373.92-.373.347 0 .662.151.929.4.267.249.391.551.391.907 0 .355-.124.657-.373.906zM5.333 7.24c-.746.018-1.373.276-1.88.773-.506.498-.769 1.13-.786 1.894v7.52c.017.764.28 1.395.786 1.893.507.498 1.134.756 1.88.773h13.334c.746-.017 1.373-.275 1.88-.773.506-.498.769-1.129.786-1.893v-7.52c-.017-.765-.28-1.396-.786-1.894-.507-.497-1.134-.755-1.88-.773zM8 11.107c.373 0 .684.124.933.373.25.249.383.569.383.96s-.124.71-.383.96c-.249.249-.56.373-.933.373s-.684-.124-.933-.373c-.249-.25-.383-.57-.383-.96s.124-.711.383-.96c.249-.249.56-.373.933-.373zm8.667 1.333c-.182.373-.43.66-.747.867-.316.206-.68.316-1.093.327H10.56c-.414-.011-.777-.121-1.093-.327a1.876 1.876 0 0 1-.747-.867 2.575 2.575 0 0 1-.253-.987c-.017-.373-.009-.733.027-1.08h5.413c.036.347.045.707.027 1.08-.018.374-.1.701-.253.987z"/></svg>',
            ),
            'xiaohongshu' => array(
                'label' => '小红书',
                'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.73 14.14c-.19.55-.66.95-1.21.95H8.48c-.55 0-1.02-.4-1.21-.95-.12-.35-.17-.72-.17-1.09V10.5c0-.37.05-.74.17-1.09.19-.55.66-.95 1.21-.95h6.24c.55 0 1.02.4 1.21.95.12.35.17.72.17 1.09v4.55c0 .37-.05.74-.17 1.09zM9.5 8.5h5v.5h-5v-.5zm.75 3h3.5v1.5H10.25V11.5zm5.5 0h.75v1.5H15.5V11.5h.25v.5h-4v-.5h.25v-1.5h-.25v-.5h4v.5h-.25V11.5z"/></svg>',
            ),
            'github'      => array(
                'label' => 'GitHub',
                'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 .297c-6.63 0-12 5.373-12 12 0 5.303 3.438 9.8 8.205 11.385.6.113.82-.258.82-.577 0-.285-.01-1.04-.015-2.04-3.338.724-4.042-1.61-4.042-1.61C4.422 18.07 3.633 17.7 3.633 17.7c-1.087-.744.084-.729.084-.729 1.205.084 1.838 1.236 1.838 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.93 0-1.31.465-2.38 1.235-3.22-.135-.303-.54-1.523.105-3.176 0 0 1.005-.322 3.3 1.23.96-.267 1.98-.399 3-.405 1.02.006 2.04.138 3 .405 2.28-1.552 3.285-1.23 3.285-1.23.645 1.653.24 2.873.12 3.176.765.84 1.23 1.91 1.23 3.22 0 4.61-2.805 5.625-5.475 5.92.42.36.81 1.096.81 2.22 0 1.606-.015 2.896-.015 3.286 0 .315.21.69.825.57C20.565 22.092 24 17.592 24 12.297c0-6.627-5.373-12-12-12"/></svg>',
            ),
            'twitter'     => array(
                'label' => 'Twitter / X',
                'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>',
            ),
            'facebook'    => array(
                'label' => 'Facebook',
                'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>',
            ),
            'instagram'   => array(
                'label' => 'Instagram',
                'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 1 0 0 12.324 6.162 6.162 0 0 0 0-12.324zM12 16a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm6.406-11.845a1.44 1.44 0 1 0 0 2.881 1.44 1.44 0 0 0 0-2.881z"/></svg>',
            ),
            'linkedin'    => array(
                'label' => 'LinkedIn',
                'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>',
            ),
        );

        $links = array();
        foreach ( $social_fields as $key => $info ) {
            $url = get_option( "zhuoer_social_{$key}", '' );
            if ( empty( $url ) ) {
                continue;
            }
            $links[] = '<li><a href="' . esc_url( $url ) . '" target="_blank" rel="noopener noreferrer" aria-label="' . esc_attr( $info['label'] ) . '" class="zhuoer-social-icon">' . $info['icon'] . '</a></li>';
        }

        if ( empty( $links ) ) {
            return '';
        }

        $html = '<ul class="zhuoer-footer__social">' . implode( '', $links ) . '</ul>';

        if ( $echo ) {
            echo $html;
        }
        return $html;
    }
endif;

/* ─── Comment Callback ─── */
if ( ! function_exists( 'zhuoer_comment_callback' ) ) :
    function zhuoer_comment_callback( $comment, $args, $depth ) {
        $GLOBALS['comment'] = $comment;

        // Avatar
        $avatar = get_avatar(
            $comment,
            $args['avatar_size'],
            '',
            '',
            array( 'class' => 'avatar', 'extra_attr' => 'loading="lazy"' )
        );

        // Meta
        $author_name = get_comment_author_link( $comment );
        $time        = '<a href="' . esc_url( get_comment_link( $comment ) ) . '"><time datetime="' . esc_attr( get_comment_date( 'c' ) ) . '">' . esc_html( get_comment_date() ) . '</time></a>';

        $moderation = '';
        if ( '0' === $comment->comment_approved ) {
            $moderation = '<span class="comment-awaiting-moderation">待审核</span>';
        }

        $reply_link = get_comment_reply_link(
            array_merge(
                $args,
                array(
                    'depth'     => $depth,
                    'max_depth' => $args['max_depth'],
                    'before'    => '',
                    'after'     => '',
                )
            )
        );

        $tag = ( 'pingback' === $comment->comment_type || 'trackback' === $comment->comment_type ) ? 'li' : 'li';
        ?>
        <li id="comment-<?php comment_ID(); ?>" <?php comment_class( empty( $args['has_children'] ) ? '' : 'parent' ); ?>>
            <div class="comment-body">
                <?php if ( $avatar ) : ?>
                <div class="comment-author-avatar">
                    <?php echo $avatar; ?>
                </div>
                <?php endif; ?>

                <div class="comment-content-wrap">
                    <div class="comment-meta">
                        <span class="comment-author-name"><?php echo $author_name; ?></span>
                        <span class="comment-time"><?php echo $time; ?></span>
                        <?php echo $moderation; ?>
                    </div>

                    <div class="comment-content" itemprop="text">
                        <?php comment_text(); ?>
                    </div>

                    <?php if ( $reply_link ) : ?>
                    <div class="comment-actions">
                        <?php echo $reply_link; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php
    }
endif;
