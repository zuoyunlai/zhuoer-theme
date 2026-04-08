<?php
/**
 * Footer template
 *
 * @package ZHUOER
 */
?>

<footer class="zhuoer-footer" role="contentinfo">
    <div class="zhuoer-container zhuoer-container--wide">
        <div class="zhuoer-footer__grid">
            <!-- Row 1 -->
            <div class="zhuoer-footer__row zhuoer-footer__row--links">
                <div class="zhuoer-footer__col zhuoer-footer__col--menu">
                    <?php if ( has_nav_menu( "footer" ) ) : ?>
                        <?php wp_nav_menu( array( "theme_location" => "footer", "menu_class" => "zhuoer-footer__nav", "container" => false, "fallback_cb" => false ) ); ?>
                    <?php endif; ?>
                </div>
                <div class="zhuoer-footer__col zhuoer-footer__col--social">
                    <?php echo zhuoer_social_links( false ); ?>
                </div>
            </div>
            <!-- Row 2 -->
            <div class="zhuoer-footer__row zhuoer-footer__row--bottom">
                <div class="zhuoer-footer__col zhuoer-footer__col--copy">
                    <p class="zhuoer-footer__copy">
                        &copy; <?php echo esc_html( date( "Y" ) ); ?>
                        <a href="<?php echo esc_url( home_url( "/" ) ); ?>"><?php bloginfo( "name" ); ?></a>.
                        <?php esc_html_e( "All rights reserved.", "zhuoer" ); ?>
                    </p>
                </div>
                <div class="zhuoer-footer__col zhuoer-footer__col--icp">
                    <?php if ( $icp = get_option( "zhuoer_icp_number", "" ) ) : ?>
                        <p class="zhuoer-footer__icp">
                            <a href="<?php echo esc_url( get_option( 'zhuoer_icp_url', 'https://beian.miit.gov.cn/' ) ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $icp ); ?></a>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>

</body>
</html>
