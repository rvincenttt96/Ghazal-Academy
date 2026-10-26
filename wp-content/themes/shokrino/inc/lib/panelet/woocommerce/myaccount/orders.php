<?php
/**
 * Orders
 *
 * Shows orders on the account page.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/orders.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.8.0
 */

// Panelet Edition

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_account_orders', $has_orders ); ?>

<?php if ( $has_orders ) : ?>

    <div class="user-panel-orders-pnlt">
        <?php
			foreach ( $customer_orders->orders as $customer_order ) {
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
            <?php
			}
        ?>
    </div>

	<?php do_action( 'woocommerce_before_account_orders_pagination' ); ?>

	<?php if ( 1 < $customer_orders->max_num_pages ) : ?>
		<div class="woocommerce-pagination woocommerce-pagination--without-numbers woocommerce-Pagination">
			<?php if ( 1 !== $current_page ) : ?>
				<a class="woocommerce-button woocommerce-button--previous woocommerce-Button woocommerce-Button--previous button<?php echo esc_attr( $wp_button_class ); ?>" href="<?php echo esc_url( wc_get_endpoint_url( 'orders', $current_page - 1 ) ); ?>"><?php esc_html_e( 'Previous', 'woocommerce' ); ?></a>
			<?php endif; ?>

			<?php if ( intval( $customer_orders->max_num_pages ) !== $current_page ) : ?>
				<a class="woocommerce-button woocommerce-button--next woocommerce-Button woocommerce-Button--next button<?php echo esc_attr( $wp_button_class ); ?>" href="<?php echo esc_url( wc_get_endpoint_url( 'orders', $current_page + 1 ) ); ?>"><?php esc_html_e( 'Next', 'woocommerce' ); ?></a>
			<?php endif; ?>
		</div>
	<?php endif; ?>

<?php else : ?>

	<?php wc_print_notice( esc_html__( 'No order has been made yet.', 'woocommerce' ) . ' <a class="woocommerce-Button button' . esc_attr( $wp_button_class ) . '" href="' . esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ) . '">' . esc_html__( 'Browse products', 'woocommerce' ) . '</a>', 'notice' ); // phpcs:ignore WooCommerce.Commenting.CommentHooks.MissingHookComment ?>

<?php endif; ?>

<?php do_action( 'woocommerce_after_account_orders', $has_orders ); ?>
