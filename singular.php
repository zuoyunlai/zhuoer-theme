<?php
/**
 * Singular Template — fallback for single + page
 * single.php 逻辑已合并至此，统一管理侧边栏/相关文章/作者介绍
 *
 * @package ZHUOER
 */

get_header();
?>

<?php while ( have_posts() ) : the_post(); ?>

    <?php
    $is_single_post = is_singular( 'post' );
    $show_sidebar   = $is_single_post
        ? get_option( 'zhuoer_show_sidebar_single', '0' ) === '1'
        : false;
    ?>

    <?php if ( $show_sidebar ) : ?>
    <div class="zhuoer-with-sidebar">
        <div class="zhuoer-container zhuoer-container--wide">
        <div class="zhuoer-layout">
        <main class="zhuoer-layout-main">
    <?php endif; ?>

    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <?php if ( $is_single_post ) : ?>
    <script type="application/ld+json">
    <?php
    $schema = array(
        '@context' => 'https://schema.org',
        '@type'    => 'BlogPosting',
        'headline' => get_the_title(),
        'url'      => get_permalink(),
        'datePublished' => get_the_date( 'c' ),
        'dateModified'  => get_the_modified_date( 'c' ),
        'author'   => array(
            '@type' => 'Person',
            'name'  => get_the_author(),
            'url'   => get_author_posts_url( get_post_field( 'post_author', get_the_ID() ) ),
        ),
        'publisher' => array(
            '@type' => 'Organization',
            'name'  => get_bloginfo( 'name' ),
            'logo'  => array(
                '@type' => 'ImageObject',
                'url'   => get_site_icon_url(),
            ),
        ),
    );
    $thumb = get_the_post_thumbnail_url( null, 'full' );
    if ( $thumb ) {
        $schema['image'] = array(
            '@type' => 'ImageObject',
            'url'   => $thumb,
        );
    }
    echo wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
    ?>
    </script>
    <?php endif; ?>

        <?php if ( $is_single_post ) : ?>
            <header class="zhuoer-single__header">
                <div class="zhuoer-container">

                    <h1 class="zhuoer-single__title"><?php echo esc_html( get_the_title() ); ?></h1>

                    <div class="zhuoer-single__meta">
                        <?php zhuoer_posted_on(); ?>
                        <?php zhuoer_posted_by(); ?>
                        <?php echo zhuoer_reading_time(); // phpcs:ignore WordPress.Security.EscapeOutput ?>
                    </div>
                </div>
            </header>
        <?php else : ?>
            <header class="zhuoer-page__header">
                <div class="zhuoer-container">
                    <h1 class="zhuoer-page__title"><?php echo esc_html( get_the_title() ); ?></h1>
                </div>
            </header>
        <?php endif; ?>

        <?php zhuoer_post_thumbnail(); ?>

        <div class="zhuoer-container">
            <?php if ( is_singular( 'post' ) ) : ?>
            <nav class="zhuoer-breadcrumb" aria-label="<?php esc_attr_e( '内容路径', 'zhuoer' ); ?>">
                <ol itemscope itemtype="https://schema.org/BreadcrumbList">
                    <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                        <a itemprop="item" href="<?php echo esc_url( home_url( '/' ) ); ?>"><span itemprop="name"><?php esc_html_e( '首页', 'zhuoer' ); ?></span></a>
                        <meta itemprop="position" content="1">
                    </li>
                    <?php
                    $cats = get_the_category();
                    if ( ! empty( $cats ) ) :
                        $cat = $cats[0];
                        ?>
                    <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                        <span class="zhuoer-breadcrumb__sep" aria-hidden="true">›</span>
                        <a itemprop="item" href="<?php echo esc_url( get_category_link( $cat->term_id ) ); ?>">
                            <span itemprop="name"><?php echo esc_html( $cat->name ); ?></span>
                        </a>
                        <meta itemprop="position" content="2">
                    </li>
                    <?php endif; ?>
                    <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                        <span class="zhuoer-breadcrumb__sep" aria-hidden="true">›</span>
                        <span itemprop="name"><?php echo esc_html( get_the_title() ); ?></span>
                        <meta itemprop="position" content="3">
                    </li>
                </ol>
            </nav>
            <?php endif; ?>
            <?php
            $toc = zhuoer_toc();
            if ( $toc ) {
                echo '<div class="zhuoer-toc-wrapper">' . $toc . '</div>';
            }
            ?>
            <div class="zhuoer-single__content">
                <?php
                the_content();
                wp_link_pages(
                    array(
                        'before' => '<div class="zhuoer-page-links">' . esc_html__( 'Pages:', 'zhuoer' ),
                        'after'  => '</div>',
                    )
                );
                ?>
            </div>

            <?php if ( $is_single_post ) : ?>

                <?php
                $tags = get_the_tags();
                if ( ! empty( $tags ) ) :
                    echo '<div class="zhuoer-single__tags"><ul class="zhuoer-entry__tags">';
                    foreach ( $tags as $tag ) {
                        echo '<li><a href="' . esc_url( get_tag_link( $tag->term_id ) ) . '">' . esc_html( $tag->name ) . '</a></li>';
                    }
                    echo '</ul></div>';
                endif;
                ?>

                <?php if ( function_exists( 'zhuoer_share_buttons' ) ) : ?>
                    <div class="zhuoer-single__share">
                        <?php zhuoer_share_buttons( null, __( '分享', 'zhuoer' ) ); ?>
                    </div>
                <?php endif; ?>

                <?php
                // 上一篇 / 下一篇
                $prev = get_previous_post();
                $next = get_next_post();
                if ( $prev || $next ) :
                    ?>
                    <nav class="zhuoer-post-nav">
                        <?php if ( $prev ) : ?>
                            <div class="zhuoer-post-nav__item">
                                <a href="<?php echo esc_url( get_permalink( $prev ) ); ?>">
                                    <span class="zhuoer-post-nav__label"><?php esc_html_e( '← 上一篇', 'zhuoer' ); ?></span>
                                    <span class="zhuoer-post-nav__title"><?php echo esc_html( get_the_title( $prev ) ); ?></span>
                                </a>
                            </div>
                        <?php endif; ?>
                        <?php if ( $next ) : ?>
                            <div class="zhuoer-post-nav__item zhuoer-post-nav__next">
                                <a href="<?php echo esc_url( get_permalink( $next ) ); ?>">
                                    <span class="zhuoer-post-nav__label"><?php esc_html_e( '下一篇 →', 'zhuoer' ); ?></span>
                                    <span class="zhuoer-post-nav__title"><?php echo esc_html( get_the_title( $next ) ); ?></span>
                                </a>
                            </div>
                        <?php endif; ?>
                    </nav>
                <?php endif; ?>

                <?php
                // 作者介绍
                if ( get_option( 'zhuoer_show_author_bio', '1' ) === '1' ) :
                    $author_id    = get_post_field( 'post_author', get_the_ID() );
                    $author_name  = get_the_author_meta( 'display_name', $author_id );
                    $author_url   = get_author_posts_url( $author_id );
                    $author_desc  = get_the_author_meta( 'description', $author_id );
                    $author_avatar = get_avatar( $author_id, 80, '', $author_name, array( 'class' => 'zhuoer-author-bio__avatar' ) );
                    if ( $author_desc ) :
                        ?>
                    <div class="zhuoer-author-bio">
                        <a href="<?php echo esc_url( $author_url ); ?>" class="zhuoer-author-bio__avatar-link">
                            <?php echo wp_kses_post( $author_avatar ); ?>
                        </a>
                        <div class="zhuoer-author-bio__info">
                            <div class="zhuoer-author-bio__name">
                                <a href="<?php echo esc_url( $author_url ); ?>"><?php echo esc_html( $author_name ); ?></a>
                            </div>
                            <p class="zhuoer-author-bio__desc"><?php echo esc_html( $author_desc ); ?></p>
                        </div>
                    </div>
                        <?php
                    endif;
                endif;
                ?>

                <?php
                // 相关文章（同类目随机 6 篇）— 使用 Transient 缓存避免 rand 性能问题
                $categories = get_the_category();
                if ( ! empty( $categories ) ) {
                    $cat_ids   = wp_list_pluck( $categories, 'term_id' );
                    $cache_key = 'zhuoer_related_' . get_the_ID();
                    $post_ids  = get_transient( $cache_key );

                    if ( false === $post_ids ) {
                        $related_q = new WP_Query( array(
                            'category__in'   => $cat_ids,
                            'post__not_in'   => array( get_the_ID() ),
                            'posts_per_page' => 20,
                            'orderby'        => 'date',
                            'fields'         => 'ids',
                        ) );
                        $post_ids = $related_q->posts;
                        shuffle( $post_ids );
                        $post_ids = array_slice( $post_ids, 0, 6 );
                        wp_reset_postdata();
                        set_transient( $cache_key, $post_ids, 12 * HOUR_IN_SECONDS );
                    }

                    $related = new WP_Query( array(
                        'post__in'       => $post_ids,
                        'posts_per_page' => 20,
                        'orderby'        => 'post__in',
                    ) );
                    if ( $related->have_posts() ) :
                        echo '<section class="zhuoer-related-posts"><div class="zhuoer-container"><h2 class="zhuoer-related-posts__title">' . esc_html__( '相关文章', 'zhuoer' ) . '</h2><div class="zhuoer-related-posts__grid">';
                        while ( $related->have_posts() ) : $related->the_post();
                            $rel_permalink = esc_url( get_permalink() );
                            $rel_title     = esc_html( get_the_title() );
                            $rel_cat       = get_the_category();
                            $rel_cat_name  = ! empty( $rel_cat ) ? esc_html( $rel_cat[0]->name ) : '';
                            $rel_date      = esc_html( get_the_date( 'Y-m-d' ) );
                            echo '<div class="zhuoer-related-card">';
                            echo '<a href="' . $rel_permalink . '" class="zhuoer-related-card__thumb" tabindex="-1" aria-hidden="true">';
                            if ( has_post_thumbnail() ) {
                                the_post_thumbnail( 'medium', array( 'alt' => the_title_attribute( 'echo=0' ), 'loading' => 'lazy' ) );
                            } else {
                                echo '<div class="zhuoer-related-card__thumb-fallback">' . mb_substr( get_the_title(), 0, 1, 'utf-8' ) . '</div>';
                            }
                            echo '</a>';
                            echo '<div class="zhuoer-related-card__body">';
                            echo '<h3 class="zhuoer-related-card__title"><a href="' . $rel_permalink . '">' . $rel_title . '</a></h3>';
                            echo '<span class="zhuoer-related-card__date">' . $rel_date . '</span>';
                            echo '</div></div>';
                        endwhile;
                        echo '</div></div></section>';
                        wp_reset_postdata();
                    endif;
                }
                ?>

                <?php if ( comments_open() || get_comments_number() ) : ?>
                    <?php comments_template(); ?>
                <?php endif; ?>

            <?php endif; // is_singular('post') ?>

        </div>

    </article>

    <?php endwhile; ?>

    <?php if ( $show_sidebar ) : ?>
        </main>
        <aside class="zhuoer-layout-sidebar">
            <?php get_sidebar(); ?>
        </aside>
        </div><!-- .zhuoer-layout -->
        </div><!-- .zhuoer-container -->
    </div><!-- .zhuoer-with-sidebar -->
    <?php endif; ?>

<?php get_footer();
