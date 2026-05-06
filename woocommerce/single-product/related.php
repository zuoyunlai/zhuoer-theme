<?php
/**
 * Related Products Template
 * Zhuoer Theme
 *
 * @package ZHUOER
 */

defined( 'ABSPATH' ) || exit;

global $product;

$related_ids = wc_get_related_products( $product->get_id(), 4 );

if ( empty( $related_ids ) ) {
    return;
}

$products = array_map( 'wc_get_product', $related_ids );
?>

<section class="zhuoer-related-products">
    <h2 class="zhuoer-related-products__title"><?php esc_html_e( '相关商品', 'zhuoer' ); ?></h2>
    <div class="zhuoer-woo-grid zhuoer-woo-grid--4">
        <?php foreach ( $products as $related_product ) : ?>
            <?php
            $pid        = $related_product->get_id();
            $purl       = get_permalink( $pid );
            $pname      = $related_product->get_name();
            $pimg       = wp_get_attachment_image_url( $related_product->get_image_id(), 'woocommerce_thumbnail' );
            $pimg       = $pimg ?: wc_placeholder_img_src( 'woocommerce_thumbnail' );
            $price_html = $related_product->get_price_html();
            $is_sale    = $related_product->is_on_sale();
            $is_stock   = $related_product->is_in_stock();
            $sale_p     = $related_product->get_sale_price();
            $reg_p      = $related_product->get_regular_price();
            $sale_pct   = '';
            if ( $is_sale && $sale_p && $reg_p ) {
                $sale_pct = round( ( ( $reg_p - $sale_p ) / $reg_p ) * 100 );
            }
            ?>
            <article class="zhuoer-product-card">
                <a href="<?php echo esc_url( $purl ); ?>" class="zhuoer-product-card__image-link" tabindex="-1" aria-hidden="true">
                    <div class="zhuoer-product-card__image-wrap">
                        <img src="<?php echo esc_url( $pimg ); ?>" alt="<?php echo esc_attr( $pname ); ?>" class="zhuoer-product-card__image" loading="lazy" />
                        <?php if ( $is_sale && $sale_pct ) : ?>
                            <span class="zhuoer-product-card__badge zhuoer-product-card__badge--sale">-<?php echo esc_html( $sale_pct ); ?>%</span>
                        <?php endif; ?>
                    </div>
                </a>
                <div class="zhuoer-product-card__body">
                    <h3 class="zhuoer-product-card__title">
                        <a href="<?php echo esc_url( $purl ); ?>"><?php echo esc_html( $pname ); ?></a>
                    </h3>
                    <div class="zhuoer-product-card__price"><?php echo wp_kses_post( $price_html ); ?></div>
                    <?php if ( $is_stock ) : ?>
                    <div class="zhuoer-product-card__actions">
                        <a href="<?php echo esc_url( $related_product->add_to_cart_url() ); ?>"
                           class="zhuoer-btn zhuoer-btn--primary zhuoer-btn--sm zhuoer-product-card__add-to-cart"
                           data-product_id="<?php echo esc_attr( $pid ); ?>">
                            加入购物车
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>
