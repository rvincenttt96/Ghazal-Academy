<?php
/**
 * Display single product reviews (comments)
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product-reviews.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.7.0
 */

defined( 'ABSPATH' ) || exit;

global $product; ?>

<div id="reviews" class="comments-area woocommerce-Reviews">
	<?php if ( have_comments() ) : ?>
        <?php
        if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) :
            echo '<nav class="woocommerce-pagination">';
            paginate_comments_links(
                apply_filters(
                    'woocommerce_comment_pagination_args',
                    array(
                        'prev_text' => is_rtl() ? '&rarr;' : '&larr;',
                        'next_text' => is_rtl() ? '&larr;' : '&rarr;',
                        'type'      => 'list',
                    )
                )
            );
            echo '</nav>';
        endif;
        ?>
    <?php endif;
    $who_can_comment = shkrn_options('blog_who_can_comment');
    if (!is_user_logged_in() && $who_can_comment == "only_logged_in" ) { ?>
    <p class="comment-notes">برای ارسال نظر لطفا ابتدا وارد حساب کاربری خود شوید.
        <a href="<?php echo get_permalink(get_option('woocommerce_myaccount_page_id')); ?>" class="comment-login-btn-shkrn flex-center">صفحه ورود و ثبت نام</a>
    </p>
    <?php
    } else {
        $show_stars = true;
        $name_comment_auther_respond = "";
	    if (isset($_GET['replytocom']) && $_GET['replytocom']) {
		    $comment_respond_form_id = $_GET['replytocom'];
		    $comment_info = get_comment( $comment_respond_form_id );
		    $respond_to_user_id = $comment_info->user_id;
		    $name_comment_auther_respond = !empty(shkrn_make_user_nickname($respond_to_user_id)) ? shkrn_make_user_nickname($respond_to_user_id) : '';
            $show_stars = false;
	    }
	    $current_user_id = wp_get_current_user()->ID;
	    $current_user_nickname = shkrn_make_user_nickname($current_user_id);
        $is_buyer = is_user_bought_product_shkrn('',$current_user_id) ? true : false;
        if (shkrn_user_has_rated_on_product_before($current_user_id,$product->get_id())) {
            $show_stars = false;
        }
        ?>

        <div id="review_form_wrapper">
            <div id="review_form">
                <?php
                $commenter    = wp_get_current_commenter();
                $comment_form = array(
                    /* translators: %s is product title */
                    'title_reply'         => have_comments() ? esc_html__( 'Add a review', 'woocommerce' ) : sprintf( esc_html__( 'Be the first to review &ldquo;%s&rdquo;', 'woocommerce' ), get_the_title() ),
                    /* translators: %s is product title */
                    'title_reply_to'      => esc_html__( 'Leave a Reply to %s', 'woocommerce' ),
                    'title_reply_before'  => '<span id="reply-title" class="comment-reply-title">',
                    'title_reply_after'   => '</span>',
                    'comment_notes_after' => '',
                    'label_submit'        => esc_html__( 'Submit', 'woocommerce' ),
                    'logged_in_as'        => '',
                    'comment_field'       => '',
                );

                $name_email_required = (bool) get_option( 'require_name_email', 1 );
                $fields              = array(
                    'author' => array(
                        'label'    => __( 'Name', 'woocommerce' ),
                        'type'     => 'text',
                        'value'    => $commenter['comment_author'],
                        'required' => $name_email_required,
                    ),
                    'email'  => array(
                        'label'    => __( 'Email', 'woocommerce' ),
                        'type'     => 'email',
                        'value'    => $commenter['comment_author_email'],
                        'required' => $name_email_required,
                    ),
                );

                $comment_form['fields'] = array();

                foreach ( $fields as $key => $field ) {
                    $field_html  = '<p class="comment-form-' . esc_attr( $key ) . '">';
                    $field_html .= '<label for="' . esc_attr( $key ) . '">' . esc_html( $field['label'] );

                    if ( $field['required'] ) {
                        $field_html .= '&nbsp;<span class="required">*</span>';
                    }

                    $field_html .= '</label><input id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" type="' . esc_attr( $field['type'] ) . '" value="' . esc_attr( $field['value'] ) . '" size="30" ' . ( $field['required'] ? 'required' : '' ) . ' /></p>';

                    $comment_form['fields'][ $key ] = $field_html;
                }

                $account_page_url = wc_get_page_permalink( 'myaccount' );
                if ( $account_page_url ) {
                    /* translators: %s opening and closing link tags respectively */
                    $comment_form['must_log_in'] = '<p class="must-log-in">' . sprintf( esc_html__( 'You must be %1$slogged in%2$s to post a review.', 'woocommerce' ), '<a href="' . esc_url( $account_page_url ) . '">', '</a>' ) . '</p>';
                }

                if ( wc_review_ratings_enabled() && is_user_bought_product_shkrn() && $show_stars ) {
                    $comment_form['comment_field'] = '<div class="comment-form-rating flex-center"><label for="rating" class="flex-center">' . 'امتیاز شما به دوره:' . ( wc_review_ratings_required() ? '&nbsp;<span class="required">*</span>' : '' ) . '</label><select name="rating" id="rating" required>
						<option value="">' . esc_html__( 'Rate&hellip;', 'woocommerce' ) . '</option>
						<option value="5">' . esc_html__( 'Perfect', 'woocommerce' ) . '</option>
						<option value="4">' . esc_html__( 'Good', 'woocommerce' ) . '</option>
						<option value="3">' . esc_html__( 'Average', 'woocommerce' ) . '</option>
						<option value="2">' . esc_html__( 'Not that bad', 'woocommerce' ) . '</option>
						<option value="1">' . esc_html__( 'Very poor', 'woocommerce' ) . '</option>
					</select></div>';
                }

                $comment_form['comment_field'] .= '<p class="comment-form-comment"><label for="comment">' . esc_html__( 'Your review', 'woocommerce' ) . '&nbsp;<span class="required">*</span></label><textarea id="comment" name="comment" cols="45" rows="8" required></textarea></p>';

                // $woocommerce_comment_args = apply_filters( 'woocommerce_product_review_comment_form_args', $comment_form );

                $text_is_buyer = $is_buyer ? 'و به عنوان (خریدار محصول)' : '';

                $shkrn_comments_args = array(
                    'title_reply' => '',
                    'must_log_in' => 'برای ارسال نظر لطفا ابتدا وارد حساب کاربری خود شوید.',
                    'logged_in_as' => 'این دیدگاه با نام ('.$current_user_nickname.') '.$text_is_buyer.' منتشر خواهد شد '.'<a href="'. esc_url(wc_get_account_endpoint_url("customer-logout")).'" class="log-out-shkrn-comment-form-btn">خروج از حساب کاربری</a>',
                    'title_reply_to' => 'ارسال پاسخ به دیدگاه '.$name_comment_auther_respond,
                    'title_reply_before' => '<h3 id="reply-title-shkrn" class="comments-title-shkrn highlight-box-shkrn flex-center-in-col">',
                    'title_reply_after' => '</h3>',
                    'cancel_reply_before' => '<span class="comment-item-btns-shkrn">',
                    'cancel_reply_after' => '</span>',
                    'comment_field' => $comment_form['comment_field'],
                    'comment_notes_after' => '',
                    'label_submit'        => $comment_form['label_submit'],
                );
                comment_form($shkrn_comments_args);
                ?>
            </div>
        </div>
    <?php } ?>

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
                'callback' => 'shkrn_comment_list_product',
                'style' => 'div'
			)
		); ?>
    </div>
