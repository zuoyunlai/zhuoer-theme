<?php
/**
 * Product Search Form Template
 * Zhuoer Theme
 *
 * @package ZHUOER
 */

defined( 'ABSPATH' ) || exit;
?>
<form role="search" method="get" class="zhuoer-product-searchform" action="<?php echo esc_url( home_url( '/' ) ); ?>">
    <input type="search" name="s" class="zhuoer-product-searchform__input" placeholder="<?php esc_attr_e( '搜索商品…', 'zhuoer' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" />
    <input type="hidden" name="post_type" value="product" />
    <button type="submit" class="zhuoer-product-searchform__submit" aria-label="<?php esc_attr_e( '搜索', 'zhuoer' ); ?>">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
    </button>
</form>
