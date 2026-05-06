<?php
/**
 * Main archive / blog list template
 *
 * @package ZHUOER
 */

get_header();
?>
<main id="main-content" role="main">

<section class="zhuoer-archive">
    <?php if ( have_posts() ) : ?>

        <?php if ( is_home() && ! is_front_page() ) : ?>
            <div class="zhuoer-page__header">
                <h1 class="zhuoer-page__title"><?php single_post_title(); ?></h1>
            </div>
        <?php elseif ( is_category() || is_tag() || is_tax() ) : ?>
            <div class="zhuoer-page__header">
                <h1 class="zhuoer-page__title"><?php single_term_title(); ?></h1>
            </div>
        <?php elseif ( is_author() ) : ?>
            <div class="zhuoer-page__header">
                <h1 class="zhuoer-page__title"><?php the_author(); ?></h1>
            </div>
        <?php elseif ( is_year() ) : ?>
            <div class="zhuoer-page__header">
                <h1 class="zhuoer-page__title"><?php echo esc_html( get_the_date( 'Y' ) ); ?></h1>
            </div>
        <?php endif; ?>

        <div class="zhuoer-entries">
            <?php
            while ( have_posts() ) :
                the_post();
                ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class( 'zhuoer-entry' ); ?>>
                    <header class="zhuoer-entry__header">
                                <h2 class="zhuoer-entry__title">
                                <a href="<?php the_permalink(); ?>" rel="bookmark"><?php echo esc_html( get_the_title() ); ?></a>
                            </h2>

                        <div class="zhuoer-entry__meta">
                            <?php zhuoer_posted_on(); ?>
                            <?php zhuoer_posted_by(); ?>
                        </div>
                    </header>

                    <?php if ( has_post_thumbnail() ) : ?>
                        <a href="<?php the_permalink(); ?>" class="zhuoer-entry__thumb" tabindex="-1" aria-hidden="true">
                            <?php the_post_thumbnail( 'medium_large', array( 'alt' => the_title_attribute( 'echo=0' ) ) ); ?>
                        </a>
                    <?php endif; ?>

                    <div class="zhuoer-entry__excerpt">
                        <?php the_excerpt(); ?>
                    </div>

                    <footer class="zhuoer-entry__footer">
                        <a href="<?php the_permalink(); ?>" class="zhuoer-read-more">
                            <?php esc_html_e( '阅读全文', 'zhuoer' ); ?>
                            <?php echo zhuoer_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
                        </a>
                        <?php zhuoer_entry_tags( '<div>', ' ', '</div>' ); ?>
                    </footer>
                </article>
            <?php endwhile; ?>
        </div>

        <?php zhuoer_posts_pagination(); ?>

    <?php else : ?>
        <div class="zhuoer-no-results">
            <h2 class="zhuoer-page__title"><?php esc_html_e( '暂无内容', 'zhuoer' ); ?></h2>
            <p class="zhuoer-error-page__text"><?php esc_html_e( '没有找到符合条件的内容。', 'zhuoer' ); ?></p>
        </div>
    <?php endif; ?>
</section>

</main>

<?php
get_footer();
