<?php
/**
 * Checkout coupon form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-coupon.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.8.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! wc_coupons_enabled() ) { // @codingStandardsIgnoreLine.
	return;
}

?>
<?php if ( wc_coupons_enabled() ) { ?>
	<div class="cart-col-box-shkrn coupon-cart-item-shkrn flex-center">
		<form class="checkout_coupon" method="post">
			<label for="coupon_code"><?php esc_html_e( 'Coupon:', 'woocommerce' ); ?></label>
			<div class="box-focus-coupon-shkrn flex-center">
				<input type="text" name="coupon_code" class="input-text" placeholder="<?php esc_attr_e( 'Coupon code', 'woocommerce' ); ?>" id="coupon_code" value="" />
				<button type="submit" class="btn-submit-coupon-shkrn" name="apply_coupon" value="<?php esc_attr_e( 'اعمال کد', 'shokrino' ); ?>"><?php esc_html_e( 'اعمال کد', 'shokrino' ); ?></button>
			</div>
			<?php
			if (!empty(WC()->cart->get_coupons())) { ?>
				<div class="cart-col-box-shkrn coupon-enable-cart-item-shkrn flex-center">
					<label class="active-coupons-box-shkrn">
						<?php _e('کدتخفیف های فعال:','shokrino') ?>
					</label>
					<?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
						<div class="item-active-coupon-shkrn flex-center">
							<?php get_coupon_remove_btn_shkrn($coupon);
							get_coupon_parts_shkrn( $coupon ); ?>
						</div>
					<?php endforeach; ?>
				</div>
			<?php } ?>
		</form>
	</div>
<?php } ?>