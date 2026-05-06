<?php
/**
 * Product Category Archive Template — Sidebar + Grid Layout
 * Zhuoer Theme
 *
 * @package ZHUOER
 */

defined( 'ABSPATH' ) || exit;

get_header();

$term          = get_queried_object();
$term_id       = $term->term_id ?? 0;
$thumbnail_id  = get_term_meta( $term_id, 'thumbnail_id', true );
$term_img      = $thumbnail_id ? wp_get_attachment_image_url( $thumbnail_id, 'full' ) : '';
$term_name     = $term->name ?? '';
$term_desc     = $term->description ?? '';
$has_shop_sidebar = is_active_sidebar( 'shop-sidebar' );

// Get subcategories of this category
$subcategories = get_terms( array(
    'taxonomy'   => 'product_cat',
    'hide_empty' => true,
    'parent'     => $term_id,
) );

// Breadcrumb data
$shop_page_id  = wc_get_page_id( 'shop' );
$shop_page_url = $shop_page_id ? get_permalink( $shop_page_id ) : home_url( '/' );
?>

<main id="main-content" class="zhuoer-woo-main" role="main">

    <?php
    /**
     * Hero Banner — full-width with overlay
     */
    if ( $term_img ) :
    ?>
    <div class="zhuoer-woo-hero" style="background-image: url('<?php echo esc_url( $term_img ); ?>')">
        <div class="zhuoer-woo-hero__overlay">
            <div class="zhuoer-container zhuoer-container--wide">
                <div class="zhuoer-woo-hero__content">
                    <h1 class="zhuoer-woo-hero__title"><?php echo esc_html( $term_name ); ?></h1>
                    <?php if ( $term_desc ) : ?>
                        <p class="zhuoer-woo-hero__desc"><?php echo esc_html( $term_desc ); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php else : ?>
    <div class="zhuoer-woo-hero zhuoer-woo-hero--plain">
        <div class="zhuoer-container zhuoer-container--wide">
            <h1 class="zhuoer-woo-hero__title zhuoer-woo-hero__title--plain"><?php echo esc_html( $term_name ); ?></h1>
            <?php if ( $term_desc ) : ?>
                <p class="zhuoer-woo-hero__desc"><?php echo esc_html( $term_desc ); ?></p>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="zhuoer-container zhuoer-container--wide">

        <?php
        /**
         * Breadcrumb
         */
        woocommerce_breadcrumb( array(
            'delimiter'   => '<span class="zhuoer-breadcrumb__sep">›</span>',
            'wrap_before' => '<nav class="zhuoer-breadcrumb" aria-label="' . esc_attr__( '面包屑', 'zhuoer' ) . '"><ol class="zhuoer-breadcrumb__list">',
            'wrap_after'  => '</ol></nav>',
            'before'      => '<li class="zhuoer-breadcrumb__item">',
            'after'       => '</li>',
            'home'        => esc_html__( '首页', 'zhuoer' ),
        ) );
        ?>

        <?php
        /**
         * Subcategories — horizontal scrollable row
         */
        if ( ! is_wp_error( $subcategories ) && ! empty( $subcategories ) ) :
        ?>
        <section class="zhuoer-woo-subcats" aria-label="<?php esc_attr_e( '子分类', 'zhuoer' ); ?>">
            <h2 class="zhuoer-woo-subcats__heading"><?php esc_html_e( '子分类', 'zhuoer' ); ?></h2>
            <div class="zhuoer-woo-subcats__grid">
                <?php foreach ( $subcategories as $subcat ) : ?>
                    <?php
                    $subcat_thumb_id = get_term_meta( $subcat->term_id, 'thumbnail_id', true );
                    $subcat_img      = $subcat_thumb_id ? wp_get_attachment_image_url( $subcat_thumb_id, 'woocommerce_thumbnail' ) : wc_placeholder_img_src( 'woocommerce_thumbnail' );
                    $subcat_url      = get_term_link( $subcat );
                    ?>
                    <a href="<?php echo esc_url( $subcat_url ); ?>" class="zhuoer-woo-subcat-card">
                        <div class="zhuoer-woo-subcat-card__img-wrap">
                            <img src="<?php echo esc_url( $subcat_img ); ?>"
                                 alt="<?php echo esc_attr( $subcat->name ); ?>"
                                 class="zhuoer-woo-subcat-card__img"
                                 loading="lazy" />
                        </div>
                        <span class="zhuoer-woo-subcat-card__name"><?php echo esc_html( $subcat->name ); ?></span>
                        <span class="zhuoer-woo-subcat-card__count">(<?php echo esc_html( $subcat->count ); ?>)</span>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>

        <?php
        /**
         * Product Loop
         */
        if ( woocommerce_product_loop() ) :
        ?>

            <?php
            /**
             * Toolbar: result count + ordering
             */
            ?>
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

            <?php
            /**
             * Hook: woocommerce_after_shop_loop
             * @hooked woocommerce_pagination - 10
             */
            do_action( 'woocommerce_after_shop_loop' );
            ?>

        <?php else : ?>

            <div class="zhuoer-woo-not-found">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <h2><?php esc_html_e( '该分类暂无商品', 'zhuoer' ); ?></h2>
                <p><?php esc_html_e( '稍后再来看看，或者看看其他分类。', 'zhuoer' ); ?></p>
                <a href="<?php echo esc_url( $shop_page_url ); ?>" class="zhuoer-btn zhuoer-btn--primary">
                    <?php esc_html_e( '返回商店', 'zhuoer' ); ?>
                </a>
            </div>

        <?php endif; ?>

    </div>
</main>

<?php
get_footer();
