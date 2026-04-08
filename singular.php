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
            <header class="zhuoer-single__header">
                <div class="zhuoer-container">
                    <?php
                    $categories = get_the_category();
                    if ( ! empty( $categories ) ) :
                        foreach ( $categories as $cat ) :
                            ?>
                            <a href="<?php echo esc_url( get_category_link( $cat->term_id ) ); ?>" class="zhuoer-category-pill">
                                <?php echo esc_html( $cat->name ); ?>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <h1 class="zhuoer-single__title"><?php the_title(); ?></h1>

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
                    <h1 class="zhuoer-page__title"><?php the_title(); ?></h1>
                </div>
            </header>
        <?php endif; ?>

        <?php zhuoer_post_thumbnail(); ?>

        <div class="zhuoer-container">
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
                                    <span class="zhuoer-post-nav__label">← 上一篇</span>
                                    <span class="zhuoer-post-nav__title"><?php echo esc_html( get_the_title( $prev ) ); ?></span>
                                </a>
                            </div>
                        <?php endif; ?>
                        <?php if ( $next ) : ?>
                            <div class="zhuoer-post-nav__item zhuoer-post-nav__next">
                                <a href="<?php echo esc_url( get_permalink( $next ) ); ?>">
                                    <span class="zhuoer-post-nav__label">下一篇 →</span>
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
                            <?php echo $author_avatar; ?>
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
                // 相关文章（同类目随机 6 篇）
                $categories = get_the_category();
                if ( ! empty( $categories ) ) {
                    $cat_ids  = wp_list_pluck( $categories, 'term_id' );
                    $related  = new WP_Query(array(
                        'category__in'   => $cat_ids,
                        'post__not_in'   => array( get_the_ID() ),
                        'posts_per_page' => 6,
                        'orderby'        => 'rand',
                    ));
                    if ( $related->have_posts() ) :
                        // 面包屑导航
                        $rel_cats = get_the_category();
                        $rel_cat_name = ! empty( $rel_cats[0] ) ? $rel_cats[0]->name : '';
                        $rel_cat_url  = ! empty( $rel_cats[0] ) ? get_category_link( $rel_cats[0]->term_id ) : '';
                        if ( $rel_cat_name ) :
                            echo '<nav class="zhuoer-related-posts__breadcrumb" aria-label="相关文章路径">';
                            echo '<ol itemscope itemtype="https://schema.org/BreadcrumbList">';
                            echo '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
                            echo '<a itemprop="item" href="' . esc_url( home_url( "/" ) ) . '"><span itemprop="name">首页</span></a>';
                            echo '<meta itemprop="position" content="1">';
                            echo '</li>';
                            echo '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
                            echo '<span class="zhuoer-breadcrumb-sep" aria-hidden="true">›</span>';
                            echo '<a itemprop="item" href="' . esc_url( $rel_cat_url ) . '"><span itemprop="name">' . esc_html( $rel_cat_name ) . '</span></a>';
                            echo '<meta itemprop="position" content="2">';
                            echo '</li>';
                            echo '</ol></nav>';
                        endif;

                        echo '<section class="zhuoer-related-posts"><div class="zhuoer-container"><h2 class="zhuoer-related-posts__title">相关文章</h2><div class="zhuoer-related-posts__grid">';
                        while ( $related->have_posts() ) : $related->the_post();
                            $rel_permalink = esc_url( get_permalink() );
                            $rel_title     = esc_html( get_the_title() );
                            $rel_cat       = get_the_category();
                            $rel_cat_name  = ! empty( $rel_cat ) ? esc_html( $rel_cat[0]->name ) : '';
                            $rel_date      = esc_html( get_the_date( 'Y-m-d' ) );
                            echo '<article class="zhuoer-related-card">';
                            echo '<a href="' . $rel_permalink . '" class="zhuoer-related-card__thumb" tabindex="-1" aria-hidden="true">';
                            if ( has_post_thumbnail() ) {
                                the_post_thumbnail( 'medium', array( 'alt' => the_title_attribute( 'echo=0' ), 'loading' => 'lazy' ) );
                            } else {
                                echo '<div class="zhuoer-related-card__thumb-fallback">' . mb_substr( get_the_title(), 0, 1, 'utf-8' ) . '</div>';
                            }
                            echo '</a>';
                            echo '<div class="zhuoer-related-card__body">';
                            echo '<div class="zhuoer-related-card__meta">';
                            if ( $rel_cat_name ) {
                                echo '<span class="zhuoer-related-card__cat">' . $rel_cat_name . '</span>';
                            }
                            echo '<time class="zhuoer-related-card__date" datetime="' . $rel_date . '">' . $rel_date . '</time>';
                            echo '</div>';
                            echo '<h3 class="zhuoer-related-card__title"><a href="' . $rel_permalink . '">' . $rel_title . '</a></h3>';
                            echo '</div></article>';
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
