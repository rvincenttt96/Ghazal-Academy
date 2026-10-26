<?php
/**
 * View Order
 *
 * Shows the details of a particular order on the account page.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/view-order.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.0.0
 */

// Panelet Edition

defined( 'ABSPATH' ) || exit;

$order = wc_get_order( $order_id ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited

if ( ! $order ) {
    return;
}

$order_items           = $order->get_items( apply_filters( 'woocommerce_purchase_order_item_types', 'line_item' ) );
$show_purchase_note    = $order->has_status( apply_filters( 'woocommerce_purchase_note_order_statuses', array( 'completed', 'processing' ) ) );
$show_customer_details = is_user_logged_in() && $order->get_user_id() === get_current_user_id();
$downloads             = $order->get_downloadable_items();
$show_downloads        = $order->has_downloadable_item() && $order->is_download_permitted();
$item_count = $order->get_item_count() - $order->get_item_count_refunded();
$order_number = $order->get_order_number();
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
    <section class="user-panel-orders-pnlt view-order-page-pnlt">
        <h2 class="woocommerce-order-details__title"><?php esc_html_e( 'Order details', 'woocommerce' ); ?></h2>
        <?php do_action( 'woocommerce_order_details_before_order_table', $order ); ?>
        <div class="order-detail-box-pnlt">
            <div class="order-item-pnlt row-detail-order-pnlt">
                <span><?php echo "شماره سفارش: ".$order_number; ?></span>
                <time datetime="<?php echo esc_attr( $order->get_date_created()->date( 'c' ) ); ?>"><?php echo esc_html( "تاریخ ثبت سفارش:".$order_date ); ?></time>
            </div>
            <div class="order-item-pnlt row-detail-order-pnlt">
                <span><?php echo "تعداد کل: ".$item_count; ?></span>
                <span><?php echo "مجموع: ".$order_total; ?></span>
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
        </div>
        <div class="order-item-pnlt products-order-pnlt">
            <?php
            do_action( 'woocommerce_order_details_before_order_table_items', $order );

            foreach ( $order_items as $item_id => $item ) {
                if ( ! apply_filters( 'woocommerce_order_item_visible', true, $item ) ) {
                    break;
                }
                $product = $item->get_product();
                $product_id = $product->get_id();
                $product_sku = $product->get_sku();
                if ($product && ! is_wp_error($product)) {
                    $product_name = $product->get_name();
                }
                $featured_image_url = get_the_post_thumbnail_url($product_id, 'thumbnail');
                if (empty($featured_image_url)) {
                    $placeholder_image_id = get_option('woocommerce_placeholder_image', 0);
                    $featured_image_url = wp_get_attachment_url($placeholder_image_id);
                }
                $is_visible        = $product && $product->is_visible();
                $product_permalink = apply_filters( 'woocommerce_order_item_permalink', $is_visible ? $product->get_permalink( $item ) : '', $item, $order );
                $qty          = $item->get_quantity();
                $refunded_qty = $order->get_qty_refunded_for_item( $item_id );
                if ( $refunded_qty ) {
                    $qty_display = '<del>' . esc_html( $qty ) . '</del> <ins>' . esc_html( $qty - ( $refunded_qty * -1 ) ) . '</ins>';
                } else {
                    $qty_display = esc_html( $qty );
                }
                ?>
                <a href="<?php echo $product_permalink; ?>" class="box-order-item">
                    <img class="order-product-image-pnlt" src="<?php echo $featured_image_url; ?>">
                    <div class="order-item-pnlt name-id-order-pnlt">
                        <span class="order-product-name-pnlt">
                            <?php
                            echo $product_name; ?>
                        </span>
                        <?php if (!empty($product_sku)) { ?>
                            <span class="order-item-title-pnlt">
                            شناسه محصول:
                            <strong>
                                <?php echo $product_sku; ?>
                            </strong>
                        </span>
                        <?php } ?>
                    </div>
                    <div class="order-item-pnlt row-detail-order-pnlt">
                        <?php echo "تعداد: ".$qty_display; ?>
                    </div>
                    <?php
                    do_action( 'woocommerce_order_item_meta_start', $item_id, $item, $order, false );
                    wc_display_item_meta( $item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                    do_action( 'woocommerce_order_item_meta_end', $item_id, $item, $order, false );
                    ?>
                    <div class="order-item-pnlt total-order-pnlt">
                        <?php echo $order->get_formatted_line_subtotal( $item ); ?>
                    </div>
                </a>
            <?php }
            do_action( 'woocommerce_order_details_after_order_table_items', $order );
            ?>
        </div>
        <div class="order-item-pnlt row-detail-order-pnlt">
            <?php if ( $order->get_customer_note() ) : ?>
                <span>
                <strong><?php echo "یادداشت:  "; ?></strong>
                <?php echo wp_kses_post( nl2br( wptexturize( $order->get_customer_note() ) ) ); ?>
            </span>
            <?php endif; ?>
        </div>
        <?php do_action( 'woocommerce_order_details_after_order_table', $order ); ?>
    </section>

<?php
/**
 * Action hook fired after the order details.
 *
 * @since 4.4.0
 * @param WC_Order $order Order data.
 */
do_action( 'woocommerce_after_order_details', $order );

$pnlt_downloads = WC()->customer->get_downloadable_products();
$has_downloads = (bool) $pnlt_downloads;

if ( $has_downloads ) :

    $downloads = array();
    foreach ($pnlt_downloads as $download) {
        $downloads[$download['product_id']][$download['order_id']][] = $download;
    } ?>

<section class="user-panel-orders-pnlt view-order-page-pnlt">
    <h2 class="woocommerce-order-downloads__title"><?php esc_html_e( 'Downloads', 'woocommerce' ); ?></h2>
    <?php do_action( 'woocommerce_before_available_downloads' ); ?>
    <div class="my-downloadds-userpanel">
        <?php
        foreach ( $downloads as $product_id => $orders_download ) :
            $order_array = array_column($orders_download, 0)[0];
            $product_id = $order_array['product_id']; ?>
            <div class="outer-box-download-shokrino-my-downloads">
                <div class="list-of-download-files-user-panel">
                    <?php foreach($orders_download  as $order_id => $download_items ): ?>
                        <?php foreach($download_items  as $key => $download ): ?>
                            <a class="download-btn-files-user-panel" href="<?php echo esc_url( $download['download_url'] ); ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-download" width="22" height="22" viewBox="0 0 24 24" stroke-width="1.5" stroke="#ffffff" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
                                    <polyline points="7 11 12 16 17 11" />
                                    <line x1="12" y1="4" x2="12" y2="16" />
                                </svg>
                                <?php echo $download['file']['name'] ?>
                            </a>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php do_action( 'woocommerce_after_available_downloads' ); ?>
</section>

<?php endif; ?>