<?php
/**
 * Product content template (product card in loops)
 * Zhuoer Theme
 *
 * @package ZHUOER
 */

defined( 'ABSPATH' ) || exit;

global $product;

// Ensure product is valid and visible
if ( ! is_a( $product, WC_Product::class ) || ! $product->is_visible() ) {
    return;
}

$product_id   = $product->get_id();
$product_name = $product->get_name();
$product_url  = get_permalink( $product_id );
$product_img  = wp_get_attachment_image_url( $product->get_image_id(), 'woocommerce_thumbnail' );
$product_img  = $product_img ?: wc_placeholder_img_src( 'woocommerce_thumbnail' );
$price_html   = $product->get_price_html();
$is_on_sale   = $product->is_on_sale();
$is_in_stock  = $product->is_in_stock();
$rating_count = $product->get_rating_count();
$average      = $product->get_average_rating();

// Sale badge
$sale_price = $product->get_sale_price();
$regular_price = $product->get_regular_price();
$sale_percent = '';
if ( $is_on_sale && $sale_price && $regular_price ) {
    $sale_percent = round( ( ( $regular_price - $sale_price ) / $regular_price ) * 100 );
}
?>

<article id="product-<?php the_ID(); ?>" <?php wc_product_class( 'zhuoer-product-card', $product ); ?>>
    <a href="<?php echo esc_url( $product_url ); ?>" class="zhuoer-product-card__image-link" tabindex="-1" aria-hidden="true">
        <div class="zhuoer-product-card__image-wrap">
            <img src="<?php echo esc_url( $product_img ); ?>"
                 alt="<?php echo esc_attr( $product_name ); ?>"
                 class="zhuoer-product-card__image"
                 loading="lazy" />
            <?php if ( $is_on_sale && $sale_percent ) : ?>
                <span class="zhuoer-product-card__badge zhuoer-product-card__badge--sale">
                    -<?php echo esc_html( $sale_percent ); ?>%
                </span>
            <?php endif; ?>
            <?php if ( ! $is_in_stock ) : ?>
                <span class="zhuoer-product-card__badge zhuoer-product-card__badge--outofstock">
                    <?php esc_html_e( '缺货', 'zhuoer' ); ?>
                </span>
            <?php endif; ?>
        </div>
    </a>

    <div class="zhuoer-product-card__body">
        <h3 class="zhuoer-product-card__title">
            <a href="<?php echo esc_url( $product_url ); ?>" title="<?php echo esc_attr( $product_name ); ?>">
                <?php echo esc_html( $product_name ); ?>
            </a>
        </h3>

        <?php if ( $rating_count > 0 ) : ?>
        <div class="zhuoer-product-card__rating" aria-label="<?php echo esc_attr( sprintf( __( '评分 %s / 5', 'zhuoer' ), $average ) ); ?>">
            <?php echo wp_kses_post( wc_get_rating_html( $average, $rating_count ) ); ?>
            <span class="zhuoer-product-card__rating-count">(<?php echo esc_html( $rating_count ); ?>)</span>
        </div>
        <?php endif; ?>

        <div class="zhuoer-product-card__price">
            <?php echo wp_kses_post( $price_html ); ?>
        </div>

        <div class="zhuoer-product-card__actions">
            <?php woocommerce_template_loop_add_to_cart(); ?>
        </div>
    </div>
</article>
