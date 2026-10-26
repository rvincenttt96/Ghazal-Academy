<?php
/**
 * Show options for ordering
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/orderby.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     9.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="row-shop-woocommerce-orderby flex-center-in-col">
    <span class="title-orderby-shkrn flex-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-list" viewBox="0 0 24 24" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke="var(--shkrn-var-main-color)" stroke-width="2.5" width="27" height="27"> <path stroke="none" d="M0 0h24v24H0z" fill="none"></path> <line x1="9" y1="6" x2="20" y2="6"></line> <line x1="9" y1="12" x2="20" y2="12"></line> <line x1="9" y1="18" x2="20" y2="18"></line> <line x1="5" y1="6" x2="5" y2="6.01"></line> <line x1="5" y1="12" x2="5" y2="12.01"></line> <line x1="5" y1="18" x2="5" y2="18.01"></line> </svg>
        مرتب‌سازی براساس
    </span>
    <form class="woocommerce-ordering flex-center" style="margin: 0" method="get">
        <?php foreach ( $catalog_orderby_options as $id => $name ) :
            if (isset($_GET['orderby']) && $_GET['orderby'] == esc_attr( $id )) {
                $class_active = ' '."active";
            } elseif (!isset($_GET['orderby']) && esc_attr( $id ) == 'menu_order') {
                $class_active = ' '.'active';
            } else {
                $class_active = '';
            } ?>
            <button type="submit" name="orderby" value="<?php echo esc_attr( $id ); ?>" class="orderby-shkrn<?php echo $class_active; ?>" aria-label="<?php esc_attr_e( 'Shop order', 'woocommerce' ); ?>"><?php echo esc_html(str_replace("مرتب‌سازی بر اساس", "",$name)); ?></button>
        <?php endforeach; ?>
        <input type="hidden" name="paged" value="1" />
        <?php wc_query_string_form_fields( null, array( 'orderby', 'submit', 'paged', 'product-page' ) ); ?>
    </form>
</div>