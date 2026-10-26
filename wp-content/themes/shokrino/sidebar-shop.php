<?php defined( 'ABSPATH' ) || exit;
if (!is_singular('product')) {
$show_sidebar_shop = shkrn_options('show_sidebar_shop');
$equal_height_products = shkrn_options('equal_height_products');
if ($show_sidebar_shop == "yes" or empty($show_sidebar_shop) ) { ?>
    <aside class="sidebar-single-shkrn">
        <div class="sidebar-box-sticky-shkrn">
            <?php if ( is_active_sidebar( 'shokrino_shop' ) ) { ?>
                <?php  dynamic_sidebar('shokrino_shop');  ?>
            <?php }
            if (shkrn_options('sticky_sidebar_blog') == 'off') { ?>
                <style>
                    .sidebar-single-shkrn {
                        position: unset;
                    }
                </style>
            <?php } ?>
        </div>
        <style>
            .single-page-main-part {
                width: 70%;
            }
        </style>
    </aside>
<?php } }