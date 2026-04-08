<?php
/**
 * Front Page Template
 *
 * @package ZHUOER
 */

get_header();

global $show_sidebar;
$show_sidebar = get_option( 'zhuoer_show_sidebar_home', '1' ) === '1';
$GLOBALS['show_sidebar'] = $show_sidebar;

$paged       = max( 1, get_query_var( 'paged' ) );
$hero_title  = get_option( 'zhuoer_hero_title' ) ?: get_bloginfo( 'name' );
$hero_sub    = get_option( 'zhuoer_hero_subtitle' ) ?: get_bloginfo( 'description' );
$cta_text    = get_option( 'zhuoer_cta_text', '' );
$cta_url     = get_option( 'zhuoer_cta_url', '' );

$latest = new WP_Query( array(
    'post_type'      => 'post',
    'post_status'    => 'publish',
    'posts_per_page' => 6,
    'paged'          => $paged,
    'ignore_sticky_posts' => 1,
) );
?>

<!-- Hero Full-Width Banner (outside .zhuoer-site for true full-width) -->
<section class="zhuoer-hero">
    <div class="zhuoer-hero__content">
        <h1 class="zhuoer-hero__title"><?php echo esc_html( $hero_title ); ?></h1>
        <p class="zhuoer-hero__subtitle"><?php echo esc_html( $hero_sub ); ?></p>
        <?php if ( $cta_text && $cta_url ) : ?>
            <a href="<?php echo esc_url( $cta_url ); ?>" class="zhuoer-cta"><?php echo esc_html( $cta_text ); ?></a>
        <?php endif; ?>
    </div>
</section>

<div class="zhuoer-site"><!-- open .zhuoer-site for article content -->

<?php if ( $show_sidebar ) : ?>
<div class="zhuoer-with-sidebar">
<div class="zhuoer-container zhuoer-container--wide">
<div class="zhuoer-layout">
<div class="zhuoer-layout-main">
<?php endif; ?>

<main id="main-content" role="main">

<section class="zhuoer-latest-posts">
    <div class="zhuoer-container<?php if ( ! $show_sidebar ) { echo ' zhuoer-container--wide'; } ?>">

        <?php if ( $latest->have_posts() ) : ?>
        <div class="zhuoer-entries">
            <?php
            while ( $latest->have_posts() ) :
                $latest->the_post();
                ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class( 'zhuoer-entry' ); ?>>
                <header class="zhuoer-entry__header">
                    <h3 class="zhuoer-entry__title">
                        <a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a>
                    </h3>
                    <div class="zhuoer-entry__meta"><?php zhuoer_posted_on(); ?></div>
                </header>
                <?php if ( has_post_thumbnail() ) : ?>
                <a href="<?php the_permalink(); ?>" class="zhuoer-entry__thumb" tabindex="-1" aria-hidden="true">
                    <?php the_post_thumbnail( 'medium_large', array( 'alt' => the_title_attribute( 'echo=0' ) ) ); ?>
                </a>
                <?php endif; ?>
                <div class="zhuoer-entry__excerpt"><?php the_excerpt(); ?></div>
                <footer class="zhuoer-entry__footer">
                    <a href="<?php the_permalink(); ?>" class="zhuoer-read-more"><?php esc_html_e( 'Read more', 'zhuoer' ); ?></a>
                </footer>
            </article>
            <?php endwhile; ?>
        </div>

        <?php if ( $latest->max_num_pages > 1 ) : ?>
        <nav class="zhuoer-pagination">
            <?php
            echo paginate_links( array(
                'total'   => $latest->max_num_pages,
                'current' => $paged,
                'mid_size' => 2,
                'end_size' => 1,
                'type'    => 'plain',
                'prev_text' => '&laquo;',
                'next_text' => '&raquo;',
            ) );
            ?>
        </nav>
        <?php endif; ?>

        <?php wp_reset_postdata(); else : ?>
        <h2 class="zhuoer-page__title">暂无文章</h2>
        <?php endif; ?>

    </div>
</section>

<?php if ( $show_sidebar ) : ?>
</div><!-- .zhuoer-layout-main -->
<aside class="zhuoer-layout-sidebar"><?php get_sidebar(); ?></aside>
</div><!-- .zhuoer-layout -->
</div><!-- .zhuoer-container -->
</div><!-- .zhuoer-with-sidebar -->
<?php endif; ?>

</main>

</div><!-- close .zhuoer-site -->

<?php get_footer();
