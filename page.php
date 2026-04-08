<?php
/**
 * Page Template
 *
 * @package ZHUOER
 */

get_header();
?>
<div class="zhuoer-site">
<?php
?>

<?php while ( have_posts() ) : the_post(); ?>

    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

        <header class="zhuoer-page__header">
            <div class="zhuoer-container">
                <h1 class="zhuoer-page__title"><?php the_title(); ?></h1>
            </div>
        </header>

        <?php zhuoer_post_thumbnail(); ?>

        <div class="zhuoer-container">
            <div class="zhuoer-single__content">
                <?php
                the_content();

                wp_link_pages(
                    array(
                        'before' => '<div class="zhuoer-page-links">' . esc_html__( 'Pages:', 'zhuoer' ),
                        'after'  => '</div>',
                    )
                );
                ?>
            </div>

            <?php
            if ( comments_open() || get_comments_number() ) :
                comments_template();
            endif;
            ?>
        </div>

    </article>

<?php endwhile; ?>

<?php get_footer(); ?>
