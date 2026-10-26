<?php
defined('ABSPATH') || exit;
require_once 'woo.class.php';
class Panelet_USER {
    public function __construct() {
        add_filter('init', [$this, 'same_username_displayname'] );
        add_filter('woocommerce_new_customer_data', [$this, 'set_customer_role']);
        add_filter('woocommerce_customer_available_downloads', [$this, 'noemail_downloads'], 10, 2 );
        add_action('edit_user_profile', [$this, 'admin_edit_field'] , 10);
        add_action('edit_user_profile_update', [$this, 'admin_update_mobile']);
        add_action('show_user_profile', [$this, 'admin_edit_field'] , 10);
        add_action('personal_options_update', [$this, 'admin_update_mobile']);
    }
    public function admin_edit_field($theUser) {
        ?>
        <table class="form-table field-dashboard-pnlt" id="field-mobile-shkrn">
            <tbody>
                </tr>
                    <th>
                        <label for="mobile_pnlt">شماره موبایل کاربر</label>
                    </th>
                    <td>
                        <input type="text" id="mobile_pnlt" name="mobile_pnlt" class="regular-text" value="<?php echo esc_attr(get_user_meta($theUser->ID, 'mobile_pnlt', true)); ?>">
                        <p class="description">شماره موبایلی که کاربر با آن میتواند وارد حساب کاربری خود شود (تایید شده با افزونه پنلت از تیم شکرینو)</p>
                        <?php wp_nonce_field('pnlt_mobile_nonce','pnlt_mobile_nonce'); ?>
                    </td>
                </tr>
            </tbody>
        </table>
        <?php
    }
    public function admin_update_mobile($id) {
        if(isset($_POST['pnlt_mobile_nonce']) && wp_verify_nonce($_POST['pnlt_mobile_nonce'],'pnlt_mobile_nonce') && current_user_can('edit_user')) {
            if(isset($_POST['mobile_pnlt'])) {
                $mobile_pnlt = isset($_POST['mobile_pnlt']) ? $_POST['mobile_pnlt'] : '';
                $mobile = Panelet_SMS::normalizeMobile($mobile_pnlt);
                if ($this->isMobileUnique($mobile)) {
                    update_user_meta($id, 'mobile_pnlt', $mobile);
                } else {
                    add_settings_error(
                        'mobile_pnlt_error',
                        'mobile_pnlt_error_key',
                        'شماره موبایل قبلا در حساب کاربری دیگری ثبت شده است',
                        'error'
                    );
                }
            }
        }
    } 
    public function noemail_downloads( $downloads, $customer_id ) {
        foreach( $downloads as $key => $download ) {
            $downloads[$key]['download_url'] = str_replace( '&email&', '&email=no&', $download['download_url'] );
        }
        return $downloads;
    }
    public function user_nickname($user_ID=0) {
        $firstname = isset(get_user_meta( $user_ID , 'first_name' )[0]) ? get_user_meta( $user_ID , 'first_name' )[0] : '';
        $lastname = isset(get_user_meta( $user_ID ,'last_name' )[0]) ? get_user_meta( $user_ID ,'last_name' )[0] : '';
        $first_last = $firstname.' '.$lastname;
        $nickname = isset(get_user_meta( $user_ID , 'nickname' )[0]) ? get_user_meta( $user_ID , 'nickname' )[0] : '';
        $description = isset(get_user_meta( $user_ID , 'description' )[0]) ? get_user_meta( $user_ID , 'description' )[0] : '';
        $billing_first_name = isset(get_user_meta( $user_ID , 'billing_first_name' )[0]) ? get_user_meta( $user_ID , 'billing_first_name' )[0] : '';
        $billing_last_name = isset(get_user_meta( $user_ID , 'billing_last_name' )[0]) ? get_user_meta( $user_ID , 'billing_last_name' )[0] : '';
        $first_last_billing = $billing_first_name.' '.$billing_last_name;
        if ($user_ID != 0) {
            if (!empty($first_last) && $first_last != ' ') {
                $value = $first_last;
            } elseif (!empty($first_last_billing) && $first_last_billing != ' ') {
                $value = $first_last_billing;
            } else {
                $user_unicode = $user_ID + 1092;
                $value = 'USER'.$user_unicode;
            }
        } else {
            $value = 'کاربر مهمان';
        }
        return $value;
    }
    public function same_username_displayname() {
        $current_user = wp_get_current_user();
        $user_id = $current_user->ID;
        $user_username = $current_user->user_login;
        $user_displayname = $current_user->display_name;
        if ($user_username == $user_displayname) {
            $user_displayname = $this->user_nickname($user_id);
            wp_update_user( array( 'ID' => $user_id, 'display_name' => $user_displayname ) );
        }
    }
    public function isMobileUnique($mobile) {
        $users_with_mobile = get_users(array(
            'meta_key' => 'mobile_pnlt',
            'meta_value' => $mobile,
            'fields' => 'ID',
        ));
        if (empty($users_with_mobile) && !username_exists($users_with_mobile)) {
            $response = true;
        } else {
            $response = false;
        }
        return $response;
    }
    public function set_customer_role($new_customer_data){
        $new_customer_data['role'] = get_option( 'default_role' );
        return $new_customer_data;
    }
    public function generate_username($input_mobile) {
        $type = PNLT_USERNAMETYPE;
        if ($type == 'from_mobile' && !username_exists($input_mobile)) {
            $value = $input_mobile;
        } else {
            $base_username = 'user' . time() . rand(100, 999);
            $unique_username = $base_username;
            $index = 1;
            while (username_exists($unique_username)) {
                $unique_username = $base_username.$index;
                $index++;
            }
            $value = $unique_username;
        }
        return $value;
    }
}
new Panelet_USER;