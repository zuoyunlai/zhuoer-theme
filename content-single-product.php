<?php
/**
 * Single Product Content Template — Centered Layout
 * Reference: Hypermarket single product page
 *
 * @package ZHUOER
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( post_password_required() ) {
    echo get_the_password_form();
    return;
}

$product_id    = $product->get_id();
$product_title = $product->get_name();
$product_img   = wp_get_attachment_image_url( $product->get_image_id(), 'woocommerce_single' );
$product_img   = $product_img ?: wc_placeholder_img_src( 'woocommerce_single' );
$gallery_ids   = $product->get_gallery_image_ids();
$is_on_sale    = $product->is_on_sale();
$is_in_stock   = $product->is_in_stock();
$rating_count  = $product->get_rating_count();
$average       = $product->get_average_rating();
$sku           = $product->get_sku();
$short_desc    = $product->get_short_description();
$price_html    = $product->get_price_html();
$product_content = apply_filters( 'the_content', get_the_content() );
$has_content   = ! empty( trim( strip_tags( $product_content ) ) );
$attributes    = $product->get_attributes();
$has_attributes = ! empty( $attributes );

// Sale badge
$sale_price    = $product->get_sale_price();
$regular_price = $product->get_regular_price();
$sale_percent  = '';
if ( $is_on_sale && $sale_price && $regular_price ) {
    $sale_percent = round( ( ( $regular_price - $sale_price ) / $regular_price ) * 100 );
}

// All images for gallery
$all_images = array_merge(
    array_filter( array( $product->get_image_id() ) ),
    $gallery_ids
);

// Category info
$cat_list = wc_get_product_category_list( $product_id, ', ', '', '' );

// Prev / Next product navigation
$prev_product = get_previous_post( true, '', 'product' );
$next_product = get_next_post( true, '', 'product' );
?>

<div id="product-<?php the_ID(); ?>" <?php wc_product_class( 'zhuoer-product-single', $product ); ?>>

    <!-- Breadcrumb -->
    <?php
    woocommerce_breadcrumb( array(
        'delimiter'   => '<span class="zhuoer-breadcrumb__sep">/</span>',
        'wrap_before' => '<nav class="zhuoer-breadcrumb zhuoer-breadcrumb--center" aria-label="' . esc_attr__( '面包屑', 'zhuoer' ) . '"><ol class="zhuoer-breadcrumb__list">',
        'wrap_after'  => '</ol></nav>',
        'before'      => '<li class="zhuoer-breadcrumb__item">',
        'after'       => '</li>',
        'home'        => esc_html__( '首页', 'zhuoer' ),
    ) );
    ?>

    <!-- Gallery Outer: Prev / Gallery / Next -->
    <div class="zhuoer-product-single__gallery-outer">

        <!-- Prev Product -->
        <?php if ( $prev_product ) : ?>
        <a href="<?php echo esc_url( get_permalink( $prev_product->ID ) ); ?>" class="zhuoer-product-nav zhuoer-product-nav--prev">
            <span class="zhuoer-product-nav__arrow" aria-hidden="true">←</span>
            <span class="zhuoer-product-nav__label"><?php esc_html_e( '上一个', 'zhuoer' ); ?></span>
        </a>
        <?php else : ?>
        <span class="zhuoer-product-nav zhuoer-product-nav--prev zhuoer-product-nav--disabled">
            <span class="zhuoer-product-nav__arrow" aria-hidden="true">←</span>
            <span class="zhuoer-product-nav__label"><?php esc_html_e( '上一个', 'zhuoer' ); ?></span>
        </span>
        <?php endif; ?>

        <!-- Gallery -->
        <div class="zhuoer-product-single__gallery">
            <div class="zhuoer-product-single__gallery-main">
                <?php if ( $is_on_sale && $sale_percent ) : ?>
                <span class="zhuoer-product-badge zhuoer-product-badge--sale">-<?php echo esc_html( $sale_percent ); ?>%</span>
                <?php endif; ?>

                <img src="<?php echo esc_url( $product_img ); ?>"
                     alt="<?php echo esc_attr( $product_title ); ?>"
                     class="zhuoer-product-single__gallery-img"
                     id="zhuoer-main-img" />

                <?php if ( count( $all_images ) > 1 ) : ?>
                <button type="button" class="zhuoer-gallery-arrow zhuoer-gallery-arrow--prev" id="zhuoer-gallery-prev" aria-label="<?php esc_attr_e( '上一张', 'zhuoer' ); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" width="8" height="12" viewBox="0 0 8 12" fill="none" aria-hidden="true"><path fill="currentColor" fill-rule="evenodd" d="M6.445 12.005.986 6 6.445-.005l1.11 1.01L3.014 6l4.54 4.995-1.109 1.01Z" clip-rule="evenodd"/></svg>
                </button>
                <button type="button" class="zhuoer-gallery-arrow zhuoer-gallery-arrow--next" id="zhuoer-gallery-next" aria-label="<?php esc_attr_e( '下一张', 'zhuoer' ); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" width="8" height="12" viewBox="0 0 8 12" fill="none" aria-hidden="true"><path fill="currentColor" fill-rule="evenodd" d="M1.555-.004 7.014 6l-5.459 6.005-1.11-1.01L4.986 6 .446 1.005l1.109-1.01Z" clip-rule="evenodd"/></svg>
                </button>
                <?php endif; ?>
            </div>

            <?php if ( count( $all_images ) > 1 ) : ?>
            <div class="zhuoer-product-single__gallery-thumbs">
                <?php
                $idx = 0;
                foreach ( $all_images as $img_id ) :
                    $thumb_url = wp_get_attachment_image_url( $img_id, 'woocommerce_gallery_thumbnail' );
                    $full_url  = wp_get_attachment_image_url( $img_id, 'woocommerce_single' );
                    $is_active = $idx === 0 ? ' is-active' : '';
                ?>
                    <button class="zhuoer-product-single__thumb<?php echo esc_attr( $is_active ); ?>"
                            data-full="<?php echo esc_url( $full_url ); ?>"
                            data-index="<?php echo esc_attr( $idx ); ?>"
                            aria-label="<?php echo esc_attr__( '产品图', 'zhuoer' ) . ' ' . ( $idx + 1 ); ?>">
                        <img src="<?php echo esc_url( $thumb_url ); ?>" alt="" loading="lazy" />
                    </button>
                <?php $idx++; endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- Next Product -->
        <?php if ( $next_product ) : ?>
        <a href="<?php echo esc_url( get_permalink( $next_product->ID ) ); ?>" class="zhuoer-product-nav zhuoer-product-nav--next">
            <span class="zhuoer-product-nav__label"><?php esc_html_e( '下一个', 'zhuoer' ); ?></span>
            <span class="zhuoer-product-nav__arrow" aria-hidden="true">→</span>
        </a>
        <?php else : ?>
        <span class="zhuoer-product-nav zhuoer-product-nav--next zhuoer-product-nav--disabled">
            <span class="zhuoer-product-nav__label"><?php esc_html_e( '下一个', 'zhuoer' ); ?></span>
            <span class="zhuoer-product-nav__arrow" aria-hidden="true">→</span>
        </span>
        <?php endif; ?>

    </div>

    <!-- Product Info — All Centered -->
    <div class="zhuoer-product-single__info">

        <h1 class="zhuoer-product-single__title"><?php echo esc_html( $product_title ); ?></h1>

        <div class="zhuoer-product-single__price">
            <?php echo wp_kses_post( $price_html ); ?>
        </div>

        <?php if ( $short_desc ) : ?>
        <div class="zhuoer-product-single__excerpt">
            <?php echo wp_kses_post( $short_desc ); ?>
        </div>
        <?php endif; ?>

        <!-- Meta row: SKU | Category | Rating -->
        <div class="zhuoer-product-single__meta-row">
            <?php if ( $sku ) : ?>
            <span class="zhuoer-product-single__meta-item">
                <span class="zhuoer-product-single__meta-label"><?php esc_html_e( '编号：', 'zhuoer' ); ?></span>
                <?php echo esc_html( $sku ); ?>
            </span>
            <?php endif; ?>

            <?php if ( $cat_list ) : ?>
            <span class="zhuoer-product-single__meta-item">
                <span class="zhuoer-product-single__meta-label"><?php esc_html_e( '分类：', 'zhuoer' ); ?></span>
                <?php echo wp_kses_post( $cat_list ); ?>
            </span>
            <?php endif; ?>

            <?php if ( $rating_count > 0 ) : ?>
            <span class="zhuoer-product-single__meta-item">
                <?php echo wp_kses_post( wc_get_rating_html( $average, $rating_count ) ); ?>
                <a href="#reviews" class="zhuoer-product-single__meta-reviews"><?php printf( esc_html__( '(%s 条评价)', 'zhuoer' ), esc_html( $rating_count ) ); ?></a>
            </span>
            <?php endif; ?>
        </div>

        <!-- Add to Cart -->
        <?php if ( $is_in_stock || $product->is_on_backorder() ) : ?>
        <div class="zhuoer-product-single__cart">
            <?php
            woocommerce_template_single_add_to_cart();
            ?>
        </div>
        <?php endif; ?>

        <!-- Share -->
        <div class="zhuoer-product-single__share-wrap">

            <?php wc_get_template_part( 'single-product/share' ); ?>
        </div>

    </div>
</div>

<?php
// Structured data
if ( class_exists( 'WC_Structured_Data' ) ) {
    $wc_schema = new WC_Structured_Data();
    $wc_schema->generate_product_data();
}
?>

<!-- Tabs: Description | Additional Info | Reviews -->
<?php
$tabs = array();

if ( $has_content ) {
    $tabs['description'] = array(
        'label'   => esc_html__( '商品详情', 'zhuoer' ),
        'content' => $product_content,
    );
}

if ( $has_attributes ) {
    ob_start();
    wc_display_product_attributes( $product );
    $attr_html = ob_get_clean();
    if ( ! empty( trim( strip_tags( $attr_html ) ) ) ) {
        $tabs['attributes'] = array(
            'label'   => esc_html__( '其他信息', 'zhuoer' ),
            'content' => $attr_html,
        );
    }
}

if ( comments_open() || $rating_count > 0 ) {
    $tabs['reviews'] = array(
        'label'   => esc_html__( '评价', 'zhuoer' ) . ( $rating_count > 0 ? ' ' . $rating_count : '' ),
        'content' => '',
    );
}

if ( ! empty( $tabs ) ) :
    $first_tab = array_key_first( $tabs );
?>
<section class="zhuoer-product-tabs-wrap">
    <div class="zhuoer-product-tabs">
        <ul class="zhuoer-product-tabs__nav" role="tablist">
            <?php foreach ( $tabs as $key => $tab ) : ?>
            <li role="presentation">
                <button class="zhuoer-product-tabs__tab<?php echo $key === $first_tab ? ' is-active' : ''; ?>"
                        data-tab="<?php echo esc_attr( $key ); ?>"
                        role="tab"
                        aria-selected="<?php echo $key === $first_tab ? 'true' : 'false'; ?>">
                    <?php echo esc_html( $tab['label'] ); ?>
                </button>
            </li>
            <?php endforeach; ?>
        </ul>
        <div class="zhuoer-product-tabs__panels">
            <?php foreach ( $tabs as $key => $tab ) : ?>
            <div class="zhuoer-product-tabs__panel<?php echo $key === $first_tab ? ' is-active' : ''; ?>"
                 id="<?php echo esc_attr( $key ); ?>"
                 data-panel="<?php echo esc_attr( $key ); ?>"
                 role="tabpanel">
                <?php if ( $key === 'reviews' ) : ?>
                    <?php comments_template(); ?>
                <?php else : ?>
                    <?php echo wp_kses_post( $tab['content'] ); ?>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Related Products -->
<?php woocommerce_output_related_products(); ?>

<!-- 产品页独有：星星评分交互（图库/标签/数量已在 main.js 中处理） -->
<script>
(function() {
    'use strict';

    var zhuoerStars = document.querySelectorAll('#zhuoer-stars a');
    var zhuoerRatingInput = document.getElementById('zhuoer-rating-input');
    if (zhuoerStars.length && zhuoerRatingInput) {
        zhuoerStars.forEach(function(star) {
            star.addEventListener('click', function(e) {
                e.preventDefault();
                var val = parseInt(this.getAttribute('data-value'), 10);
                zhuoerRatingInput.value = val;
                document.querySelector('#zhuoer-stars').classList.add('selected');
                zhuoerStars.forEach(function(s) {
                    var sVal = parseInt(s.getAttribute('data-value'), 10);
                    if (sVal <= val) {
                        s.classList.add('active');
                        s.setAttribute('aria-checked', 'true');
                    } else {
                        s.classList.remove('active');
                        s.setAttribute('aria-checked', 'false');
                    }
                });
            });
            star.addEventListener('mouseenter', function() {
                var val = parseInt(this.getAttribute('data-value'), 10);
                zhuoerStars.forEach(function(s) {
                    var sVal = parseInt(s.getAttribute('data-value'), 10);
                    if (sVal <= val) { s.classList.add('active'); }
                    else { s.classList.remove('active'); }
                });
            });
        });
        document.getElementById('zhuoer-stars').addEventListener('mouseleave', function() {
            var val = parseInt(zhuoerRatingInput.value, 10) || 0;
            zhuoerStars.forEach(function(s) {
                var sVal = parseInt(s.getAttribute('data-value'), 10);
                if (sVal <= val) { s.classList.add('active'); }
                else { s.classList.remove('active'); }
            });
        });
    }

    /* WeChat QR popup is now loaded site-wide via inc/share-buttons.php -> wp_footer */
})();
</script>

<?php do_action( 'woocommerce_after_single_product' ); ?>
