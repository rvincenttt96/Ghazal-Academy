<?php

if (!defined('ABSPATH')) {
    exit;
}

use Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType;

final class WC_Fiura_Blocks extends AbstractPaymentMethodType
{
    protected $name = 'fiura';

    public function initialize(): void
    {
        $this->settings = get_option('woocommerce_fiura_settings', []);
    }

    public function is_active(): bool
    {
        return ($this->settings['enabled'] ?? 'no') === 'yes';
    }

    public function get_payment_method_script_handles(): array
    {
        wp_register_script(
            'fiurapay-blocks',
            FIURAPAY_WC_URL . 'assets/js/blocks.js',
            ['wc-blocks-registry', 'wc-settings', 'wp-element'],
            FIURAPAY_WC_VERSION,
            true
        );
        return ['fiurapay-blocks'];
    }

    /**
     * @return array<string, mixed>
     */
    public function get_payment_method_data(): array
    {
        return [
            'title'       => $this->settings['title'] ?? 'Fiura Pay',
            'description' => $this->settings['description'] ?? '',
            'supports'    => ['products'],
        ];
    }
}
