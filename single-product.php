<?php
/**
 * Single Product Template
 * Zhuoer Theme — Centered Layout (Reference Image)
 *
 * @package ZHUOER
 */

defined( 'ABSPATH' ) || exit;

get_header();

/**
 * Hook: woocommerce_before_main_content
 */
do_action( 'woocommerce_before_main_content' );
?>

<main id="main-content" class="zhuoer-woo-main zhuoer-woo-main--single" role="main">
    <div class="zhuoer-container zhuoer-container--wide">

        <?php
        while ( have_posts() ) :
            the_post();
            wc_get_template_part( 'content', 'single-product' );
        endwhile;
        ?>

    </div>
</main>

<?php
/**
 * Hook: woocommerce_after_main_content
 */
do_action( 'woocommerce_after_main_content' );

get_footer();
