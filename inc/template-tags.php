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
        ?>
        <div class="zhuoer-thumbnail">
            <?php the_post_thumbnail( 'zhuoer-thumbnail', array( 'alt' => the_title_attribute( 'echo=0' ), 'loading' => 'lazy', 'srcset' => wp_get_attachment_image_srcset( get_post_thumbnail_id(), 'zhuoer-thumbnail' ), 'sizes' => '(max-width: 768px) 100vw, (max-width: 1024px) 50vw, 540px' ) ); ?>
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
// SOCIAL LINKS
// =============================================
if ( ! function_exists( 'zhuoer_social_links' ) ) :
    function zhuoer_social_links( $echo = true ) {
        // 用 get_option 读取后台设置（与 customizer.php 的 register_setting 配套）
        $social_fields = array(
            'weibo'       => array( 'label' => 'Weibo',     'icon' => '微博' ),
            'zhihu'       => array( 'label' => 'Zhihu',     'icon' => '知乎' ),
            'bilibili'    => array( 'label' => 'Bilibili',  'icon' => 'B站' ),
            'xiaohongshu' => array( 'label' => '小红书',    'icon' => '小红书' ),
            'github'      => array( 'label' => 'GitHub',    'icon' => 'GitHub' ),
            'twitter'     => array( 'label' => 'Twitter',   'icon' => 'Twitter' ),
            'facebook'    => array( 'label' => 'Facebook',   'icon' => 'Facebook' ),
            'instagram'   => array( 'label' => 'Instagram', 'icon' => 'Instagram' ),
            'linkedin'    => array( 'label' => 'LinkedIn',   'icon' => 'LinkedIn' ),
        );

        $links = array();
        foreach ( $social_fields as $key => $info ) {
            $url = get_option( "zhuoer_social_{$key}", '' );
            if ( empty( $url ) ) {
                continue;
            }
            $links[] = '<li><a href="' . esc_url( $url ) . '" target="_blank" rel="noopener noreferrer" aria-label="' . esc_attr( $info['label'] ) . '">' . esc_html( $info['icon'] ) . '</a></li>';
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
