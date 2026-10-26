<?php
/**
 * My Account Dashboard
 *
 * Shows the first intro screen on the account dashboard.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/dashboard.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @author      WooThemes
 * @package     WooCommerce/Templates
 * @version     4.4.0
 */

// Panelet Edition

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
$orders_on_panel = pnlt_option('orders_show');
$tickets_on_panel = pnlt_option('tickets_show');
$notificationsystem_on_panel = pnlt_option('notifications_on_dashboard_enable');
$notification_panel = pnlt_option('notification_panel');
$notification_panel_text = pnlt_option('notification_panel_text');
$show_coupon_panel = pnlt_option('show_coupon_panel');
$text_coupon_panel = pnlt_option('coupon_panel_text');
?>

    <div class="user-panel-dashboard-pnlt">
        <div class="box-inner-content dashboard-item-pnlt first-item-dash-pnlt">
            <div class="name-userpanel-welcome">
                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="60" height="60" x="0" y="0" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><g xmlns="http://www.w3.org/2000/svg"><g><g clip-rule="evenodd" fill-rule="evenodd"><path d="m7.5 357.25v112.5c0 8.25 6.75 15 15 15h467c8.249 0 15-6.75 15-15v-112.5c0-8.249-6.75-15-15-15h-467c-8.25 0-15 6.75-15 15z" fill="#FFFFFF" data-original="#ffffff"></path><path d="m121 27.25v315h30v-285h210l-.001 285h30.001v-315z" fill="#FFF5BD" data-original="#fff5bd"></path><path d="m226 94.749v210.001l-75 37.5h210l-75.001-37.499v-210.002l75.001-37.499h-210z" fill="#C6D2DB" data-original="#c6d2db"></path><path d="m285.999 94.749v210.001l75 37.499.001-285zm-59.999 210.001v-210.001l-75-37.499v285z" fill="#FFC277" data-original="#ffc277"></path><path d="m121 319.75v22.5h30v-22.5zm239.999 0v22.5h30v-22.5z" fill="#FEE95D" data-original="#fee95d"></path><path d="m173.5 68.499-22.5-11.249v285l22.5-11.25zm165 0 22.5-11.249v285l-22.5-11.25z" fill="#F69500" data-original="#f69500"></path><path d="m7.5 357.25v112.5c0 8.249 6.75 15 15 15h22.5c-8.25 0-15-6.75-15-15v-112.5c0-8.25 6.75-15 15-15h-22.5c-8.249 0-15 6.751-15 15z" fill="#9CE4F8" data-original="#9ce4f8" class=""></path></g><g><path d="m338.5 147.25h-15v-15h15zm0-30h-15v-15h15z" fill="#FFFFFF" data-original="#ffffff"></path></g></g><g><path d="m393.69 414.433-9.57-32.437h-14.053l-11.211 62.298 14.763 2.657 5.314-29.528 8.242 27.936h13.439l7.834-28.001 5.804 29.684 14.722-2.879-12.162-62.191-14.056.053z" fill="#000000" data-original="#000000"></path><path d="m169.729 381.068v64.865h32.475v-15h-17.475v-49.865z" fill="#000000" data-original="#000000"></path><path d="m153.273 396.482v-15h-35.836v64.451h35.836v-15h-20.836v-9.725h19.3v-15h-19.3v-9.726z" fill="#000000" data-original="#000000"></path><path d="m245.635 396.047c3.498 0 6.871 1.029 9.753 2.977l6.215 4.198 8.397-12.43-6.214-4.199c-5.372-3.629-11.648-5.547-18.15-5.547-17.895 0-32.453 14.559-32.453 32.453 0 17.895 14.559 32.454 32.453 32.454 8.584 0 16.208-3.454 21.467-9.725l4.819-5.747-11.493-9.639-4.819 5.747c-2.428 2.896-5.784 4.364-9.974 4.364-9.624 0-17.453-7.83-17.453-17.454-.001-9.623 7.829-17.452 17.452-17.452z" fill="#000000" data-original="#000000"></path><path d="m72.075 382.23-13.47-.018-8.348 28.866-6.053-30.601-14.715 2.911 12.383 62.601 13.931-.095 9.523-32.93 9.522 33.022 13.922-.096 12.419-62.4-14.711-2.928-6.079 30.537z" fill="#000000" data-original="#000000"></path><path d="m282.641 413.5c0 17.895 14.559 32.453 32.453 32.453s32.453-14.559 32.453-32.453-14.559-32.453-32.453-32.453-32.453 14.558-32.453 32.453zm49.906 0c0 9.624-7.83 17.453-17.453 17.453s-17.453-7.83-17.453-17.453 7.83-17.453 17.453-17.453 17.453 7.829 17.453 17.453z" fill="#000000" data-original="#000000"></path><path d="m481.198 396.482v-15h-35.836v64.451h35.836v-15h-20.836v-9.725h19.3v-15h-19.3v-9.726z" fill="#000000" data-original="#000000"></path><path d="m437 477.25h15v15h-15z" fill="#000000" data-original="#000000"></path><path d="m489.5 334.75h-91v-315h-285v315h-91c-12.407 0-22.5 10.093-22.5 22.5v112.5c0 12.406 10.093 22.5 22.5 22.5h399.5v-15h-399.5c-4.135 0-7.5-3.364-7.5-7.5v-112.5c0-4.135 3.365-7.5 7.5-7.5h467c4.135 0 7.5 3.365 7.5 7.5v112.5c0 4.135-3.365 7.5-7.5 7.5h-22.5v15h22.5c12.407 0 22.5-10.093 22.5-22.5v-112.5c0-12.407-10.093-22.5-22.5-22.5zm-361-300h255v300h-15v-285h-225v52.499h15v-32.864l60 30v200.731l-60 29.999v-212.866h-15v217.501h-15zm54.272 30h146.457l-50.729 25.364v219.271l50.729 25.364h-146.458l50.729-25.364v-219.271zm170.728 4.635v260.729l-60-30v-200.729z" fill="#000000" data-original="#000000"></path><path d="m308.499 207.25h15v22.5h-15z" fill="#000000" data-original="#000000"></path><path d="m188.501 207.25h15v22.5h-15z" fill="#000000" data-original="#000000"></path></g></g></g></svg>
                <div class="name-userpanel-welcome-inner">
                    <h3>
                        <?php
                        $current_user = wp_get_current_user();
                        if (!empty($current_user->display_name)) {
                            echo $current_user->display_name;
                        } else {
                            echo _e('Site user','shokrino');
                        }
                        ?>
                    </h3>
                    <p class="welcome-text">
                        به پنل کاربری وبسایت&nbsp;&nbsp;<strong><?php echo get_bloginfo( 'name' ); ?></strong>&nbsp;&nbsp;خوش آمدید
                    </p>
                </div>
            </div><?php
            if ($show_coupon_panel && !empty($text_coupon_panel)) {
                ?>
                <div class="discount-coupon">
                    <div class="title-and-icon-discount">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-ticket" width="28" height="28" viewBox="0 0 24 24" stroke-width="1" stroke="#294559" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <line x1="15" y1="5" x2="15" y2="7" />
                            <line x1="15" y1="11" x2="15" y2="13" />
                            <line x1="15" y1="17" x2="15" y2="19" />
                            <path d="M5 5h14a2 2 0 0 1 2 2v3a2 2 0 0 0 0 4v3a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-3a2 2 0 0 0 0 -4v-3a2 2 0 0 1 2 -2" />
                        </svg>
                        کد تخفیف شما:
                    </div>
                    <div class="coupon-code">
                        <?php echo $text_coupon_panel; ?>
                    </div>
                </div>
            <?php } ?>
        </div>
        <div class="box-inner-content dashboard-item-pnlt calendar-item-dash-pnlt">
            <div class="days-of-membership">
                <?php
                $today_obj      = new DateTime( date( 'Y-m-d', strtotime( 'today' ) ) );
                $register_date  = get_the_author_meta( 'user_registered', get_current_user_id() );
                $registered_obj = new DateTime( date( 'Y-m-d', strtotime( $register_date ) ) );
                $interval_obj   = $today_obj->diff( $registered_obj );
                if( $interval_obj->days > 1 ) {
                    ?>
                    <p class='first-text-days'>
                        عضویت از
                    </p><strong><?php echo $interval_obj->days; ?></strong>
                    <p class='end-text-days'>
                        روز پیش
                    </p>
                    <?php
                } elseif( 0 == $interval_obj->days ) {
                    ?>
                    امروز عضو شدید
                    <?php
                } elseif( 1 == $interval_obj->days ) {
                    ?>
                    دیروز عضو شدید
                    <?php
                } else {
                    ?>
                    عضو شده اید
                    <?php
                }
                ?>
            </div>
            <ul class="circles-pnlt">
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
            </ul>
        </div>
        <?php if ($notification_panel && !empty($notification_panel_text)) { ?>
            <div class="box-inner-content dashboard-item-pnlt notice-admin-dash-pnlt">
                <div class="admin-message-box-pnlt">
                    <div class="title-notification-pnlt title-dash-pnlt"><?php echo "پیام مدیر سایت"; ?></div>
                    <div class="text-notification-pnlt">
                        <?php echo $notification_panel_text; ?>
                    </div>
                </div>
            </div>
        <?php } ?>