</div>
<?php
function shkrn_comment_list_product( $comment, $args, $depth ) {
    if ( 'div' === $args['style'] ) {
        $tag       = 'div';
        $add_below = 'comment';
    } else {
        $tag       = 'li';
        $add_below = 'div-comment';
    }
    $comment_classes = array(
        empty( $args['has_children'] ) ? '' : 'parent',
        'comment-shkrn-body'
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

            if ( 'div' != $args['style'] ) { ?>
                <div id="div-comment-<?php comment_ID() ?>" class="comment-body">
            <?php } ?>
            <div class="comment-author-shkrn flex-center vcard">
                <?php
                echo shkrn_user_avatar($comment->user_id,80); ?>
                <span class="comment-author-name-shkrn">
                    <?php echo shkrn_make_user_nickname($comment->user_id); ?>
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
                <span class="rating-start-comment-shkrn shkrn-product-rate-stars flex-center">
                    <?php
                    $rating = intval( get_comment_meta( $comment->comment_ID, 'rating', true ) );
                    if ( $rating && wc_review_ratings_enabled() ) {
                        echo wc_get_rating_html( $rating ); // WPCS: XSS ok.
                    }
                    if (shkrn_options('product-review-label')) {
                        if (user_can($comment->user_id, 'administrator')) {
                            $admin_label_review = product_type_shkrn() == 'course' ? "مدرس دوره" : "مدیر سایت" ;
                            echo '<span class="adminstrator-comment-label">'.$admin_label_review.'</span> ';
                        } elseif (get_option('woocommerce_review_rating_verification_label') === 'yes') {
                            $buyer_label_review = product_type_shkrn() == 'course' ? "دانشجوی دوره" : "خریدار محصول" ;
                            if (wc_customer_bought_product($comment->comment_author_email, $comment->user_id, $comment->comment_post_ID)) {
                                echo '<span class="customer-comment-label">'.$buyer_label_review.'</span> ';
                            }
                        }
                    }
                    ?>
                </span>
            </div>
            <!-- .comment-details -->
            <?php
            if ( 'div' != $args['style'] ) { ?>
                </div>
            <?php }
            break;
    endswitch;
}
