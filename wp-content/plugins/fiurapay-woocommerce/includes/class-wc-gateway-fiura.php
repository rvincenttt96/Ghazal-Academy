<?php

if (!defined('ABSPATH')) {
    exit;
}

final class WC_Gateway_Fiura extends WC_Payment_Gateway
{
    public const META_INVOICE = '_fiura_invoice_id';
    public const META_CHECKOUT = '_fiura_checkout_url';

    public function __construct()
    {
        $this->id                 = 'fiura';
        $this->method_title       = 'Fiura Pay';
        $this->method_description = 'درگاه غیرحضانتی USDT / BNB / ETH. کلید API، کیف تسویه و راز webhook را فقط در fiurapay.com بسازید — این افزونه فاکتور می‌سازد و رویداد invoice.settled را گوش می‌دهد.';
        $this->has_fields         = false;
        $this->supports           = ['products'];

        $this->init_form_fields();
        $this->init_settings();

        $this->title       = $this->get_option('title', 'پرداخت رمزارزی Fiura');
        $this->description = $this->get_option('description', 'پرداخت با USDT یا BNB روی BNB Smart Chain. به صفحهٔ پرداخت Fiura هدایت می‌شوید.');
        $this->enabled     = $this->get_option('enabled', 'no');

        add_action(
            'woocommerce_update_options_payment_gateways_' . $this->id,
            [$this, 'process_admin_options']
        );

        if (!self::$hooks_bound) {
            self::$hooks_bound = true;
            add_action('woocommerce_api_fiura_pay', [__CLASS__, 'dispatch_webhook']);
            add_action('woocommerce_thankyou_fiura', [__CLASS__, 'dispatch_thankyou']);
            add_action('add_meta_boxes', [__CLASS__, 'dispatch_metabox']);
        }
    }

    private static bool $hooks_bound = false;

    public static function dispatch_webhook(): void
    {
        $gateway = self::instance();
        if ($gateway) {
            $gateway->handle_webhook();
        }
        status_header(503);
        echo 'gateway unavailable';
        exit;
    }

    public static function dispatch_thankyou($order_id): void
    {
        $gateway = self::instance();
        if ($gateway) {
            $gateway->thankyou($order_id);
        }
    }

    public static function dispatch_metabox(): void
    {
        $gateway = self::instance();
        if ($gateway) {
            $gateway->order_metabox();
        }
    }

    private static function instance(): ?self
    {
        if (!function_exists('WC') || !WC()) {
            return null;
        }
        $gateways = WC()->payment_gateways();
        if (!$gateways) {
            return null;
        }
        $list = $gateways->payment_gateways();
        $g = $list['fiura'] ?? null;
        return $g instanceof self ? $g : null;
    }

