<?php
/**
 * Source: wordpress default theme
 */
defined( 'ABSPATH' ) || exit;

if ( post_password_required() ) {
    return;
}
?>
    <div id="comments" class="comments-area">
        <?php if ( have_comments() ) : ?>
            <?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
                <nav id="comment-nav-below" class="navigation" role="navigation">
                    <div class="nav-previous"><?php previous_comments_link( __( '&larr; Previous Comments',) ); ?></div>
                    <div class="nav-next"><?php next_comments_link( __( 'New Comments &rarr;') ); ?></div>
                </nav>
            <?php endif; ?>

        <?php endif;
        $who_can_comment = shkrn_options('blog_who_can_comment');
        if (!is_user_logged_in() && $who_can_comment == "only_logged_in" ) { ?>
            <p class="comment-notes">برای ارسال نظر لطفا ابتدا وارد حساب کاربری خود شوید.
                <a href="<?php echo get_permalink(get_option('woocommerce_myaccount_page_id')); ?>" class="comment-login-btn-shkrn flex-center">صفحه ورود و ثبت نام</a>
            </p>
            <?php
        } else {
            $name_comment_auther_respond = "";
            if (isset($_GET['replytocom']) && $_GET['replytocom']) {
                $comment_respond_form_id = $_GET['replytocom'];
                $comment_info = get_comment( $comment_respond_form_id );
                $respond_to_user_id = $comment_info->user_id;
                $name_comment_auther_respond = !empty(shkrn_make_user_nickname($respond_to_user_id)) ? shkrn_make_user_nickname($respond_to_user_id) : '';
            }
            $current_user_id = wp_get_current_user()->ID;
            $current_user_nickname = shkrn_make_user_nickname($current_user_id);
            comment_form(array(
                'title_reply' => '',
                'must_log_in' => 'برای ارسال نظر لطفا ابتدا وارد حساب کاربری خود شوید.',
                'logged_in_as' => 'این دیدگاه با نام ('.$current_user_nickname.') منتشر خواهد شد '.'<a href="'. esc_url(wc_get_account_endpoint_url("customer-logout")).'" class="log-out-shkrn-comment-form-btn">خروج از حساب کاربری</a>',
                'title_reply_to' => 'ارسال پاسخ به دیدگاه '.$name_comment_auther_respond,
                'title_reply_before' => '<h3 id="reply-title-shkrn" class="comments-title-shkrn flex-center-in-col">',
                'title_reply_after' => '</h3>',
                'cancel_reply_before' => '<span class="comment-item-btns-shkrn">',
                'cancel_reply_after' => '</span>',
            ));
        } ?>
        <?php if ( ! comments_open() ) : ?>
            <p class="comment-notes">
                <p class="nocomments"><?php echo __( 'Comments closed.'); ?></p>
            </p>
        <?php endif; ?>
        <div class="commentlist flex">
            <?php
            wp_list_comments(
                array(
                    'avatar_size' => 80,
                    'callback' => 'shkrn_comment_list',
                    'style' => 'div'
                )
            );
            $comment_pagination = paginate_comments_links(
                array(
                    'echo'      => false,
                    'end_size'  => 0,
                    'mid_size'  => 0,
                    'next_text' => __( 'Newer Comments', 'shokrino' ) . ' <span aria-hidden="true">&rarr;</span>',
                    'prev_text' => '<span aria-hidden="true">&larr;</span> ' . __( 'Older Comments', 'shokrino' ),
                )
            );

            if ( $comment_pagination ) {
                $pagination_classes = '';

                // If we're only showing the "Next" link, add a class indicating so.
                if ( false === strpos( $comment_pagination, 'prev page-numbers' ) ) {
                    $pagination_classes = ' only-next';
                }
                ?>

                <nav class="comments-pagination pagination<?php echo $pagination_classes; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static output ?>" aria-label="<?php esc_attr_e( 'Comments', 'shokrino' ); ?>">
                    <?php echo wp_kses_post( $comment_pagination ); ?>
                </nav>

                <?php
            }
            ?>

        </div>
    </div>
<?php
function shkrn_comment_list( $comment, $args, $depth ) {
    if ( 'div' === $args['style'] ) {
        $tag       = 'div';
        $add_below = 'comment';
    } else {
        $tag       = 'li';
        $add_below = 'div-comment';
    }
    $comment_classes = array(
        empty( $args['has_children'] ) ? '' : 'parent',
        'comment-shkrn-body flex'
    )
    ?>
    <<?php echo $tag; ?> <?php comment_class($comment_classes); ?> id="comment-<?php comment_ID() ?>">
    <?php
    switch ( $comment->comment_type ) :
        case 'pingback' :
        case 'trackback' : ?>
            <div class="pingback-entry"><span class="pingback-heading"><?php esc_html_e( 'Pingback:' ); ?></span> <?php comment_author_link(); ?></div>
            <?php
            break;
        default :
            $auther_comment_shkrn_id = $comment->user_id;
            if ( 'div' != $args['style'] ) { ?>
                <div id="div-comment-<?php comment_ID() ?>" class="comment-body">
            <?php } ?>
            <div class="comment-author-shkrn flex-center vcard">
                <?php
                echo shkrn_user_avatar($auther_comment_shkrn_id,80); ?>
                <span class="comment-author-name-shkrn">
                    <?php
                    if ($auther_comment_shkrn_id == 0) {
                        echo $comment->comment_author;
                    } else {
                        echo shkrn_make_user_nickname($auther_comment_shkrn_id);
                    } ?>
                </span>
                <span class="comment-date-shkrn">
                    <?php echo get_comment_date('j F Y'); ?>
                </span>
            </div>
            <!-- .comment-author -->
            <div class="comment-details flex-center">
                <div class="comment-meta commentmetadata">
                </div><!-- .comment-meta -->
                <?php
                if ( $comment->comment_approved == '0' ) { ?>
                    <em class="comment-awaiting-moderation highlight-box-shkrn">نظر شما در انتظار تایید توسط مدیر سایت است...</em><br/><?php
                } ?>
                <div class="comment-text"><?php comment_text(); ?></div><!-- .comment-text -->
                <div class="comment-item-btns-shkrn">
                <span class="comment-edit-shkrn">
	                <?php edit_comment_link( 'ویرایش', '  ', '' ); ?>
                </span>
                    <span class="reply-shkrn">
                    <?php
                    $who_can_comment = shkrn_options('blog_who_can_comment');
                    if (!is_user_logged_in() && $who_can_comment == "only_logged_in" ) { ?>
                        <a class="comment-reply-link" href="<?php echo get_permalink(get_option('woocommerce_myaccount_page_id')); ?>" >
                            برای نوشتن پاسخ وارد شوید
                        </a>
                    <?php } else {
                        comment_reply_link(array_merge($args,array(
                            'add_below' => $add_below,
                            'depth'     => $depth,
                            'max_depth' => $args['max_depth'],
                            'reply_text' => 'نوشتن پاسخ',
                        )),$comment, get_the_ID() );
                    } ?>
                </span>
                </div>
            </div>
            <!-- .comment-details -->
            <?php
            if ( 'div' != $args['style'] ) { ?>
                </div>
            <?php }
            break;
    endswitch;
}