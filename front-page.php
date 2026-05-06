<?php
/**
 * Front Page Template
 *
 * @package ZHUOER
 */

get_header();

$show_sidebar = get_option( 'zhuoer_show_sidebar_home', '1' ) === '1';

$paged       = max( 1, get_query_var( 'paged' ) );
$hero_title  = get_option( 'zhuoer_hero_title' ) ?: get_bloginfo( 'name' );
$hero_sub    = get_option( 'zhuoer_hero_subtitle' ) ?: get_bloginfo( 'description' );
$cta_text    = get_option( 'zhuoer_cta_text', '' );
$cta_url     = get_option( 'zhuoer_cta_url', '' );

// Hero Slides
$hero_slides_raw = get_option( 'zhuoer_hero_slides', '[]' );
$hero_slides     = json_decode( $hero_slides_raw, true ) ?: array();
$use_slider      = ! empty( $hero_slides ) && is_array( $hero_slides );

$latest = new WP_Query( array(
    'post_type'      => 'post',
    'post_status'    => 'publish',
    'posts_per_page' => get_option( 'posts_per_page', 10 ),
    'paged'          => $paged,
    'ignore_sticky_posts' => 1,
) );
?>

<?php if ( $use_slider ) : ?>
<section class="zhuoer-hero-slider" role="region" aria-roledescription="carousel" aria-label="<?php esc_attr_e( '首页幻灯片', 'zhuoer' ); ?>">
    <div class="zhuoer-hero-slider__track">
        <?php foreach ( $hero_slides as $i => $slide ) : ?>
        <div class="zhuoer-hero-slider__slide<?php echo $i === 0 ? ' is-active' : ''; ?>" data-index="<?php echo $i; ?>">
            <div class="zhuoer-container zhuoer-container--wide">
                <div class="zhuoer-hero-slider__inner">
                    <div class="zhuoer-hero-slider__text">
                        <?php if ( ! empty( $slide['title'] ) ) : ?>
                        <h1 class="zhuoer-hero-slider__title"><?php echo esc_html( $slide['title'] ); ?></h1>
                        <?php endif; ?>
                        <?php if ( ! empty( $slide['sub'] ) ) : ?>
                        <p class="zhuoer-hero-slider__subtitle"><?php echo esc_html( $slide['sub'] ); ?></p>
                        <?php endif; ?>
                        <?php if ( ! empty( $slide['cta'] ) && ! empty( $slide['url'] ) ) : ?>
                        <a href="<?php echo esc_url( $slide['url'] ); ?>" class="zhuoer-cta"><?php echo esc_html( $slide['cta'] ); ?></a>
                        <?php endif; ?>
                    </div>
                    <?php if ( ! empty( $slide['img'] ) ) : ?>
                    <div class="zhuoer-hero-slider__image">
                        <img src="<?php echo esc_url( $slide['img'] ); ?>" alt="<?php echo esc_attr( $slide['title'] ?? '' ); ?>" loading="<?php echo $i === 0 ? 'eager' : 'lazy'; ?>" />
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <?php if ( count( $hero_slides ) > 1 ) : ?>
    <button class="zhuoer-hero-slider__arrow zhuoer-hero-slider__arrow--prev" aria-label="<?php esc_attr_e( '上一张', 'zhuoer' ); ?>">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
    </button>
    <button class="zhuoer-hero-slider__arrow zhuoer-hero-slider__arrow--next" aria-label="<?php esc_attr_e( '下一张', 'zhuoer' ); ?>">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
    </button>
    <div class="zhuoer-hero-slider__dots">
        <?php foreach ( $hero_slides as $i => $slide ) : ?>
        <button class="zhuoer-hero-slider__dot<?php echo $i === 0 ? ' is-active' : ''; ?>" data-index="<?php echo $i; ?>" aria-label="<?php echo esc_attr( sprintf( __( '第 %d 张幻灯片', 'zhuoer' ), $i + 1 ) ); ?>"></button>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</section>
<?php else : ?>
<section class="zhuoer-hero">
    <div class="zhuoer-container zhuoer-container--wide">
        <h1 class="zhuoer-hero__title"><?php echo esc_html( $hero_title ); ?></h1>
        <p class="zhuoer-hero__subtitle"><?php echo esc_html( $hero_sub ); ?></p>
        <?php if ( $cta_text && $cta_url ) : ?>
            <a href="<?php echo esc_url( $cta_url ); ?>" class="zhuoer-cta"><?php echo esc_html( $cta_text ); ?></a>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

<?php if ( $show_sidebar ) : ?>
<div class="zhuoer-with-sidebar">
<div class="zhuoer-container zhuoer-container--wide">
<div class="zhuoer-layout">
<div class="zhuoer-layout-main">
<?php endif; ?>

<section class="zhuoer-latest-posts<?php if ( $show_sidebar ) { echo ' has-sidebar'; } ?>">
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
                        <a href="<?php the_permalink(); ?>" rel="bookmark"><?php echo esc_html( get_the_title() ); ?></a>
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
                    <a href="<?php the_permalink(); ?>" class="zhuoer-read-more">阅读全文 <?php echo zhuoer_icon( 'arrow' ); ?></a>
                </footer>
            </article>
            <?php endwhile; ?>
        </div>

        <?php if ( $latest->max_num_pages > 1 ) : ?>
        <nav class="zhuoer-pagination">
            <?php
            $base = add_query_arg( 'paged', '%#%', home_url( '/' ) );
            echo paginate_links( array(
                'base'     => $base,
                'format'   => '?paged=%#%',
                'total'    => $latest->max_num_pages,
                'current'  => $paged,
                'prev_text' => zhuoer_icon( 'arrow' ),
                'next_text' => zhuoer_icon( 'arrow' ),
            ) );
            ?>
        </nav>
        <?php endif; ?>

        <?php else : ?>
        <div class="zhuoer-no-posts">
            <p><?php esc_html_e( '暂无文章', 'zhuoer' ); ?></p>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php if ( $show_sidebar ) : ?>
</div><!-- .zhuoer-layout-main -->
<aside class="zhuoer-layout-sidebar">
    <?php get_sidebar(); ?>
</aside>
</div><!-- .zhuoer-layout -->
</div><!-- .zhuoer-container -->
</div><!-- .zhuoer-with-sidebar -->
<?php endif; ?>

<?php
wp_reset_postdata();
get_footer();