    public function init_form_fields(): void
    {
        $webhook = home_url('/?wc-api=fiura_pay');

        $this->form_fields = [
            'enabled' => [
                'title'   => 'فعال‌سازی',
                'type'    => 'checkbox',
                'label'   => 'فعال کردن Fiura Pay در تسویه‌حساب',
                'default' => 'no',
            ],
            'title' => [
                'title'   => 'عنوان درگاه',
                'type'    => 'text',
                'default' => 'پرداخت رمزارزی Fiura',
            ],
            'description' => [
                'title'   => 'توضیح برای مشتری',
                'type'    => 'textarea',
                'default' => 'پرداخت با USDT یا BNB روی زنجیره. به صفحهٔ پرداخت Fiura هدایت می‌شوید و پس از تأیید برمی‌گردید.',
            ],
            'dashboard_note' => [
                'title'       => 'کلید، کیف پول و webhook (فقط در سایت Fiura)',
                'type'        => 'title',
                'description' => '<p>کیف تسویه را داخل وردپرس bind نکنید و کلید را در قالب قرار ندهید. از داشبورد Fiura استفاده کنید:</p>'
                    . '<ol>'
                    . '<li>ورود به <a href="https://fiurapay.com/login" target="_blank" rel="noreferrer">fiurapay.com</a></li>'
                    . '<li>اتصال کیف تسویه (امضا + 2FA)</li>'
                    . '<li>ساخت کلید API (<code>ath_test_</code> یا <code>ath_live_</code>)</li>'
                    . '<li>این آدرس webhook فروشگاه را در تنظیمات Fiura بچسبانید:</li>'
                    . '</ol>'
                    . '<p><code>' . esc_html($webhook) . '</code></p>'
                    . '<p>سپس کلید و <em>راز webhook</em> را در فیلدهای زیر وارد کنید. تغییر آدرس تسویه فقط در Fiura ممکن است.</p>',
            ],
            'api_key' => [
                'title'       => 'کلید API',
                'type'        => 'password',
                'description' => 'از داشبورد Fiura. هرگز در کد قالب قرار ندهید.',
                'default'     => '',
            ],
            'webhook_secret' => [
                'title'       => 'راز webhook',
                'type'        => 'password',
                'description' => 'هنگام ذخیرهٔ آدرس webhook در Fiura یک‌بار نمایش داده می‌شود. برای تأیید HMAC لازم است.',
                'default'     => '',
            ],
            'base_url' => [
                'title'       => 'آدرس پایهٔ API',
                'type'        => 'text',
                'default'     => 'https://fiurapay.com',
                'description' => 'مگر اینکه پشتیبانی Fiura چیز دیگری بگوید، همین را نگه دارید.',
            ],
            'network_mode' => [
                'title'       => 'شبکه',
                'type'        => 'select',
                'default'     => 'testnet',
                'options'     => [
                    'testnet' => 'Testnet (کلید ath_test_ — فقط Chapel tBNB / Sepolia ETH)',
                    'mainnet' => 'Mainnet (کلید ath_live_)',
                ],
                'description' => 'باید با نوع کلید یکی باشد. در Testnet نمی‌توان USDT فرستاد؛ افزونه خودکار BNB یا ETH می‌فرستد.',
            ],
            'chain' => [
                'title'   => 'زنجیره',
                'type'    => 'select',
                'default' => 'bsc',
                'options' => [
                    'bsc'      => 'BNB Smart Chain',
                    'ethereum' => 'Ethereum',
                ],
            ],
            'asset' => [
                'title'       => 'دارایی (مین‌نت)',
                'type'        => 'select',
                'default'     => 'USDT',
                'options'     => [
                    'USDT' => 'USDT',
                    'BNB'  => 'BNB / tBNB',
                    'ETH'  => 'ETH',
                ],
                'description' => 'مین‌نت: USDT یا کوین بومی. تست‌نت این گزینه را نادیده می‌گیرد و tBNB (BSC) یا ETH (اتریوم) می‌فرستد.',
            ],
            'pricing' => [
                'title'       => 'مبلغ سفارش ارسالی به Fiura',
                'type'        => 'select',
                'default'     => 'auto',
                'options'     => [
                    'auto'  => 'خودکار (IRT/IRR → تومان، در غیر این صورت واحد USDT)',
                    'toman' => 'همیشه orderAmountToman',
                    'usdt'  => 'همیشه orderAmountUsdt (جمع فروشگاه به عنوان USDT)',
                ],
                'description' => 'ارز فروشگاه باید منطبق باشد. حالت تومان از نرخ زندهٔ Fiura استفاده می‌کند.',
            ],
            'irr_as_rials' => [
                'title'   => 'IRR به ریال است',
                'type'    => 'checkbox',
                'label'   => 'جمع IRR را قبل از ارسال تومان بر ۱۰ تقسیم کن (رایج در فروشگاه‌های ووکامرس ایرانی)',
                'default' => 'yes',
            ],
        ];
    }

    public function process_admin_options()
    {
        $prev_key    = (string) $this->get_option('api_key');
        $prev_secret = (string) $this->get_option('webhook_secret');
        parent::process_admin_options();
        if (trim((string) $this->get_option('api_key')) === '') {
            $this->update_option('api_key', $prev_key);
        }
        if (trim((string) $this->get_option('webhook_secret')) === '') {
            $this->update_option('webhook_secret', $prev_secret);
        }
    }

