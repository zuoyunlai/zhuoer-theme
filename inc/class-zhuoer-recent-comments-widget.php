<?php
/**
 * ZHUOER Recent Comments Widget
 *
 * @package ZHUOER
 */

if ( ! defined( 'ABSPATH' ) ) {
    die;
}

class ZHUOER_Recent_Comments_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'zhuoer_recent_comments',
            'ZHUOER 最新评论',
            array( 'description' => '显示站点的最新评论' )
        );
    }

    public function widget( $args, $instance ) {
        $title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '最新评论' : $instance['title'], $instance, $this->id_base );
        $num   = isset( $instance['num'] ) ? absint( $instance['num'] ) : 5;

        echo $args['before_widget'];

        if ( ! empty( $title ) ) {
            echo $args['before_title'] . esc_html( $title ) . $args['after_title'];
        }

        $comments = get_comments(
            array(
                'number' => $num,
                'status' => 'approve',
                'type'   => 'comment',
            )
        );

        if ( ! empty( $comments ) ) {
            echo '<ul class="zhuoer-recent-comments-list">';
            foreach ( $comments as $comment ) {
                $post    = get_post( $comment->comment_post_ID );
                $avatar  = get_avatar( $comment, 36 );
                $excerpt = mb_strlen( $comment->comment_content, 'utf-8' ) > 40
                    ? mb_substr( strip_tags( $comment->comment_content ), 0, 40, 'utf-8' ) . '…'
                    : strip_tags( $comment->comment_content );
                printf(
                    '<li class="zhuoer-recent-comment-item">' .
                    '<div class="zhuoer-recent-comment-avatar">%s</div>' .
                    '<div class="zhuoer-recent-comment-body">' .
                    '<a href="%s" class="zhuoer-recent-comment-post">%s</a>' .
                    '<span class="zhuoer-recent-comment-meta"><span class="zhuoer-recent-comment-author">%s</span> · <time>%s</time></span>' .
                    '<span class="zhuoer-recent-comment-excerpt">%s</span>' .
                    '</div></li>',
                    $avatar,
                    esc_url( get_comment_link( $comment ) ),
                    esc_html( get_the_title( $post->ID ) ),
                    esc_html( $comment->comment_author ),
                    esc_html( wp_date( 'Y-m-d', strtotime( $comment->comment_date ) ) ),
                    esc_html( $excerpt )
                );
            }
            echo '</ul>';
        } else {
            echo '<p style="font-size:0.875rem;color:var(--zhuoer-color-text-muted);">暂无评论</p>';
        }

        echo $args['after_widget'];
    }

    public function form( $instance ) {
        $title = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '最新评论';
        $num   = isset( $instance['num'] ) ? absint( $instance['num'] ) : 5;
        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>">标题：</label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'num' ); ?>">显示数量：</label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'num' ); ?>" name="<?php echo $this->get_field_name( 'num' ); ?>" type="number" min="1" max="20" value="<?php echo $num; ?>">
        </p>
        <?php
    }

    public function update( $new_instance, $old_instance ) {
        $instance          = $old_instance;
        $instance['title'] = sanitize_text_field( $new_instance['title'] );
        $instance['num']   = absint( $new_instance['num'] );
        return $instance;
    }
}
