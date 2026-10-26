<?php
defined( 'ABSPATH' ) || exit;
/**
 * Checkout customized for shokrino theme
 */
get_header(); ?>
<div class="container">
<style>
    .woocommerce-notices-wrapper {
        margin-top: 25px;
    }
</style>
<?php
echo do_shortcode('[woocommerce_checkout]');
?>
</div>
<?php get_footer();