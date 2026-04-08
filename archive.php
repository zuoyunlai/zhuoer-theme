<?php
/**
 * Archive Template
 *
 * @package ZHUOER
 */

get_header();
?>
<div class="zhuoer-site">
<?php

$show_sidebar = get_option('zhuoer_show_sidebar_archive', '1') === '1';
?>

<div class="zhuoer-archive">
<?php if ($show_sidebar) : ?>
<div class="zhuoer-container zhuoer-container--wide">
<div class="zhuoer-layout">
<div class="zhuoer-layout-main">
<?php endif; ?>

<div class="zhuoer-page__header">
<?php if (is_category()) : ?>
<h1 class="zhuoer-page__title"><?php echo esc_html(single_cat_title('', false)); ?></h1>
<?php elseif (is_tag()) : ?>
<h1 class="zhuoer-page__title"><?php echo esc_html(single_tag_title('', false)); ?></h1>
<?php elseif (is_author()) : ?>
<h1 class="zhuoer-page__title"><?php echo esc_html(the_author()); ?></h1>
<?php elseif (is_year()) : ?>
<h1 class="zhuoer-page__title"><?php echo esc_html(get_the_date('Y')); ?></h1>
<?php elseif (is_month()) : ?>
<h1 class="zhuoer-page__title"><?php echo esc_html(get_the_date('F Y')); ?></h1>
<?php else : ?>
<h1 class="zhuoer-page__title">归档</h1>
<?php endif; ?>
</div>

<?php if (have_posts()) : ?>
<div class="zhuoer-entries">
<?php while (have_posts()) : the_post(); ?>
<article id="post-<?php the_ID(); ?>" <?php post_class('zhuoer-entry'); ?>>
<header class="zhuoer-entry__header">
<h2 class="zhuoer-entry__title"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
<div class="zhuoer-entry__meta"><?php zhuoer_posted_on(); ?></div>
</header>
<?php if (has_post_thumbnail()) : ?>
<a href="<?php the_permalink(); ?>" class="zhuoer-entry__thumb" tabindex="-1" aria-hidden="true"><?php the_post_thumbnail('medium_large', array('alt' => the_title_attribute('echo=0'))); ?></a>
<?php endif; ?>
<div class="zhuoer-entry__excerpt"><?php the_excerpt(); ?></div>
<footer class="zhuoer-entry__footer"><a href="<?php the_permalink(); ?>" class="zhuoer-read-more">阅读全文 <?php echo zhuoer_icon('arrow'); ?></a></footer>
</article>
<?php endwhile; ?>
</div>
<?php zhuoer_posts_pagination(); ?>
<?php else : ?>
<h2 class="zhuoer-page__title">暂无内容</h2>
<?php endif; ?>

<?php if ($show_sidebar) : ?>
</div><!-- .zhuoer-layout-main -->
<aside class="zhuoer-layout-sidebar"><?php get_sidebar(); ?></aside>
</div><!-- .zhuoer-layout -->
</div><!-- .zhuoer-container -->
</div><!-- .zhuoer-archive -->
<?php else : ?>
</div><!-- .zhuoer-archive -->
<?php endif; ?>

<?php get_footer();
