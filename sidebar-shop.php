<?php
/**
 * Shop Sidebar Template
 *
 * @package ZHUOER
 */

if ( ! is_active_sidebar( 'shop-sidebar' ) ) {
    return;
}
?>

<aside class="zhuoer-shop-sidebar" role="complementary" aria-label="<?php esc_attr_e( '商城筛选', 'zhuoer' ); ?>">
    <div class="zhuoer-shop-sidebar__header">
        <h3 class="zhuoer-shop-sidebar__heading"><?php esc_html_e( '筛选', 'zhuoer' ); ?></h3>
    </div>
    <?php dynamic_sidebar( 'shop-sidebar' ); ?>
</aside>
