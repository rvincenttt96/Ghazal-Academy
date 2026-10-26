<?php defined( 'ABSPATH' ) || exit; ?>
<aside class="sidebar-single-shkrn">
    <div class="sidebar-box-sticky-shkrn">
        <?php if ( is_active_sidebar( 'shokrino_blog' ) ) {
            dynamic_sidebar('shokrino_blog');
        }
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