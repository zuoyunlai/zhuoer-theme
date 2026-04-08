<?php
/**
 * 404 Template
 *
 * @package ZHUOER
 */

get_header();
?>
<div class="zhuoer-site">
<main id="main-content" role="main">

<div class="zhuoer-error-page">
    <div class="zhuoer-container">
        <div class="zhuoer-error-page__code">404</div>
        <h1 class="zhuoer-error-page__title">页面未找到</h1>
        <p class="zhuoer-error-page__text">
            您访问的页面不存在或已被移动。
        </p>
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="zhuoer-cta">
            返回首页
            <?php echo zhuoer_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
        </a>

        <div class="zhuoer-search-form">
            <?php get_search_form(); ?>
        </div>
    </div>
</div>

</main>

</div><!-- close .zhuoer-site -->

<?php get_footer(); ?>
