<?php
defined('ABSPATH') || exit;
function is_panelet() {
    return is_page() && is_account_page() && !is_lostpassword_panelet();
}
function is_wc_panelet() {
    if (class_exists('WooCommerce') && is_plugin_active('woocommerce/woocommerce.php')) {
        return true;
    } else {
        return false;
    }
}
function is_lostpassword_panelet() {
    global $wp;
    $current_url = trailingslashit(home_url($wp->request));
    $lost_password_url = trailingslashit(home_url('/my-account/lost-password'));
    return $current_url === $lost_password_url;
}
function bought_products_panelet($user_id) {
    if (is_wc_panelet()) {
        $customer_orders = wc_get_orders(array(
            'customer' => $user_id,
            'status'   => array_keys(wc_get_is_paid_statuses()),
        ));
        $product_ids = array();
        foreach ($customer_orders as $customer_order) {
            $order_items = $customer_order->get_items();
            foreach ($order_items as $order_item) {
                $product_id = $order_item->get_product_id();
                $product_ids[] = $product_id;
            }
        }
        return array_unique($product_ids);
    }
    return array();
}
function get_user_avatar_panelet($user_id='',$id='',$width='auto',$height='auto') {
    if(empty($user_id)) {
        global $current_user;
        $user_id = $current_user->ID;
    }
    $output = '<div class="profile-general">'.get_avatar($user_id, '', '', '', $args = array( 'width' => $width, 'height' => $height )).'</div>';
    return $output;
}
function sms_services_panelet() {
    return Panelet_SMS_Panels::listOfPanels();
}
function display_notification_metabox_pnlt($post) {
    $array_ids = [];
    $products_IDs = new WP_Query(array(
        'post_type' => 'product',
        'post_status' => 'publish',
    ));
    if ($products_IDs->have_posts()) {
        while ($products_IDs->have_posts()) {
            $products_IDs->the_post();
            $array_ids[get_the_id()] = get_the_title();
        }
    }
    wp_reset_postdata();
    $buyers_of_products = array_unique($array_ids);
    $all_users = [];
    $args = array('orderby' => 'id');
    $users = get_users($args);
    foreach ($users as $user) {
        $id = $user->ID;
        $display_name = $user->display_name;
        $all_users[$id] = $display_name;
    }
    $notif_type = get_post_meta($post->ID, 'notif_type', true);
    $selected_buyers_option = get_post_meta($post->ID, 'selected_buyers_option', true);
    ?>
    <label for="notif_type">نوع اعلان:</label>
    <select name="notif_type" id="notif_type" onchange="toggleOptions(this.value)">
        <option value="public" <?php selected($notif_type, 'public'); ?>>عمومی (برای همه)</option>
        <option value="private" <?php selected($notif_type, 'private'); ?>>خصوصی</option>
    </select>
    <p></p>
    <div id="private_options" style="display: <?php echo ($notif_type === 'private') ? 'block' : 'none'; ?>">
        <label for="selected_buyers_option">نوع انتخاب کاربران:</label>
        <select name="selected_buyers_option" id="selected_buyers_option" onchange="toggleOptionsUsers(this.value)">
            <option value="select_users" <?php selected($selected_buyers_option, 'select_users'); ?>>انتخاب کاربران</option>
            <option value="select_buyers_of_product" <?php selected($selected_buyers_option, 'select_buyers_of_product'); ?>>انتخاب خریداران محصول خاص</option>
        </select>
        <p></p>
        <div id="buyers_of_product_options" style="display: <?php echo ($selected_buyers_option === 'select_buyers_of_product') ? 'block' : 'none'; ?>">
            <label for="buyers_of_product">به خریداران کدام محصول ارسال شود؟</label>
            <select name="buyers_of_product" id="buyers_of_product">
                <?php
                foreach ($buyers_of_products as $product_id => $product_title) {
                    echo '<option value="' . esc_attr($product_id) . '">' . esc_html($product_title) . '</option>';
                }
                ?>
            </select>
        </div>
        <div id="select_users_options" style="display: <?php echo ($selected_buyers_option !== 'select_buyers_of_product') ? 'block' : 'none'; ?>">
            <label for="selected_users">به کدام کاربران ارسال شود؟</label>
            <select name="selected_users" id="selected_users" style="width: 100%">
                <?php
                foreach ($all_users as $user_id => $user_name) {
                    echo '<option value="' . esc_attr($user_id) . '">' . esc_html($user_name) . '</option>';
                }
                ?>
            </select>
        </div>
    </div>

    <script>
        function toggleOptions(selectedValue) {
            var privateOptionsDiv = document.getElementById('private_options');
            if (selectedValue === 'private') {
                privateOptionsDiv.style.display = 'block';
            } else {
                privateOptionsDiv.style.display = 'none';
            }
        }
        function toggleOptionsUsers(selectedValue) {
            var buyersOfProductOptionsDiv = document.getElementById('buyers_of_product_options');
            var selectUsersOptionsDiv = document.getElementById('select_users_options');
            if (selectedValue === 'select_buyers_of_product') {
                buyersOfProductOptionsDiv.style.display = 'block';
                selectUsersOptionsDiv.style.display = 'none';
            } else if (selectedValue === 'select_users') {
                buyersOfProductOptionsDiv.style.display = 'none';
                selectUsersOptionsDiv.style.display = 'block';
            } else {
                buyersOfProductOptionsDiv.style.display = 'none';
                selectUsersOptionsDiv.style.display = 'none';
            }
        }
    </script>

    <?php
}
function create_my_account_endpoint_pnlt($menu_title, $endpoint, $content_callback) {
    add_rewrite_endpoint($endpoint, EP_ROOT | EP_PAGES);
    add_filter('woocommerce_account_menu_items', function ($items) use ($menu_title, $endpoint) {
        $new_item = array($endpoint => $menu_title);
        $items = array_slice($items, 0, 1, true) + $new_item + array_slice($items, 1, NULL, true);
        return $items;
    });
    add_action("woocommerce_account_{$endpoint}_endpoint", function () use ($content_callback) {
        echo do_shortcode($content_callback);
    });
    flush_rewrite_rules();
}
function create_my_account_endpoint_pnlt_func($menu_title, $endpoint, $content_callback, $length = 1) {
    add_rewrite_endpoint($endpoint, EP_ROOT | EP_PAGES);
    add_filter('woocommerce_account_menu_items', function ($items) use ($menu_title, $endpoint, $length) {
        $new_item = array($endpoint => $menu_title);
        $items = array_slice($items, 0, $length, true) + $new_item + array_slice($items, 1, NULL, true);
        return $items;
    });
    add_action("woocommerce_account_{$endpoint}_endpoint", function () use ($content_callback) {
        call_user_func($content_callback);
    });
    flush_rewrite_rules();
}
function create_my_account_only_endpoint_pnlt_func($endpoint, $content_callback) {
    add_rewrite_endpoint($endpoint, EP_ROOT | EP_PAGES);
    add_action("woocommerce_account_{$endpoint}_endpoint", function () use ($content_callback) {
        call_user_func($content_callback);
    });
    flush_rewrite_rules();
}