<!--        <div class="box-inner-content dashboard-item-pnlt slider-item-dash-pnlt">6</div>-->
        <?php if ($orders_on_panel) { ?>
            <div class="box-inner-content dashboard-item-pnlt orders-dash-pnlt">
                <div class="title-orders-pnlt title-dash-pnlt"><?php echo "آخرین سفارشات شما"; ?></div>
                    <div class="user-panel-orders-pnlt">
                        <?php
                        $customer_orders = wc_get_orders(array(
                            'customer' => get_current_user_id(),
                            'limit' => 2,
                            'orderby' => 'date',
                            'order' => 'DESC',
                        ));

                        foreach ( $customer_orders as $customer_order ) {
                            $order      = wc_get_order( $customer_order ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
                            $item_count = $order->get_item_count() - $order->get_item_count_refunded();
                            $actions = wc_get_account_orders_actions( $order );
                            $order_url = esc_url($actions['view']['url']);
                            $order_view = esc_attr($actions['view']['name']);
                            $order_number = $order->get_order_number();
                            $order_items = $order->get_items('line_item');
                            $order_status = $order->get_status();
                            $order_total = $order->get_formatted_order_total();
                            $dateformatter = new IntlDateFormatter(
                                'fa_IR@calendar=persian',
                                IntlDateFormatter::LONG,
                                IntlDateFormatter::NONE,
                                'Asia/Tehran',
                                IntlDateFormatter::TRADITIONAL
                            );
                            $order_date = $dateformatter->format($order->get_date_created('j M'));
                            if (!empty($order_items)) {
                                $first_order_item = reset($order_items);
                                $product_id = $first_order_item->get_product_id();
                                $product = wc_get_product($product_id);
                                if ($product && ! is_wp_error($product)) {
                                    $product_name = $product->get_name();
                                }
                                $featured_image_url = get_the_post_thumbnail_url($product_id, 'thumbnail');
                                if (empty($featured_image_url)) {
                                    $placeholder_image_id = get_option('woocommerce_placeholder_image', 0);
                                    $featured_image_url = wp_get_attachment_url($placeholder_image_id);
                                }
                            }
                            switch ($order_status) {
                                case 'pending':
                                case 'on-hold':
                                case 'pending-payment':
                                case 'pending-shipment':
                                case 'pending-approval':
                                    $status_class = 'yellow-status-order-pnlt';
                                    break;
                                case 'failed':
                                case 'cancelled':
                                case 'failed-shipment':
                                    $status_class = 'red-status-order-pnlt';
                                    break;
                                case 'processing':
                                case 'completed':
                                case 'refunded':
                                    $status_class = 'green-status-order-pnlt';
                                    break;
                                default:
                                    $status_class = '';
                                    break;
                            }
                            ?>
                            <a href="<?php echo $order_url; ?>" class="box-order-item">
                                <img class="order-product-image-pnlt" src="<?php echo $featured_image_url; ?>">
                                <div class="order-item-pnlt name-id-order-pnlt">
                                    <span class="order-product-name-pnlt">
                                        <?php
                                        if ($item_count > 1) {
                                            $product_name .= sprintf( '<span class="other-order-items-pnlt"> و %1$s محصول دیگر</span>',  $item_count - 1 );
                                        }
                                        echo $product_name; ?>
                                    </span>
                                    <span class="order-item-title-pnlt">
                                        شماره سفارش:
                                        <strong>
                                            <?php echo $order_number; ?>
                                        </strong>
                                    </span>
                                </div>
                                <div class="order-item-pnlt datetime-order-pnlt">
                                    <time datetime="<?php echo esc_attr( $order->get_date_created()->date( 'c' ) ); ?>"><?php echo esc_html( $order_date ); ?></time>
                                </div>
                                <div class="order-item-pnlt total-order-pnlt">
                                    <?php echo $order_total; ?>
                                </div>
                                <div class="order-item-pnlt status-order-pnlt">
                                    <span class="order-item-status-pnlt <?php echo $status_class; ?>">
                                        <svg class="icon-status-pnlt icon-processing-pnlt" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                          <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                          <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
                                          <path d="M21 21l-6 -6" />
                                        </svg>
                                        <svg class="icon-status-pnlt icon-completed-pnlt" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                          <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                          <path d="M5 12l5 5l10 -10" />
                                        </svg>
                                        <svg class="icon-status-pnlt icon-failed-pnlt" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">                              <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                          <path d="M18 6l-12 12" />
                                          <path d="M6 6l12 12" />
                                        </svg>
                                        <?php echo esc_html( wc_get_order_status_name( $order_status ) ); ?>
                                    </span>
                                </div>
                                <div class="order-item-pnlt view-order-pnlt">
                                    <span class="order-item-view-pnlt">
                                        <?php echo $order_view; ?>
                                    </span>
                                </div>
                            </a>
                        <?php } ?>
                    </div>
            </div>
        <?php } ?>
        <?php if ($tickets_on_panel) { ?>
            <div class="box-inner-content dashboard-item-pnlt orders-dash-pnlt">
                <div class="title-orders-pnlt title-dash-pnlt"><?php echo "آخرین تیکت شما"; ?></div>
                <div class="tickets-pnlt-list-box user-panel-orders-pnlt">
                    <?php
                    $current_user_id = get_current_user_id();
                    $tickets = Panelet_TICKET::get_tickets($current_user_id);
                    if (is_array($tickets)) {
                        foreach ($tickets as $message) {
                            $ticket_id = $message['ticket_id'];
                            $ticket_date = strtotime($message['updated_at']);
                            $formatter = new IntlDateFormatter(
                                'fa_IR@calendar=persian',
                                IntlDateFormatter::LONG,
                                IntlDateFormatter::SHORT,
                                'Asia/Tehran',
                                IntlDateFormatter::TRADITIONAL
                            );
                            $ticket_date = $formatter->format($ticket_date);
                            if ($message['status'] == "open") {
                                $ticket_status = "در انتظار پاسخ";
                                $ticket_status_class = "yellow-wait-pnlt";
                            } else {
                                $ticket_status = "پاسخ داده شده";
                                $ticket_status_class = "green-answered-pnlt";
                            }
                            ?>
                            <a href="<?php echo wc_get_account_endpoint_url('p-tickets') . "?view-ticket=" . $ticket_id; ?>" class="box-order-item">
                                <div class="order-item-pnlt name-id-order-pnlt">
                                <span class="order-product-name-pnlt">
                                    <?php echo $message['title']; ?>
                                </span>
                                    <span class="order-item-title-pnlt">
                                    شماره تیکت:
                                    <strong>
                                        <?php echo $ticket_id; ?>
                                    </strong>
                                </span>
                                </div>
                                <div class="order-item-pnlt datetime-order-pnlt">
                                    <time><?php echo $ticket_date; ?></time>
                                </div>
                                <div class="order-item-pnlt status-order-pnlt">
                                <span class="status-ticket-pnlt <?php echo $ticket_status_class; ?>">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-chart-pie-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                      <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                      <path d="M12 3v9h9" />
                                      <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                    </svg>
                                    <?php echo $ticket_status; ?>
                                </span>
                                </div>
                                <div class="order-item-pnlt view-order-pnlt">
                                <span class="order-item-view-pnlt">
                                    <?php echo "مشاهده تیکت"; ?>
                                </span>
                                </div>
                            </a>
                            <?php
                        }
                    } else {
                        ?>
                        <a class="box-order-item">هیچ تیکتی تاکنون ارسال نشده است</a>
                        <?php
                    }
                    ?>
                </div>
            </div>
        <?php } ?>
        <?php if ($notificationsystem_on_panel) { ?>
            <div class="box-inner-content dashboard-item-pnlt notice-admin-dash-pnlt">
                <div class="admin-message-box-pnlt">
                    <div class="title-notification-pnlt title-dash-pnlt"><?php echo "اعلان های عمومی"; ?></div>
                    <div class="list-notifications">
                        <?php
                        $user_messages = get_posts( array(
                            'numberposts' => -1,
                            'meta_key'    => 'notif_type',
                            'meta_value'  => 'public',
                            'post_type'   => 'notification',
                            'post_status' => 'publish',
                        ) );
                        if ( $user_messages ) {
                            foreach ($user_messages as $value) {
                                ?>
                                <div class="notif-item">
                                    <div class="notif-header">
                                        <h3 class="notif"><?php echo $value->post_title; ?></h3>
                                        <span>
                                    <?php
                                    $date_notif = $value->post_date;
                                    echo date( 'H:i', strtotime( $date_notif ));
                                    echo '&nbsp;&nbsp;&nbsp;';
                                    echo date( 'Y/m/d', strtotime( $date_notif ));
                                    ?>
                                </span>
                                    </div>
                                    <div class="notif-content">
                                        <p><?php echo $value->post_content; ?></p>
                                    </div>
                                </div>
                                <?php
                            }
                        } else { ?>
                            <span>پیامی وجود ندارد!</span>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="box-inner-content dashboard-item-pnlt notice-admin-dash-pnlt">
                <div class="admin-message-box-pnlt">
                    <div class="title-notification-pnlt title-dash-pnlt"><?php echo "اعلان های خصوصی"; ?></div>
                    <div class="list-notifications">
                        <?php
                        $user = wp_get_current_user();

                        $user_messages = get_posts( array(
                            'numberposts' => -1,
                            'meta_key'    => 'notif_type',
                            'meta_value'  => 'private',
                            'post_type'   => 'notification',
                            'post_status' => 'publish',
                        ) );

                        if ( $user_messages ){
                            foreach ($user_messages as $post) {
                                $post_id = $post->ID;
                                $selected_buyers_option = get_post_meta($post_id, 'selected_buyers_option', true);
                                $selected_product_id = get_post_meta($post_id, 'buyers_of_product', true);
                                $selected_user_id = get_post_meta($post_id, 'selected_users', true);
                                $show_status = true;
                                if ($selected_buyers_option == "select_users") {
                                    $show_status = $selected_user_id == $user->ID ? true : false;
                                } elseif ($selected_buyers_option == "select_buyers_of_product") {
                                    $show_status = wc_customer_bought_product($user->user_email, $user->ID, $selected_product_id) ? true : false;
                                }
                                if ($show_status) {
                                    ?>
                                    <div class="notif-item">
                                        <div class="notif-header">
                                            <h3 class="private"><?php echo $post->post_title; ?></h3>
                                            <span>
                                        <?php
                                        $date_notif = $post->post_date;
                                        echo date('H:i', strtotime($date_notif));
                                        echo '&nbsp;&nbsp;&nbsp;';
                                        echo date('Y/m/d', strtotime($date_notif));
                                        ?>
                                    </span>
                                        </div>
                                        <div class="notif-content">
                                            <p><?php echo $post->post_content; ?></p>
                                        </div>
                                    </div>
                                    <?php
                                } else {
                                    ?>
                                    <span>پیامی وجود ندارد!</span>
                                <?php }
                            }
                        } else {
                            ?>
                            <span>پیامی وجود ندارد!</span>
                        <?php } ?>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
<?php
/**
 * My Account dashboard.
 *
 * @since 2.6.0
 */
do_action( 'woocommerce_account_dashboard' );

/**
 * Deprecated woocommerce_before_my_account action.
 *
 * @deprecated 2.6.0
 */
do_action( 'woocommerce_before_my_account' );

/**
 * Deprecated woocommerce_after_my_account action.
 *
 * @deprecated 2.6.0
 */
do_action( 'woocommerce_after_my_account' );

/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
