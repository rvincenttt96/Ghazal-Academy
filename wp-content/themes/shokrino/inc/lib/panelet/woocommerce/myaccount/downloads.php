<?php
/**
 * Downloads
 *
 * Shows downloads on the account page.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/downloads.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.8.0
 */

// Panelet Edition

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$pnlt_downloads = WC()->customer->get_downloadable_products();
$has_downloads = (bool) $pnlt_downloads;

if( $has_downloads ) {
	$downloads = array();
	foreach ($pnlt_downloads as $download) {
		$downloads[$download['product_id']][$download['order_id']][] = $download;
	}
}

do_action( 'woocommerce_before_account_downloads', $has_downloads );

if (function_exists('spot_shortcode')) { ?>
	<div class="title-spot-user-panel" style="background: #6611DD">
		<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Layer_1" width="50px" height="50px"x="0px" y="0px" viewBox="0 0 92 92" style="enable-background:new 0 0 92 92;" xml:space="preserve">
        <style type="text/css">
			.st0{fill:#FFFFFF;}
			.st1{fill:#6611DD;}
		</style>
			<path class="st0" d="M46,72.2c-13.2-4.9-20.5-13.5-21.8-25.9c-0.5-3.8-0.7-7.4-0.7-11.1l0.1-9c9.3,0,16.8-2.9,22.3-8.5  c5.7,5.7,13.1,8.5,22.4,8.5l0.2,9c0,3.6-0.3,7.7-0.8,12.3c-0.5,4.6-2.4,9.1-5.6,13.7C59,65.8,53.5,69.5,46,72.2z"/>
			<path class="st1" d="M40.3,56.5c0.1,0,5.5-3.7,16.3-11.2c0.2-0.1,0.3-0.3,0.3-0.4c0-0.2-0.1-0.4-0.3-0.5  C45.9,37,40.5,33.3,40.3,33.3c-0.4,0-0.6,0.2-0.6,0.6V56C39.8,56.4,40,56.5,40.3,56.5z"/>
        </svg>
		<h3><?php echo "دوره های اسپات پلیر"; ?></h3>
	</div>
	<?php echo do_shortcode('[spotplayer_courses]'); } ?>

<?php if ( $has_downloads ) : ?>

	<?php do_action( 'woocommerce_before_available_downloads' ); ?>

	<div class="my-downloadds-userpanel">
		<?php
		foreach ( $downloads as $product_id => $orders_download ) :
			$order_array = array_column($orders_download, 0)[0];
			$product_id = $order_array['product_id']; ?>
			<div class="outer-box-download-shokrino-my-downloads">
				<div class="box-download-shokrino-my-downloads">
					<?php $image = wp_get_attachment_image_src( get_post_thumbnail_id( $product_id ), 'single-post-thumbnail' );?>
					<img src="<?php echo $image[0]; ?>" width="80px" height="auto">
					<h3 class="product-download-title-user-panel"><?php echo get_the_title($product_id) ?></h3>
					<a class="show-product-user-panel-dl" href="<?php echo $order_array['product_url']; ?>">
						<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-hand-finger" width="28" height="28" viewBox="0 0 24 24" stroke-width="1.5" stroke="#ffffff" fill="none" stroke-linecap="round" stroke-linejoin="round">
							<path stroke="none" d="M0 0h24v24H0z" fill="none"/>
							<path d="M8 13v-8.5a1.5 1.5 0 0 1 3 0v7.5" />
							<path d="M11 11.5v-2a1.5 1.5 0 1 1 3 0v2.5" />
							<path d="M14 10.5a1.5 1.5 0 0 1 3 0v1.5" />
							<path d="M17 11.5a1.5 1.5 0 0 1 3 0v4.5a6 6 0 0 1 -6 6h-2h.208a6 6 0 0 1 -5.012 -2.7a69.74 69.74 0 0 1 -.196 -.3c-.312 -.479 -1.407 -2.388 -3.286 -5.728a1.5 1.5 0 0 1 .536 -2.022a1.867 1.867 0 0 1 2.28 .28l1.47 1.47" />
						</svg>
						<?php echo "مشاهده محصول"; ?>
					</a>
				</div>
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

<?php else : ?>
	<div class="woocommerce-Message woocommerce-Message--info woocommerce-info">
		<a class="woocommerce-Button button" href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>">
			<?php esc_html_e( 'Browse products', 'woocommerce' ); ?>
		</a>
		<?php
			esc_html_e('No downloads available yet.', 'woocommerce');
		?>
	</div>
<?php endif; ?>

<?php do_action( 'woocommerce_after_account_downloads', $has_downloads ); ?>

