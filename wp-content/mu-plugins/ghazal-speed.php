<?php
/**
 * Plugin Name: Ghazal Speed
 * Description: Dequeue unused front-end assets without breaking Digits login.
 * Version: 1.0.4
 */
defined('ABSPATH') || exit;

function ghazal_speed_is_commerce() {
    if (function_exists('is_cart') && (is_cart() || is_checkout() || is_account_page())) {
        return true;
    }
    if (function_exists('is_woocommerce') && is_woocommerce()) {
        return true;
    }
    return false;
}

function ghazal_speed_is_digits_auth() {
    if (isset($_GET['login']) || isset($_GET['signup']) || isset($_GET['register'])) {
        return true;
    }
    $uri = (string) ($_SERVER['REQUEST_URI'] ?? '');
    if (preg_match('/[?&](login|signup|register)=/i', $uri)) {
        return true;
    }
    if (preg_match('#/(my-account|account)(/|$)#i', $uri)) {
        return true;
    }
    $action = (string) ($_REQUEST['action'] ?? '');
    if ($action !== '' && strpos($action, 'digits') === 0) {
        return true;
    }
    return false;
}

function ghazal_speed_dequeue_misc() {
    if (is_admin()) {
        return;
    }
    global $wp_scripts;
    if (isset($wp_scripts->registered) && is_array($wp_scripts->registered)) {
        foreach ($wp_scripts->registered as $handle => $obj) {
            $src = isset($obj->src) ? (string) $obj->src : '';
            if ($src && strpos($src, 'themes/shokrino/assets/js/jquery.min.js') !== false) {
                wp_dequeue_script($handle);
                wp_deregister_script($handle);
            }
        }
    }
    if (!ghazal_speed_is_commerce() && !ghazal_speed_is_digits_auth()) {
        foreach ([
            'woocommerce-layout', 'woocommerce-smallscreen', 'woocommerce-general',
            'woocommerce-inline', 'wc-blocks-style', 'wc-blocks-vendors-style',
        ] as $h) {
            wp_dequeue_style($h);
        }
        foreach ([
            'woocommerce', 'wc-add-to-cart', 'wc-cart-fragments', 'sourcebuster-js',
            'wc-order-attribution',
        ] as $h) {
            wp_dequeue_script($h);
        }
    }
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
    wp_dequeue_script('wp-embed');
}

add_action('wp_enqueue_scripts', 'ghazal_speed_dequeue_misc', 99999);

add_action('template_redirect', static function () {
    if (is_admin() || wp_doing_ajax() || ghazal_speed_is_digits_auth()) {
        return;
    }
    ob_start(static function ($html) {
        if (!is_string($html) || $html === '') {
            return $html;
        }
        $html = preg_replace('#<script type="text/template" id="digits_country_list_template">.*?</script>#si', '', $html);
        $html = preg_replace('#<div id="digits_country_list_wrapper"></div>#si', '', $html);
        return $html;
    });
}, 2);
