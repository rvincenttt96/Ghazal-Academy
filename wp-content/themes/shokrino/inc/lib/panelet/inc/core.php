<?php
defined('ABSPATH') || exit;
if (!class_exists('Panelet_CORE')) {
    require_once 'classes/sms.class.php';
    if (PNLT_AVATAR) {
        require_once 'classes/avatar.class.php';
    }
    if (PNLT_NOTIF) {
        require_once 'classes/notifications.class.php';
    }
    if (PNLT_WALLET && function_exists('woo_wallet')) {
        require_once 'classes/wallet.class.php';
    }
    if (PNLT_TICKET) {
        require_once 'classes/ticket.class.php';
    }
    require_once 'classes/user.class.php';
    require_once 'classes/ajax.class.php';
class Panelet_CORE {
    public function __construct() {
        $this->init();
    }
    public function init() {
        // to fix learndash
        $pageURL = "https://";
		if ($_SERVER["SERVER_PORT"] != "80") {
			$pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
		} else {
			$pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
		}
		if (strpos($pageURL, '/dashboard')) {
			echo '<p style="display: none;">1</p>';
		}
        // to fix learndash
        add_filter('plugin_action_links_panelet/panelet.php', [$this,'settings_link'] );
        add_action('woocommerce_edit_account_form', [$this, 'account_edit_form']);
        add_action('wp_enqueue_scripts', [$this, 'load_styles']);
        if (PNLT_THEME) {
            add_filter('woocommerce_account_menu_items', [$this, 'customize_account_menu_items'], 99999);
            add_filter('template_include', [$this, 'custom_template'], 99);
        }
        if (PNLT_MOBILE || PNLT_THEME) {
            add_filter('woocommerce_locate_template', [$this, 'intercept_wc_template'], 99, 3);
        }
        add_action('wp_ajax_nopriv_ajax_login_panelet', [$this, 'ajax_login']);
        add_action('wp_ajax_ajax_login_panelet', [$this, 'ajax_login']);
        add_action('wp_ajax_nopriv_ajax_signup_panelet', [$this, 'ajax_signup']);
        add_action('wp_ajax_ajax_signup_panelet', [$this, 'ajax_signup']);
    }
    public function settings_link($links) {
        $url = esc_url( add_query_arg(
            'page',
            'panelet_options',
            get_admin_url() . 'admin.php'
        ) );
        $settings_link = "<a href='$url'>" . __( 'Settings' , 'panelet' ) . '</a>';
        array_push(
            $links,
            $settings_link
        );
        return $links;
    }
    public function intercept_wc_template($template,$template_name,$template_path) {
        $template_directory = trailingslashit( PNLT_PATH ) . 'woocommerce/';
        $path = $template_directory . $template_name;
        return file_exists( $path ) ? $path : $template;
    }
    public function custom_template( $template ) {
        if ( is_panelet() ) {
            if (is_user_logged_in()) {
                $template = PNLT_TMPL . '/panel.php';
            } else {
                $template = PNLT_TMPL . '/login-register.php';
            }
        }
        return $template;
    }
    public function customize_account_menu_items($menu_items) {
        unset($menu_items['customer-logout']);
        return $menu_items;
    }
    public function load_styles() {
        if(is_panelet() && PNLT_THEME) {
            wp_enqueue_style('font-style-panelet', PNLT_ASSETS.'/css/font-yekan-bakh.css', array(), PNLT_VERSION, 'all', false);
            if(is_user_logged_in()) {
                wp_enqueue_style('panel-style-panelet', PNLT_ASSETS.'/css/panel-style.css', array(), PNLT_VERSION, 'all', false);
                wp_enqueue_script('panel-script-panelet', PNLT_ASSETS.'/js/script-panel.js', array(), PNLT_VERSION, true);
                wp_localize_script('panel-script-panelet', 'login_script_data_panelet', array(
                    'smstime' => shkrn_options('time_sms'),
                    'ajax_url' => admin_url( 'admin-ajax.php' ),
                    'nonce' => wp_create_nonce('pnlt-ajax-nonce')
                ));
            } else {
                wp_enqueue_style('login-style-panelet', PNLT_ASSETS.'/css/login-style.css', array(), PNLT_VERSION, 'all', false);
            }
        }
    }
    public static function load_form_scripts() {
        wp_enqueue_style('general-style-panelet', PNLT_ASSETS.'/css/general.css', array(), PNLT_VERSION, 'all', false);
        wp_enqueue_script('general-script-panelet', PNLT_ASSETS.'/js/general.js', array(), PNLT_VERSION, true);
        wp_localize_script('general-script-panelet', 'login_script_data_panelet', array(
            'smstime' => shkrn_options('time_sms'),
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce('pnlt-ajax-nonce')
        ));
    }
    public function account_edit_form() {
        $user = wp_get_current_user(); ?>
        <form id="detail_change" class="box-inner-content form-edit-detail" action="" method="post" <?php do_action( 'woocommerce_edit_account_form_tag' ); ?>>
            <div class="error-text"></div>
            <div class="success-text"></div>
            <div class="fields-box-pnlt flex">
                <div class="field-first-last flex">
                    <div class="field-detail flex">
                        <input type="text" class="input-text" name="account_first_name" id="account_first_name" autocomplete="given-name" value="<?php echo esc_attr( $user->first_name ); ?>" placeholder="<?php esc_html_e( 'First name', 'woocommerce' ); ?>"/>
                        <span><?php esc_html_e( 'First name', 'woocommerce' ); ?></span>
                    </div>
                    <div class="field-detail flex">
                        <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="account_last_name" id="account_last_name" autocomplete="family-name" value="<?php echo esc_attr( $user->last_name ); ?>" placeholder=" "/>
                        <span><?php esc_html_e( 'Last name', 'woocommerce' ); ?></span>
                    </div>
                </div>
                <div class="field-detail flex">
                    <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="account_display_name" id="account_display_name" value="<?php echo esc_attr( $user->display_name ); ?>" placeholder=" "/>
                    <span><?php esc_html_e( 'Display name', 'woocommerce' ); ?></span>
                </div>
                <div class="field-detail flex">
                    <input type="email" class="woocommerce-Input woocommerce-Input--email input-text" name="account_email" id="account_email" autocomplete="email" value="<?php echo esc_attr( $user->user_email ); ?>" placeholder=" "/>
                    <span><?php esc_html_e( 'Email address', 'woocommerce' ); ?></span>
                </div>
            </div>
            <?php wp_nonce_field( 'save_account_details', 'save-account-details-nonce' ); ?>
            <input type="hidden" name="action" value="save_account_details" />
            <input class="btn-panel" type="submit" name="submit" value="<?php esc_html_e( 'Save changes', 'woocommerce' ); ?>">
        </form>
        <?php if (PNLT_AVATAR) { ?>
            <form id="avatar_change" class="box-inner-content form-edit-avatar" action="#" method="POST" enctype="multipart/form-data" autocomplete="off">
                <div class="error-text"></div>
                <div class="success-text"></div>
                <div class="field-image flex-center">
                    <?php echo Panelet_AVATAR::getUserImg('','','150'); ?>
                    <label id="label-profile-image" class="flex-center" for="file-profile-image">
                        آپلود تصویر دلخواه
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-photo" width="24"
                             height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                             stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <line x1="15" y1="8" x2="15.01" y2="8"></line>
                            <rect x="4" y="4" width="16" height="16" rx="3"></rect>
                            <path d="M4 15l4 -4a3 5 0 0 1 3 0l5 5"></path>
                            <path d="M14 14l1 -1a3 5 0 0 1 3 0l2 2"></path>
                        </svg>
                    </label>
                    <input type="file" id="file-profile-image" name="image"
                           accept="image/x-png,image/gif,image/jpeg,image/jpg" hidden>
                </div>
                <p class="text-inage-select">یا از عکس های آماده انتخاب کنید</p>
                <div class="select-image avatar-select">
                    <div class="custom-select">
                        <div class="options">
                            <div class="option">
                                <img src="<?php echo PNLT_ASSETS; ?>/img/m1.webp" alt="Option Image">
                            </div>
                            <div class="option">
                                <img src="<?php echo PNLT_ASSETS; ?>/img/m2.webp" alt="Option Image">
                            </div>
                            <div class="option">
                                <img src="<?php echo PNLT_ASSETS; ?>/img/m3.webp" alt="Option Image">
                            </div>
                            <div class="option">
                                <img src="<?php echo PNLT_ASSETS; ?>/img/m4.webp" alt="Option Image">
                            </div>
                            <div class="option">
                                <img src="<?php echo PNLT_ASSETS; ?>/img/m5.webp" alt="Option Image">
                            </div>
                            <div class="option">
                                <img src="<?php echo PNLT_ASSETS; ?>/img/m6.webp" alt="Option Image">
                            </div>
                            <div class="option">
                                <img src="<?php echo PNLT_ASSETS; ?>/img/w1.webp" alt="Option Image">
                            </div>
                            <div class="option">
                                <img src="<?php echo PNLT_ASSETS; ?>/img/w2.webp" alt="Option Image">
                            </div>
                            <div class="option">
                                <img src="<?php echo PNLT_ASSETS; ?>/img/w3.webp" alt="Option Image">
                            </div>
                            <div class="option">
                                <img src="<?php echo PNLT_ASSETS; ?>/img/w4.webp" alt="Option Image">
                            </div>
                            <div class="option">
                                <img src="<?php echo PNLT_ASSETS; ?>/img/w5.webp" alt="Option Image">
                            </div>
                            <div class="option">
                                <img src="<?php echo PNLT_ASSETS; ?>/img/w6.webp" alt="Option Image">
                            </div>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="image-default-url" id="image-default-url">
                <input type="hidden" name="image-type" id="image-type">
                <input class="btn-panel" type="submit" name="submit" value="بروزرسانی آواتار">
            </form>
            <?php }
        if (!PNLT_MOBILE) { ?>
            <form id="password_change" class="box-inner-content form-password-change" action="" method="post" <?php do_action( 'woocommerce_edit_account_form_tag' ); ?>>
                <div class="error-text"></div>
                <div class="success-text"></div>
                <div class="fields-box-pnlt flex">
                    <div class="field-detail flex">
                        <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="password_current" id="password_current" placeholder=" " autocomplete="off"/>
                        <span>رمزعبور پیشین شما</span>
                    </div>
                    <div class="field-detail flex">
                        <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="password_1" id="password_1" placeholder=" " autocomplete="off"/>
                        <span>رمزعبور جدید</span>
                    </div>
                    <div class="field-detail flex">
                        <input type="text" class="woocommerce-Input woocommerce-Input--email input-text" name="password_2" id="password_2" placeholder=" " autocomplete="off"/>
                        <span>تکرار رمزعبور جدید</span>
                    </div>
                </div>
                <?php wp_nonce_field( 'save_account_details', 'save-account-details-nonce' ); ?>
                <input type="hidden" name="action" value="save_account_details" />
                <input class="btn-panel" type="submit" name="submit" value="تغییر رمزعبور">
            </form>
        <?php }
    }
    public static function date($format='') {
        date_default_timezone_set('Asia/Tehran');
        $date = new DateTime();
        $date->setTimezone(new DateTimeZone('Asia/Tehran'));
        $date->modify('-1 hour');
        $date->format($format);
        return $date;
    }
}
new Panelet_CORE();

}

