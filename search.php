<?php
/**
 * 搜索结果模板
 *
 * @package ZHUOER
 */

get_header();

$paged   = max( 1, get_query_var( 'paged' ) );
$base    = esc_url_raw( str_replace( 999999999, '%#%', get_pagenum_link( 999999999, false ) ) );
$search_url = $paged > 1
    ? str_replace( '%#%', $paged, $base )
    : home_url( '/?s=' . get_search_query() );
?>
<link rel="canonical" href="<?php echo esc_url( $search_url ); ?>" />
<div class="zhuoer-site">
<main id="main-content" role="main">

<section class="zhuoer-archive">
    <div class="zhuoer-container zhuoer-container--wide">

        <div class="zhuoer-page__header">
            <h1 class="zhuoer-page__title">
                搜索结果：<span><?php echo esc_html( get_search_query() ); ?></span>
            </h1>
        </div>

        <?php if ( have_posts() ) : ?>
            <div class="zhuoer-entries">
                <?php
                while ( have_posts() ) :
                    the_post();
                ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class( 'zhuoer-entry' ); ?>>
                        <header class="zhuoer-entry__header">
                            <h2 class="zhuoer-entry__title">
                                <a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a>
                            </h2>
                            <div class="zhuoer-entry__meta">
                                <?php zhuoer_posted_on(); ?>
                                <?php zhuoer_posted_by(); ?>
                            </div>
                        </header>
                        <?php if ( has_post_thumbnail() ) : ?>
                            <a href="<?php the_permalink(); ?>" class="zhuoer-entry__thumb" tabindex="-1" aria-hidden="true">
                                <?php the_post_thumbnail( 'medium_large', array( 'alt' => the_title_attribute( 'echo=0' ) ) ); ?>
                            </a>
                        <?php endif; ?>
                        <div class="zhuoer-entry__excerpt"><?php the_excerpt(); ?></div>
                        <footer class="zhuoer-entry__footer">
                            <a href="<?php the_permalink(); ?>" class="zhuoer-read-more">阅读全文</a>
                        </footer>
                    </article>
                <?php endwhile; ?>
            </div>

            <?php zhuoer_posts_pagination(); ?>

        <?php else : ?>
            <div class="zhuoer-no-results">
                <h2 class="zhuoer-page__title">未找到相关内容</h2>
                <p class="zhuoer-error-page__text">
                    没有找到与 "<?php echo esc_html( get_search_query() ); ?>" 相关的内容，请尝试其他关键词。
                </p>
                <div class="zhuoer-search-form">
                    <?php get_search_form(); ?>
                </div>
            </div>
        <?php endif; ?>

    </div>
</section>

</main>
</div><!-- close .zhuoer-site -->

<?php get_footer();
