<?php defined( 'SDOPATH' ) || exit;
/**
 * Config SDO framework
 */
define('PNLT_NAME', SHKRN_THEME_NAME);
define('PNLT_VERSION', SHKRN_THEME_VERSION);
define('PNLT_TEXTDOMAIN', SHKRN_THEME_TEXTDOMAIN);
define('PNLT_PATH' , SHKRN_LIB.'/panelet');
define('PNLT_URL' , SHKRN_DIR_URL.'/inc/lib/panelet');
define('PNLT_INC' , PNLT_PATH.'/inc');
define('PNLT_TMPL' , PNLT_PATH.'/inc/templates');
define('PNLT_ASSETS' , PNLT_URL.'/assets');
define('PNLT_ACCOUNT' , 'pro');
define('PNLT_USERNAMETYPE' , "from_mobile");
define('PNLT_THEME' , true);
define('PNLT_AVATAR' , shkrn_options('avatar_enable'));
define('PNLT_NOTIF' , true);
define('PNLT_WALLET' , shkrn_options('show_wallet_userpanel'));
define('PNLT_TICKET' , shkrn_options('ticket_enable'));
define('PNLT_ROLE' , true);
define('PNLT_FIELDS' , true);
define('PNLT_MENUS' , true);
define('PNLT_MOBILE' , shkrn_options('mobile_login') ? true : false);
define('PNLT_SMSTIME' , shkrn_options('time_sms'));
if (defined('PNLT_VERSION')) {
    function pnlt_option($field) {
        return shkrn_options($field);
    }
    function shkrn_feature_options($field) {
        return sdo_option('shkrn_feature_options',$field);
    }
}

SDO::set_config('shkrn_feature_options', array(
    'dev_title' => 'تنظیمات بیشتر',
    'dev_version' => PNLT_VERSION,
    'logo_url' => SHKRN_ASSETS . '/img/logo.png',
    'dev_textdomain' > PNLT_TEXTDOMAIN,
    'menu_type' => 'submenu',
    'menu_title' => 'تنظیمات بیشتر',
    'page_title' => 'تنظیمات بیشتر',
    'page_parent' => 'features_shokrino',
    'page_capability' => 'manage_options',
    'page_slug' => 'settings_features_shokrino',
    'icon_url' => '',
    'menu_priority' => 100,
    'admin_bar' => false,
    'admin_bar_priority' => null,
    'admin_bar_icon' => 'dashicons-menu',
));

SDO::set_tab('shkrn_feature_options', array(
    'id' => 'menu_settings',
    'title' => 'ساخت منو پنل کاربری',
    'desc' => 'تنظیمات بیشتر برای پنل کاربری ووکامرس',
    'fields' => array(
        array(
            'id' => 'menu_custom',
            'type' => 'switcher',
            'title' => 'فعال سازی منوی سفارشی',
            'desc' => 'به هر تعدادی که نیاز داشته باشید میتوانید منو به پنل کاربری اضافه کنید',
            'default' => false,
        ),
        array(
            'id' => 'menu_repeater',
            'type' => 'repeater',
            'title' => 'تکرار شونده',
            'fields' => array(
                array(
                    'id' => 'menu_title',
                    'type' => 'text',
                    'title' => 'عنوان منو',
                    'default' => '',
                ),
                array(
                    'id' => 'menu_endpoint',
                    'type' => 'text',
                    'title' => 'نامک منو',
                    'default' => '',
                ),
                array(
                    'id' => 'menu_callback',
                    'type' => 'text',
                    'title' => 'شورتکد محتوا',
                    'desc' => 'محتوای این شورتکد داخل صفحه منوی مورد نظر اجرا میشود',
                    'default' => '',
                ),
            ),
            'require' => array(
                array('menu_custom', '=', true),
            ),
        ),
    ),
));