    public function process_payment($order_id): array
    {
        $order = wc_get_order($order_id);
        if (!$order) {
            wc_add_notice('سفارش پیدا نشد.', 'error');
            return ['result' => 'failure'];
        }

        $api_key = trim((string) $this->get_option('api_key'));
        if ($api_key === '') {
            wc_add_notice('Fiura پیکربندی نشده است. فروشنده باید کلید API را از fiurapay.com وارد کند.', 'error');
            return ['result' => 'failure'];
        }

        try {
            $api = new Fiura_Api($api_key, (string) $this->get_option('base_url', 'https://fiurapay.com'));
            $existing_id  = (string) $order->get_meta(self::META_INVOICE);
            $existing_url = (string) $order->get_meta(self::META_CHECKOUT);
            if ($existing_id !== '' && $existing_url !== '' && !$order->is_paid()) {
                try {
                    $open = $api->get_invoice($existing_id);
                    $st = isset($open['status']) ? (string) $open['status'] : '';
                    $reuse = isset($open['checkoutUrl']) ? (string) $open['checkoutUrl'] : $existing_url;
                    if (in_array($st, ['pending', 'confirming', 'paid'], true) && self::safe_checkout_url($reuse)) {
                        return ['result' => 'success', 'redirect' => $reuse];
                    }
                } catch (Throwable $ignore) {
                    // Create a fresh invoice.
                }
            }
            $invoice = $api->create_invoice($this->invoice_body($order));
        } catch (Throwable $e) {
            wc_add_notice(esc_html($e->getMessage()), 'error');
            return ['result' => 'failure'];
        }

        $id  = isset($invoice['id']) ? (string) $invoice['id'] : '';
        $url = isset($invoice['checkoutUrl']) ? (string) $invoice['checkoutUrl'] : '';
        if ($id === '' || !self::safe_checkout_url($url)) {
            wc_add_notice('Fiura آدرس checkout برنگرداند.', 'error');
            return ['result' => 'failure'];
        }

        $order->update_meta_data(self::META_INVOICE, $id);
        $order->update_meta_data(self::META_CHECKOUT, $url);
        $order->add_order_note('Fiura invoice ' . $id);
        $order->save();

        return [
            'result'   => 'success',
            'redirect' => $url,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function invoice_body(WC_Order $order): array
    {
        $network = (string) $this->get_option('network_mode', 'testnet');
        $chain   = (string) $this->get_option('chain', 'bsc');
        $asset   = (string) $this->get_option('asset', 'USDT');
        if ($chain !== 'ethereum') {
            $chain = 'bsc';
        }
        if ($network === 'testnet') {
            $asset = $chain === 'ethereum' ? 'ETH' : 'BNB';
        } elseif ($chain === 'ethereum' && $asset === 'BNB') {
            $asset = 'ETH';
        } elseif ($chain === 'bsc' && $asset === 'ETH') {
            $asset = 'BNB';
        }
        $pricing = (string) $this->get_option('pricing', 'auto');
        $currency = strtoupper($order->get_currency());
        $total    = (float) $order->get_total();

        $use_toman = $pricing === 'toman'
            || ($pricing === 'auto' && in_array($currency, ['IRT', 'IRR', 'TMN'], true));

        $body = [
            'chain'        => $chain,
            'networkMode'  => $network,
            'asset'        => $asset,
            'externalId'   => 'wc-' . $order->get_id(),
            'successUrl'   => $this->get_return_url($order),
            'cancelUrl'    => $order->get_cancel_order_url_raw(),
            'customerName' => $order->get_formatted_billing_full_name(),
            'metadata'     => [
                'woo_order_id' => $order->get_id(),
                'currency'     => $currency,
            ],
        ];

        if ($use_toman) {
            $toman = $total;
            if ($currency === 'IRR' && $this->get_option('irr_as_rials', 'yes') === 'yes') {
                $toman = $total / 10;
            }
            $body['orderAmountToman'] = (string) max(1, (int) round($toman));
        } else {
            $body['orderAmountUsdt'] = wc_format_decimal($total, 8);
        }

        return $body;
    }

    public function handle_webhook(): void
    {
        $raw = (string) file_get_contents('php://input');
        $secret = trim((string) $this->get_option('webhook_secret'));
        $ts  = Fiura_Hmac::header('X-Athura-Timestamp');
        $sig = Fiura_Hmac::header('X-Athura-Signature');

        if ($secret === '' || !Fiura_Hmac::verify($secret, $raw, $ts, $sig)) {
            status_header(400);
            echo 'invalid signature';
            exit;
        }

        $payload = json_decode($raw, true);
        if (!is_array($payload)) {
            status_header(400);
            echo 'invalid json';
            exit;
        }

        $data = is_array($payload['data'] ?? null) ? $payload['data'] : [];
        $event = isset($payload['event']) ? (string) $payload['event'] : '';
        if ($event !== 'invoice.settled') {
            echo wp_json_encode(['ok' => true, 'ignored' => $event]);
            exit;
        }

        $invoice = is_array($data['invoice'] ?? null) ? $data['invoice'] : [];
        $invoice_id = isset($invoice['id']) ? (string) $invoice['id'] : '';
        $external = isset($invoice['externalId']) ? (string) $invoice['externalId'] : '';
        if ($external === '' && isset($data['report']['externalId'])) {
            $external = (string) $data['report']['externalId'];
        }

        $order = $this->find_order($invoice_id, $external);
        if (!$order) {
            status_header(404);
            echo 'order not found';
            exit;
        }

        $delivery = Fiura_Hmac::header('X-Athura-Delivery');
        $seen = (string) $order->get_meta('_fiura_delivery');
        if ($delivery !== '' && $seen === $delivery) {
            echo wp_json_encode(['ok' => true, 'duplicate' => true]);
            exit;
        }

        if (!$order->is_paid()) {
            $tx = '';
            if (isset($data['transaction']['settleTxHash'])) {
                $tx = (string) $data['transaction']['settleTxHash'];
            }
            if ($tx === '' && isset($invoice['settleTxHash'])) {
                $tx = (string) $invoice['settleTxHash'];
            }
            $order->payment_complete($tx !== '' ? $tx : $invoice_id);
            $order->add_order_note('Fiura invoice.settled ' . $invoice_id . ($tx !== '' ? ' settle ' . $tx : ''));
        }
        if ($delivery !== '') {
            $order->update_meta_data('_fiura_delivery', $delivery);
        }
        $order->save();

        echo wp_json_encode(['ok' => true]);
        exit;
    }

    private function find_order(string $invoice_id, string $external): ?WC_Order
    {
        if ($external !== '' && preg_match('/^wc-(\d+)$/', $external, $m)) {
            $order = wc_get_order((int) $m[1]);
            if ($order instanceof WC_Order && $order->get_payment_method() === $this->id) {
                return $order;
            }
        }
        if ($invoice_id === '') {
            return null;
        }
        $orders = wc_get_orders([
            'limit'      => 1,
            'meta_key'   => self::META_INVOICE,
            'meta_value' => $invoice_id,
        ]);
        $first = $orders[0] ?? null;
        return $first instanceof WC_Order ? $first : null;
    }

    public function thankyou($order_id): void
    {
        $order = wc_get_order($order_id);
        if (!$order || $order->get_payment_method() !== $this->id) {
            return;
        }
        if ($order->is_paid()) {
            return;
        }
        $invoice_id = (string) $order->get_meta(self::META_INVOICE);
        $api_key = trim((string) $this->get_option('api_key'));
        if ($invoice_id === '' || $api_key === '') {
            echo '<p>' . esc_html__('منتظر تسویهٔ زنجیره‌ای هستیم. این صفحه وقتی Fiura پرداخت را تأیید کند به‌روز می‌شود.', 'fiurapay') . '</p>';
            return;
        }
        try {
            $api = new Fiura_Api($api_key, (string) $this->get_option('base_url', 'https://fiurapay.com'));
            $inv = $api->get_invoice($invoice_id);
            if (($inv['status'] ?? '') === 'settled' && !$order->is_paid()) {
                $order->payment_complete(isset($inv['settleTxHash']) ? (string) $inv['settleTxHash'] : $invoice_id);
                $order->save();
            }
        } catch (Throwable $e) {
            // Keep pending; webhook is source of truth.
        }
        if (!$order->is_paid()) {
            echo '<p>' . esc_html__('پرداخت روی زنجیره در حال تأیید است. سفارش را دوباره ارسال نکنید — منتظر تسویه بمانید.', 'fiurapay') . '</p>';
        }
    }

    public function order_metabox(): void
    {
        $screen = class_exists('\Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController')
            ? wc_get_page_screen_id('shop-order')
            : 'shop_order';
        add_meta_box(
            'fiura_pay',
            'Fiura Pay',
            [$this, 'render_metabox'],
            $screen,
            'side',
            'default'
        );
    }

    public function render_metabox($post_or_order): void
    {
        $order = $post_or_order instanceof WC_Order ? $post_or_order : wc_get_order($post_or_order->ID ?? 0);
        if (!$order) {
            return;
        }
        $id  = (string) $order->get_meta(self::META_INVOICE);
        $url = (string) $order->get_meta(self::META_CHECKOUT);
        echo '<p><strong>Invoice</strong><br>' . esc_html($id !== '' ? $id : '—') . '</p>';
        if ($url !== '') {
            echo '<p><a href="' . esc_url($url) . '" target="_blank" rel="noreferrer">Checkout</a></p>';
        }
        echo '<p class="description">اتصال کیف و کلید API فقط در داشبورد fiurapay.com.</p>';
    }

    public static function safe_checkout_url(string $url): bool
    {
        $url = trim($url);
        if ($url === '' || !filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }
        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));
        return $scheme === 'https' || $scheme === 'http';
    }
}
