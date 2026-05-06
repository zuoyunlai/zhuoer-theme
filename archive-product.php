<?php
/**
 * Product Archive Template (Main Shop Page)
 * Zhuoer Theme — Sidebar + Grid Layout
 *
 * @package ZHUOER
 */

defined( 'ABSPATH' ) || exit;

get_header();

$shop_page_id  = wc_get_page_id( 'shop' );
$shop_page_url = $shop_page_id ? get_permalink( $shop_page_id ) : home_url( '/' );
$has_shop_sidebar = is_active_sidebar( 'shop-sidebar' );
?>

<?php do_action( 'woocommerce_before_main_content' ); ?>
<main id="main-content" class="zhuoer-woo-main" role="main">

    <?php
    $hero_title = woocommerce_page_title( false );
    ?>
    <div class="zhuoer-woo-hero zhuoer-woo-hero--shop">
        <div class="zhuoer-container zhuoer-container--wide">
            <h1 class="zhuoer-woo-hero__title zhuoer-woo-hero__title--plain"><?php echo esc_html( $hero_title ); ?></h1>
        </div>
    </div>

    <div class="zhuoer-container zhuoer-container--wide">

        <?php
        woocommerce_breadcrumb( array(
            'delimiter'   => '<span class="zhuoer-breadcrumb__sep">›</span>',
            'wrap_before' => '<nav class="zhuoer-breadcrumb" aria-label="' . esc_attr__( '面包屑', 'zhuoer' ) . '"><ol class="zhuoer-breadcrumb__list">',
            'wrap_after'  => '</ol></nav>',
            'before'      => '<li class="zhuoer-breadcrumb__item">',
            'after'       => '</li>',
            'home'        => esc_html__( '首页', 'zhuoer' ),
        ) );
        ?>

        <?php if ( woocommerce_product_loop() ) : ?>

            <div class="zhuoer-woo-toolbar">
                <div class="zhuoer-woo-toolbar__right">
                    <?php woocommerce_result_count(); ?>
                    <?php woocommerce_catalog_ordering(); ?>
                </div>
            </div>

            <div class="zhuoer-shop-layout<?php echo $has_shop_sidebar ? ' zhuoer-shop-layout--has-sidebar' : ''; ?>">
                <?php if ( $has_shop_sidebar ) : ?>
                    <?php get_sidebar( 'shop' ); ?>
                <?php endif; ?>

                <div class="zhuoer-shop-layout__main">
                    <div class="zhuoer-woo-grid">
                        <?php
                        while ( have_posts() ) :
                            the_post();
                            wc_get_template_part( 'content', 'product' );
                        endwhile;
                        ?>
                    </div>
                </div>
            </div>

            <?php do_action( 'woocommerce_after_shop_loop' ); ?>

        <?php else : ?>

            <div class="zhuoer-woo-not-found">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <h2><?php esc_html_e( '暂无商品', 'zhuoer' ); ?></h2>
                <p><?php esc_html_e( '商店里还没有商品，敬请期待。', 'zhuoer' ); ?></p>
                <a href="<?php echo esc_url( $shop_page_url ); ?>" class="zhuoer-btn zhuoer-btn--primary">
                    <?php esc_html_e( '返回首页', 'zhuoer' ); ?>
                </a>
            </div>

        <?php endif; ?>

    </div>
</main>

<?php
get_footer();
