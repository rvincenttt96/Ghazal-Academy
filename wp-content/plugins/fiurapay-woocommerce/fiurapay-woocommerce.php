<?php
/**
 * Plugin Name: Fiura Pay for WooCommerce
 * Plugin URI: https://fiurapay.com/woocommerce
 * Description: درگاه رمزارزی Fiura برای ووکامرس — ساخت فاکتور، هدایت به checkout میزبانی‌شده، و تأیید سفارش با webhook امضاشده.
 * Version: 2.0.1
 * Requires at least: 6.4
 * Requires PHP: 8.1
 * Requires Plugins: woocommerce
 * WC requires at least: 8.0
 * WC tested up to: 9.8
 * Author: Fiura Pay
 * License: MIT
 * Text Domain: fiurapay
 */

if (!defined('ABSPATH')) {
    exit;
}

define('FIURAPAY_WC_VERSION', '2.0.1');
define('FIURAPAY_WC_PATH', plugin_dir_path(__FILE__));
define('FIURAPAY_WC_URL', plugin_dir_url(__FILE__));
define('FIURAPAY_WC_BASENAME', plugin_basename(__FILE__));

add_action('plugins_loaded', 'fiurapay_wc_init', 11);

function fiurapay_wc_init(): void
{
    if (!class_exists('WooCommerce') || !class_exists('WC_Payment_Gateway')) {
        add_action('admin_notices', static function (): void {
            echo '<div class="notice notice-error"><p>'
                . esc_html__('افزونه Fiura Pay به ووکامرس نیاز دارد. ابتدا ووکامرس را نصب و فعال کنید.', 'fiurapay')
                . '</p></div>';
        });
        return;
    }

    require_once FIURAPAY_WC_PATH . 'includes/class-fiura-hmac.php';
    require_once FIURAPAY_WC_PATH . 'includes/class-fiura-api.php';
    require_once FIURAPAY_WC_PATH . 'includes/class-wc-gateway-fiura.php';

    add_filter('woocommerce_payment_gateways', static function (array $gateways): array {
        $gateways[] = 'WC_Gateway_Fiura';
        return $gateways;
    });
}

add_action('woocommerce_blocks_loaded', static function (): void {
    if (!class_exists('\Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType')) {
        return;
    }
    require_once FIURAPAY_WC_PATH . 'includes/class-wc-fiura-blocks.php';
    add_action(
        'woocommerce_blocks_payment_method_type_registration',
        static function ($registry): void {
            $registry->register(new WC_Fiura_Blocks());
        }
    );
});

add_action('before_woocommerce_init', static function (): void {
    if (class_exists('\Automattic\WooCommerce\Utilities\FeaturesUtil')) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility(
            'custom_order_tables',
            __FILE__,
            true
        );
    }
});

register_activation_hook(__FILE__, static function (): void {
    set_transient('fiurapay_wc_activated', '1', 60);
});

add_action('admin_notices', static function (): void {
    if (!get_transient('fiurapay_wc_activated')) {
        return;
    }
    delete_transient('fiurapay_wc_activated');
    $url = admin_url('admin.php?page=wc-settings&tab=checkout&section=fiura');
    echo '<div class="notice notice-success is-dismissible"><p>'
        . esc_html__('Fiura Pay نصب شد.', 'fiurapay')
        . ' <a href="' . esc_url($url) . '">'
        . esc_html__('رفتن به تنظیمات درگاه', 'fiurapay')
        . '</a></p></div>';
});

add_filter('plugin_action_links_' . FIURAPAY_WC_BASENAME, static function (array $links): array {
    $url = admin_url('admin.php?page=wc-settings&tab=checkout&section=fiura');
    array_unshift(
        $links,
        '<a href="' . esc_url($url) . '">' . esc_html__('تنظیمات', 'fiurapay') . '</a>'
    );
    return $links;
});
