<?php

if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

delete_option('woocommerce_fiura_settings');
delete_transient('fiurapay_wc_activated');
