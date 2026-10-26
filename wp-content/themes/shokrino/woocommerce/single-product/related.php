<?php
/**
 * Related Products
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/related.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     9.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$product_show_related = shkrn_metaoptions('related_products_show');

if ( $related_products && $product_show_related ) : ?>

    <section class="anchor-list-on-product-shkrn posts-section-shkrn flex-center">

        <?php shkrn_anchor_maker('related-shkrn'); ?>
    <?php
    $heading = apply_filters( 'woocommerce_product_related_products_heading', __( 'Related products', 'woocommerce' ) );

    if ( $heading ) :
        ?>
        <div class="posts-section-header flex-center">
            <div class="posts-title-shkrn flex-center">
                <h2><?php echo esc_html( $heading ); ?></h2>
            </div>
        </div>
    <?php endif; ?>



        <div class="archive-grid-posts-shkrn products shkrn-grid-columns-3">

        <?php foreach ( $related_products as $related_product ) : ?>

					<?php
					$post_object = get_post( $related_product->get_id() );

					setup_postdata( $GLOBALS['post'] =& $post_object ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited, Squiz.PHP.DisallowMultipleAssignments.Found

					wc_get_template_part( 'content', 'product' );
					?>

			<?php endforeach; ?>

		<?php woocommerce_product_loop_end(); ?>

	</section>
	<?php
endif;

wp_reset_postdata();
