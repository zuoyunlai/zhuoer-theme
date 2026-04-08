<?php
/**
 * Comments template
 *
 * @package ZHUOER
 */

if ( post_password_required() ) {
    return;
}
?>

<section id="comments" class="zhuoer-comments" aria-label="<?php esc_attr_e( 'Comments', 'zhuoer' ); ?>">

    <?php if ( have_comments() ) : ?>

        <h2 class="zhuoer-comments__title">
            <?php
            $count = get_comments_number();
            if ( $count === 1 ) {
                echo '1 条评论';
            } else {
                echo esc_html( $count ) . ' 条评论';
            }
            ?>
        </h2>

        <ul class="zhuoer-comment-list" aria-label="<?php esc_attr_e( 'Comment list', 'zhuoer' ); ?>">
            <?php
            wp_list_comments(
                array(
                    'style'       => 'ul',
                    'short_ping'  => true,
                    'avatar_size' => 44,
                    'callback'    => 'zhuoer_comment_callback',
                )
            );
            ?>
        </ul>

        <?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
            <nav class="zhuoer-pagination" aria-label="Comment pagination">
                <?php paginate_comments_links( array( 'type' => 'plain', 'prev_text' => '&laquo;', 'next_text' => '&raquo;' ) ); ?>
            </nav>
        <?php endif; ?>

    <?php elseif ( ! comments_open() && get_comments_number( get_the_ID() ) ) : ?>
        <p class="zhuoer-comments--empty">评论已关闭</p>
    <?php endif; ?>

    <?php if ( comments_open() ) : ?>

        <div class="zhuoer-comment-respond">
            <?php
            // 独立输出登录状态，脱离 comment_form 的 flex 布局
            $user = wp_get_current_user();
            if ( is_user_logged_in() ) :
                ?>
                <div class="comment-form-loggedin-extras">
                    <span class="comment-form-loggedin-extras__user">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        以 <strong><?php echo esc_html( $user->display_name ); ?></strong> 的身份登录
                    </span>
                    <a href="<?php echo esc_url( admin_url( 'profile.php' ) ); ?>">编辑个人资料</a>
                    <a href="<?php echo esc_url( wp_logout_url( get_permalink() ) ); ?>">注销</a>
                </div>
                <?php
            endif;

            // 移除 comment_form 默认的 logged-in-as 段落（用 filter 吞掉）
            add_filter( 'comment_form_logged_in', '__return_empty_string' );

            comment_form(
                array(
                    'title_reply'          => '<span class="comment-reply-title">发表评论</span>',
                    'title_reply_before'  => '',
                    'title_reply_after'   => '',
                    'cancel_reply_link'   => '取消回复',
                    'label_submit'        => '发表评论',
                    'submit_button'       => '<button type="submit" id="%2$s" class="%3$s submit"><span>%4$s</span></button>',
                    'comment_field'       => '<div class="comment-form-comment"><label for="comment" class="screen-reader-text">评论内容</label><textarea id="comment" name="comment" placeholder="写下你的评论…" required aria-required="true"></textarea></div>',
                    'fields'              => array(
                        'author' => '<div class="comment-form-author"><input type="text" id="author" name="author" placeholder="昵称 *" value="' . esc_attr( $commenter['comment_author'] ) . '" required aria-required="true" /></div>',
                        'email'  => '<div class="comment-form-email"><input type="email" id="email" name="email" placeholder="邮箱 *" value="' . esc_attr( $commenter['comment_author_email'] ) . '" required aria-required="true" /></div>',
                        'url'    => '<div class="comment-form-url"><input type="url" id="url" name="url" placeholder="网站" value="' . esc_attr( $commenter['comment_author_url'] ) . '" /></div>',
                    ),
                    'comment_notes_before' => '',
                    'class_form'           => 'comment-form',
                )
            );
            ?>
        </div>

    <?php endif; ?>

</section>
