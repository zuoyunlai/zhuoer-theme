<?php
/**
 * WooCommerce Single Product Share (inline buttons)
 * 调用全站统一分享组件
 *
 * @package ZHUOER
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( function_exists( 'zhuoer_share_buttons' ) ) {
    zhuoer_share_buttons( $product, __( '分享产品', 'zhuoer' ) );
}
