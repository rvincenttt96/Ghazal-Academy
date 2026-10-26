<?php
/**
 * Checkout Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-checkout.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

do_action( 'woocommerce_before_checkout_form', $checkout );

// If checkout registration is disabled and not logged in, the user cannot checkout.
if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
    echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) ) );
    return;
}

?>

    <form name="checkout" method="post" class="single-page-shkrn checkout-page-shkrn checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data" aria-label="<?php echo esc_attr__( 'Checkout', 'woocommerce' ); ?>">
        <div class="single-page-main-part checkout-main-part-shkrn">
            <h1 class="cart-page-title-shkrn entry-title">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-basket" width="30" height="30" viewBox="0 0 24 24" stroke-width="2" stroke="var(--shkrn-var-main-color)" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M7 10l5 -6l5 6" />
                    <path d="M21 10l-2 8a2 2.5 0 0 1 -2 2h-10a2 2.5 0 0 1 -2 -2l-2 -8z" />
                    <path d="M12 15m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                </svg>
                <?php _e('سبد خرید','shokrino');?>
            </h1>
            <div class="cart-items-checkout-shkrn">
                <?php do_action( 'woocommerce_before_cart_contents' );
                foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
                $_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
                $product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
                if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
                $product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key ); ?>
                <div class="cart-item-wrapper-shkrn flex-center-in-col">
                    <?php
                    if ( $product_permalink ) { ?>
                    <a href="<?php echo esc_url( $product_permalink ) ?>" class="cart-item-shkrn-url flex-center-in-col">
                        <?php } else { ?>
                        <div class="cart-item-shkrn-url flex-center-in-col">
                            <?php } ?>
                            <div class="cart-col-box-shkrn image-cart-item-shkrn flex-center">
                                <?php
                                $thumbnail = get_the_post_thumbnail($product_id,'posts');
                                echo $thumbnail;
                                ?>
                            </div>
                            <div class="cart-col-box-shkrn name-cart-item-shkrn flex-center">
                                <?php echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) . '&nbsp;' );
                                do_action( 'woocommerce_after_cart_item_name', $cart_item, $cart_item_key );
                                echo wc_get_formatted_cart_item_data( $cart_item ); // PHPCS: XSS ok.
                                if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) ) {
                                    echo wp_kses_post( apply_filters( 'woocommerce_cart_item_backorder_notification', '<p class="backorder_notification">' . esc_html__( 'Available on backorder', 'woocommerce' ) . '</p>', $product_id ) );
                                } ?>
                            </div>
                            <div class="cart-col-box-shkrn price-cart-item-shkrn price-product-shkrn flex-center">
                                <div class="price">
                                    <?php echo $_product->get_price_html(); ?>
                                </div>
                            </div>
                            <?php if ( $product_permalink ) { ?>
                    </a>
                    <?php } else { ?>
                </div>
            <?php }
            echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                'woocommerce_cart_item_remove_link',
                sprintf(
                    '<a href="%s" class="remove-item-cart-shkrn" aria-label="%s" data-product_id="%s" data-product_sku="%s">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-trash" width="27" height="27" viewBox="0 0 24 24" stroke-width="1.5" stroke="#f00" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                               <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                               <path d="M4 7l16 0"></path>
                                                               <path d="M10 11l0 6"></path>
                                                               <path d="M14 11l0 6"></path>
                                                               <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2.5 0 0 0 2 -2l1 -12"></path>
                                                               <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"></path>
                                                            </svg>
                                                            </a>',
                    esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
                    esc_html__( 'Remove this item', 'woocommerce' ),
                    esc_attr( $product_id ),
                    esc_attr( $_product->get_sku() )
                ),
                $cart_item_key
            );
            ?>
            </div>
            <?php } }
            do_action( 'woocommerce_cart_contents' );
            do_action( 'woocommerce_after_cart_contents' );
            do_action( 'woocommerce_after_cart_table' );
            do_action( 'woocommerce_after_cart' ); ?>
        </div>
        <?php if ( $checkout->get_checkout_fields() ) { ?>

            <?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

            <div class="checkout-fields-checkout-shkrn">
                <?php do_action( 'woocommerce_checkout_billing' ); ?>
            </div>
            <?php if ( shkrn_options('additional-desc-field') ) { ?>
                <div class="checkout-fields-checkout-shkrn">
                    <?php do_action( 'woocommerce_checkout_shipping' ); ?>
                </div>
            <?php } ?>

            <?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

        <?php } ?>

        <!--	--><?php //do_action( 'woocommerce_checkout_before_order_review_heading' ); ?>
        <!---->
        <!--	<h3 id="order_review_heading">--><?php //esc_html_e( 'Your order', 'woocommerce' ); ?><!--</h3>-->

        </div>
        <aside class="sidebar-single-shkrn checkout-sidebar-shkrn">
            <div class="checkout-sticky-sidebar flex-center-in-col">
                <?php do_action( 'woocommerce_checkout_before_order_review_heading' ); ?>

                <!-- <h2 class="title-coupon-shkrn-cart"><?php _e('اطلاعات پرداخت','shokrino') ?></h2> -->

                <?php do_action( 'woocommerce_checkout_before_order_review' ); ?>
                <div id="order_review" class="woocommerce-checkout-review-order-shkrn">
                    <?php do_action( 'woocommerce_checkout_order_review' ); ?>
                </div>
                <?php do_action( 'woocommerce_checkout_after_order_review' ); ?>
            </div>
        </aside>
    </form>

<?php do_action( 'woocommerce_after_checkout_form', $checkout );
function get_coupon_parts_shkrn($coupon) {
    $newcoupon = new WC_Coupon( $coupon );
    $discount_amount_html = '';
    $amount               = WC()->cart->get_coupon_discount_amount( $coupon->get_code(), WC()->cart->display_cart_ex_tax );
    $discount_amount_html = '-' . wc_price( $amount );
    if ( $coupon->get_free_shipping() && empty( $amount ) ) {
        $discount_amount_html = __( 'Free shipping coupon', 'woocommerce' );
    }
    $discount_amount_html = apply_filters( 'woocommerce_coupon_discount_amount_html', $discount_amount_html, $coupon );
    $coupon_html = $discount_amount_html . '&nbsp;&nbsp;|&nbsp;&nbsp;' . $coupon->get_code();
    echo wp_kses( apply_filters( 'woocommerce_cart_totals_coupon_html', $coupon_html, $coupon, $discount_amount_html ), array_replace_recursive( wp_kses_allowed_html( 'post' ), array( 'a' => array( 'data-coupon' => true ) ) ) );
}
function get_coupon_remove_btn_shkrn($coupon) { ?>
    <a href="<?php echo esc_url( add_query_arg( 'remove_coupon', rawurlencode( $coupon->get_code() ), is_checkout() ? wc_get_checkout_url() : wc_get_cart_url() ) ); ?>" class="remove-coupon-shkrn flex-center" data-coupon="<?php echo esc_attr( $coupon->get_code() ); ?>">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-circle-x-filled" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
            <path d="M17 3.34a10 10 0 1 1 -14.995 8.984l-.005 -.324l.005 -.324a10 10 0 0 1 14.995 -8.336zm-6.489 5.8a1 1 0 0 0 -1.218 1.567l1.292 1.293l-1.292 1.293l-.083 .094a1 1 0 0 0 1.497 1.32l1.293 -1.292l1.293 1.292l.094 .083a1 1 0 0 0 1.32 -1.497l-1.292 -1.293l1.292 -1.293l.083 -.094a1 1 0 0 0 -1.497 -1.32l-1.293 1.292l-1.293 -1.292l-.094 -.083z" stroke-width="0" fill="#f00"></path>
        </svg>
    </a>&nbsp;&nbsp;
    <?php
}