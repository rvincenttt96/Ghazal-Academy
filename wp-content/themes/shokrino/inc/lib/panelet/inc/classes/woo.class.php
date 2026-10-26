<?php
defined('ABSPATH') || exit;
class Panelet_WOO {
    public function __construct() {
        if (PNLT_THEME) {
            add_filter('woocommerce_login_form', [$this, 'woo_login_form']);
        }
        if (PNLT_MOBILE) {
            add_action('panelet_login_form_fields', [$this, 'woo_login_form']);
            add_filter('woocommerce_checkout_fields', [$this, 'checkout_fields_override']);
            add_action('woocommerce_before_checkout_form', [$this, 'checkout_login'], 99);
            add_action('woocommerce_after_checkout_validation', [$this, 'check_user_logged_in_before_checkout'], 10, 2);
        }
        if (shkrn_feature_options('menu_custom') == "on") {
            add_action('init', 'flush_rewrite_rules');
            self::custom_menus();
        }
        add_filter('woocommerce_account_menu_items', [$this, 'remove_menus']);
        add_action('woocommerce_update_product', [$this, 'download_permissions_update'], 20, 1);
        if (!shkrn_options('remove_bought_products_woo')) {
            create_my_account_endpoint_pnlt_func('محصولات خریداری شده', 'Purchased-courses', [$this, 'purchased_products']);
        }
    }
    public function purchased_products() {
    ?> <div class="purchased-courses"> <?php
        $current_user = wp_get_current_user();
        if ( 0 == $current_user->ID ) return;
        $customer_orders = get_posts( array(
            'numberposts' => -1,
            'meta_key'    => '_customer_user',
            'meta_value'  => $current_user->ID,
            'post_type'   => wc_get_order_types(),
            'post_status' => array_keys( wc_get_is_paid_statuses() ),
        ) );
        if ( ! $customer_orders ) return;
        $product_ids = array();
        foreach ( $customer_orders as $customer_order ) {
            $order = wc_get_order( $customer_order->ID );
            $items = $order->get_items();
            foreach ( $items as $item ) {
                $product_id = $item->get_product_id();
                $product_ids[] = $product_id;
            }
        }
        $product_ids = array_unique( $product_ids );
        if (!empty($product_ids) && is_array($product_ids)) {
            ?>
            <div class="archive-grid-posts-shkrn products shkrn-grid-columns-<?php echo shkrn_options('courses_columns'); ?>">
            <?php
            foreach ($product_ids as $product_id) {
                $product = wc_get_product($product_id);
                if (empty($product) || !$product->is_visible()) {
                    return;
                }
                ?>
                <div class="archive-product-shkrn box-course">
                    <?php
                    $link_html = '<a class="course-openner-link-box-shkrn" href="' . esc_url(get_permalink($product_id)) . '">';
                    if (has_post_thumbnail($product_id)) {
                        $link_html .= get_the_post_thumbnail($product_id, optimize_img_shkrn('posts'));
                    } else {
                        $link_html .= '<img src="' . esc_url(SHKRN_ASSETS . '/img/0.jpg') . '">';
                    }
                    $link_html .= '<h2>' . esc_html(get_the_title($product_id)) . '</h2>';
                    $link_html .= '</a>';
                    echo $link_html;
                    ?>
                </div>
                <?php
            } ?>
            </div>
            <?php
        } else {
            ?>
            <div class="panelet-purchased-products woocommerce-message woocommerce-message--info woocommerce-Message woocommerce-Message--info woocommerce-info">
                <a class="woocommerce-Button button" href="<?php echo wc_get_page_permalink( 'shop' ) ?>">مرور محصولات</a>
                <?php esc_html_e('هنوز در هیچ دوره ای شرکت نکرده اید', 'shokrino' ); ?>
            </div>
            <?php
        }
        ?> </div> <?php
    }
    public function woo_login_form() {
        if (PNLT_MOBILE) {
            require_once PNLT_INC . '/templates/custom-login-form.php';
        } else {
            require_once PNLT_INC . '/templates/default-login-form.php';
        }
    }
    public function checkout_login() {
        if ( is_user_logged_in() || 'no' === get_option( 'woocommerce_enable_checkout_login_reminder' ) ) {
            return;
        }
        woocommerce_login_form(
            array(
                'message'  => esc_html__( 'If you have shopped with us before, please enter your details below. If you are a new customer, please proceed to the Billing section.', 'woocommerce' ),
                'redirect' => wc_get_checkout_url()
            )
        );
    }
    public function checkout_fields_override($fields) {
        $user = wp_get_current_user();
        $billing_phone = get_user_meta($user->ID, 'mobile_pnlt', true);
        $fields['billing']['billing_phone']['value'] = $billing_phone;
        return $fields;
    }
    public function check_user_logged_in_before_checkout( $posted ) {
        if ( ! is_user_logged_in() ) {
            wc_add_notice( 'قبل از ثبت سفارش باید وارد حساب کاربری خود شود. <a href="#pnlt-login">ورود به حساب کاربری</a>', 'error' );
            return false;
        }
        return $posted;
    }
    public function custom_menus() {
        if (shkrn_feature_options('menu_custom') == "on") {
            $menus = shkrn_feature_options('menu_repeater');
            foreach ($menus as $menu) {
                create_my_account_endpoint_pnlt($menu['menu_title'], $menu['menu_endpoint'], $menu['menu_callback']);
            }
        }
    }
    public function remove_menus($items) {
        if (pnlt_option('remove_orders_woo')) {
            unset($items['orders']);
        }
        if (pnlt_option('remove_downloads_woo')) {
            unset($items['downloads']);
        }
        if (pnlt_option('remove_addresses_woo')) {
            unset($items['edit-address']);
        }
        return $items;
    }
    public function download_permissions_update() {
        $orders_with_product = wc_get_orders(array(
            'status'   => array('completed', 'processing')
        ));
        foreach ($orders_with_product as $order) {
            $data_store = WC_Data_Store::load('customer-download');
            $orders_ids[] = $order->get_id();
        }
        foreach ($orders_ids as $orders_id) {
            wc_downloadable_product_permissions($orders_id);
        }
    }
}
new Panelet_WOO();

