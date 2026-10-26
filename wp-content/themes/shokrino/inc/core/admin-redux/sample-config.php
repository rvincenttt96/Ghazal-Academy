<?php
/**
 * ReduxFramework Sample Config File
 * For full documentation, please visit: http://devs.redux.io/
 *
 * @package Redux Framework
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Redux' ) ) {
    return;
}

// This is your option name where all the Redux data is stored.
$opt_name = 'shokrino_options';  // YOU MUST CHANGE THIS.  DO NOT USE 'redux_demo' IN YOUR PROJECT!!!

// Uncomment to disable demo mode.
/* Redux::disable_demo(); */  // phpcs:ignore Squiz.PHP.CommentedOutCode

$dir = dirname( __FILE__ ) . DIRECTORY_SEPARATOR;

/*
 * --> Used within different fields. Simply examples. Search for ACTUAL DECLARATION for field examples
 */

// Background Patterns Reader.
$sample_patterns_path = Redux_Core::$dir . '../sample/patterns/';
$sample_patterns_url  = Redux_Core::$url . '../sample/patterns/';
$sample_patterns      = array();

if ( is_dir( $sample_patterns_path ) ) {
    $sample_patterns_dir = opendir( $sample_patterns_path );

    if ( $sample_patterns_dir ) {

        // phpcs:ignore WordPress.CodeAnalysis.AssignmentInCondition
        while ( false !== ( $sample_patterns_file = readdir( $sample_patterns_dir ) ) ) {
            if ( stristr( $sample_patterns_file, '.png' ) !== false || stristr( $sample_patterns_file, '.jpg' ) !== false ) {
                $name              = explode( '.', $sample_patterns_file );
                $name              = str_replace( '.' . end( $name ), '', $sample_patterns_file );
                $sample_patterns[] = array(
                    'alt' => $name,
                    'img' => $sample_patterns_url . $sample_patterns_file,
                );
            }
        }
    }
}

// Used to execept HTML tags in description arguments where esc_html would remove.
$kses_exceptions = array(
    'a'      => array(
        'href' => array(),
    ),
    'strong' => array(),
    'br'     => array(),
    'code'   => array(),
);

/*
 * ---> BEGIN ARGUMENTS
 */

/**
 * All the possible arguments for Redux.
 * For full documentation on arguments, please refer to: https://devs.redux.io/core/arguments/
 */
$theme = wp_get_theme(); // For use with some settings. Not necessary.

// TYPICAL -> Change these values as you need/desire.
$args = array(
    // This is where your data is stored in the database and also becomes your global variable name.
    'opt_name'                  => $opt_name,

    // Name that appears at the top of your panel.
    'display_name'              => SHKRN_THEME_NAME,

    'templates_path'            => dirname(__FILE__).'/templates/panel/',

    // Version that appears at the top of your panel.
    'display_version'           => SHKRN_THEME_VERSION,

    // Specify if the admin menu should appear or not. Options: menu or submenu (Under appearance only).
    'menu_type'                 => 'submenu',

    // Show the sections below the admin menu item or not.
    'allow_sub_menu'            => true,

    // The text to appear in the admin menu.
    'menu_title'                => 'تنظیمات قالب',

    // The text to appear on the page title.
    'page_title'                => 'تنظیمات قالب شکرینو',

    // Disable to create your own Google fonts loader.
    'disable_google_fonts_link' => false,

    // Show the panel pages on the admin bar.
    'admin_bar'                 => true,

    // Icon for the admin bar menu.
    'admin_bar_icon'            => 'dashicons-layout',

    // Priority for the admin bar menu.
    'admin_bar_priority'        => 50,

    // Sets a different name for your global variable other than the opt_name.
    'global_variable'           => $opt_name,

    // Show the time the page took to load, etc. (forced on while on localhost or when WP_DEBUG is enabled).
    'dev_mode'                  => false,

    // Enable basic customizer support.
    'customizer'                => true,

    // Allow the panel to open expanded.
    'open_expanded'             => false,

    // Disable the save warning when a user changes a field.
    'disable_save_warn'         => false,

    // Order where the menu appears in the admin area. If there is any conflict, something will not show. Warning.
    'page_priority'             => 2,

    // For a full list of options, visit: http://codex.wordpress.org/Function_Reference/add_submenu_page#Parameters.
    'page_parent'               => 'features_shokrino',

    // Permissions needed to access the options panel.
    'page_permissions'          => 'manage_options',

    // Specify a custom URL to an icon.
    'menu_icon'                 => 'dashicons-layout',

    // Force your panel to always open to a specific tab (by id).
    'last_tab'                  => '',

    // Icon displayed in the admin panel next to your menu_title.
    'page_icon'                 => 'icon-themes',

    // Page slug used to denote the panel, will be based off page title, then menu title, then opt_name if not provided.
    'page_slug'                 => $opt_name,

    // On load save the defaults to DB before user clicks save.
    'save_defaults'             => true,

    // Display the default value next to each field when not set to the default value.
    'default_show'              => false,

    // What to print by the field's title if the value shown is default.
    'default_mark'              => '*',

    // Shows the Import/Export panel when not used as a field.
    'show_import_export'        => true,

    // The time transinets will expire when the 'database' arg is set.
    'transient_time'            => 60 * MINUTE_IN_SECONDS,

    // Global shut-off for dynamic CSS output by the framework. Will also disable google fonts output.
    'output'                    => true,

    // Allows dynamic CSS to be generated for customizer and google fonts,
    // but stops the dynamic CSS from going to the page head.
    'output_tag'                => true,

    // If you prefer not to use the CDN for ACE Editor.
    // You may download the Redux Vendor Support plugin to run locally or embed it in your code.
    'use_cdn'                   => true,

    // Set the theme of the option panel.  Use 'wp' to use a more modern style, default is classic.
    'admin_theme'               => 'wp',

    // Enable or disable flyout menus when hovering over a menu with submenus.
    'flyout_submenus'           => true,

    // Mode to display fonts (auto|block|swap|fallback|optional)
    // See: https://developer.mozilla.org/en-US/docs/Web/CSS/@font-face/font-display.
    'font_display'              => 'swap',

    // HINTS.
    'hints'                     => array(
        'icon'          => 'el el-question-sign',
        'icon_position' => 'right',
        'icon_color'    => 'lightgray',
        'icon_size'     => 'normal',
        'tip_style'     => array(
            'color'   => 'red',
            'shadow'  => true,
            'rounded' => false,
            'style'   => '',
        ),
        'tip_position'  => array(
            'my' => 'top left',
            'at' => 'bottom right',
        ),
        'tip_effect'    => array(
            'show' => array(
                'effect'   => 'slide',
                'duration' => '500',
                'event'    => 'mouseover',
            ),
            'hide' => array(
                'effect'   => 'slide',
                'duration' => '500',
                'event'    => 'click mouseleave',
            ),
        ),
    ),

    // FUTURE -> Not in use yet, but reserved or partially implemented. Use at your own risk.
    // possible: options, theme_mods, theme_mods_expanded, transient. Not fully functional, warning!
    'database'                  => '',
    'network_admin'             => false,
    'search'                    => false,
);


// ADMIN BAR LINKS -> Setup custom links in the admin bar menu as external items.
// PLEASE CHANGE THEME BEFORE RELEASEING YOUR PRODUCT!!
// If these are left unchanged, they will not display in your panel!
/*$args['admin_bar_links'][] = array(
	'id'    => 'shokrino-docs',
	'href'  => '',
	'title' => __( 'فیلم های آموزشی شکرینو', 'shokrino' ),
);*/

// SOCIAL ICONS -> Setup custom links in the footer for quick links in your panel footer icons.
// PLEASE CHANGE THEME BEFORE RELEASEING YOUR PRODUCT!!
// If these are left unchanged, they will not display in your panel!
/*$args['share_icons'][] = array(
	'url'   => '',
	'title' => 'مارا در اینستاگرام دنبال کنید',
	'icon'  => 'el el-instagram',
);*/

// Panel Intro text -> before the form.
if ( ! isset( $args['global_variable'] ) || false !== $args['global_variable'] ) {
    if ( ! empty( $args['global_variable'] ) ) {
        $v = $args['global_variable'];
    } else {
        $v = str_replace( '-', '_', $args['opt_name'] );
    }

    // translators:  Panel opt_name.
    $args['intro_text'] = '<p>' . sprintf( '', '<strong>' . $v . '</strong>' ) . '<p>';
} else {
    $args['intro_text'] = '<p>' . '' . '</p>';
}

// Add content after the form.
$args['footer_text'] = '<p>' . '' . '</p>';

Redux::set_args( $opt_name, $args );

/*
 * ---> END ARGUMENTS
 */

/*
 * ---> START HELP TABS
 */
$help_tabs = array(
    array(
        'id'      => 'redux-help-tab-1',
        'title'   => esc_html__( 'Theme Information 1', 'shokrino' ),
        'content' => '<p>' . esc_html__( 'This is the tab content, HTML is allowed.', 'shokrino' ) . '</p>',
    ),
    array(
        'id'      => 'redux-help-tab-2',
        'title'   => esc_html__( 'Theme Information 2', 'shokrino' ),
        'content' => '<p>' . esc_html__( 'This is the tab content, HTML is allowed.', 'shokrino' ) . '</p>',
    ),
);
Redux::set_help_tab( $opt_name, $help_tabs );

// Set the help sidebar.
$content = '<p>' . esc_html__( 'This is the sidebar content, HTML is allowed.', 'shokrino' ) . '</p>';

Redux::set_help_sidebar( $opt_name, $content );

/*
 * <--- END HELP TABS
 */

/*
 * ---> START SECTIONS
 */

// -> START Fields Added By Shokrino Team
//////////////////////////////////////////////////////////////////////////////////////////////////////////////


Redux::set_section(
    $opt_name,
    array(
        'title' => esc_html__('تنظیمات اصلی', 'shokrino'),
        'id' => 'shokrino_general',
        'icon' => 'el-icon-home',
        'fields' => array(
            array(
                'id' => 'primary_color',
                'type' => 'color',
                'title' => esc_html__('رنگ اصلی سایت', 'shokrino'),
                'description' => esc_html__('این رنگ در قسمت های مختلف قالب اعمال میشود تا سایتی مخصوص به خودتان داشته باشید', 'shokrino'),
                'validate' => 'color',
                'transparent' => false,
                'default' => '#00B4FF'
            ),
            array(
                'id' => 'userpanel_enable',
                'type' => 'switch',
                'title' => esc_html__( 'فعال سازی قابلیت پنل کاربری', 'shokrino' ),
                'subtitle' => esc_html__( 'برای استفاده از افزونه های پنل کاربری این گزینه را غیرفعال کنید تا همه قابلیت های مرتبط خاموش شود و تداخل بوجود نیاید', 'shokrino' ),
                'default' => true,
                'on' => esc_html__('فعال', 'shokrino'),
                'off' => esc_html__('غیرفعال', 'shokrino')
            ),
            array(
                'id' => 'ticket_enable',
                'type' => 'switch',
                'title' => esc_html__( 'فعال سازی قابلیت تیکت', 'shokrino' ),
                'subtitle' => esc_html__( 'برای استفاده از سیستم تیکت اختصاصی قالب سایر افزونه های مشابه را غیرفعال کرده و این گزینه را فعال کنید', 'shokrino' ),
                'default' => false,
                'on' => esc_html__('فعال', 'shokrino'),
                'off' => esc_html__('غیرفعال', 'shokrino')
            ),
            array(
                'id' => 'avatar_enable',
                'type' => 'switch',
                'title' => esc_html__( 'فعال سازی قابلیت آواتار کاربران', 'shokrino' ),
                'subtitle' => esc_html__( 'برای استفاده از سیستم عکس پروفایل برای کاربران سایت این گزینه را فعال نمایید', 'shokrino' ),
                'default' => true,
                'on' => esc_html__('فعال', 'shokrino'),
                'off' => esc_html__('غیرفعال', 'shokrino')
            ),
            array(
                'id' => 'show_admin_styles',
                'type' => 'switch',
                'title' => esc_html__( 'فعال سازی قابلیت استایل های ادمین', 'shokrino' ),
                'subtitle' => esc_html__( 'با غیرفعال سازی این گزینه، استایل های همین پنل پیشخوان برای شما غیرفعال خواهد شد', 'shokrino' ),
                'default' => true,
                'on' => esc_html__('فعال', 'shokrino'),
                'off' => esc_html__('غیرفعال', 'shokrino')
            ),
            array(
                'id' => 'show_darkmode_shokrino',
                'type' => 'switch',
                'title' => esc_html__( 'فعال سازی قابلیت دارک مود', 'shokrino' ),
                'subtitle' => esc_html__( 'با قرار دادن این گزینه روی فعال ، کلید تغییر حالت شب (دارک مود) به هدر تمامی صفحات سایت اضافه خواهد شد', 'shokrino' ),
                'default' => false,
                'on' => esc_html__('فعال', 'shokrino'),
                'off' => esc_html__('غیرفعال', 'shokrino')
            ),
            array(
                'id' => 'show_wallet_userpanel',
                'type' => 'switch',
                'title' => esc_html__( 'فعال سازی قابلیت کیف پول', 'shokrino' ),
                'subtitle' => esc_html__( 'با قرار دادن این گزینه روی فعال ، سیستم کیف پول پرداخت در سایت شما فعال خواهد شد', 'shokrino' ),
                'desc' => esc_html__( ' نصب و فعال باشد woo-wallet (TeraWallet) برای کار کردن کیف پول باید افزونه ', 'shokrino' ),
                'default' => false,
                'on' => esc_html__('فعال', 'shokrino'),
                'off' => esc_html__('غیرفعال', 'shokrino')
            ),
            array(
                'id' => 'show_readbar_shokrino',
                'type' => 'switch',
                'title' => esc_html__( 'فعال سازی قابلیت نوار پیشرفت اسکرول', 'shokrino' ),
                'subtitle' => esc_html__( 'با قرار دادن این گزینه روی فعال ، یک نوار به بالای تمامی صفحات سایت اضافه خواهد شد که میزان اسکرول را نمایش میدهد', 'shokrino' ),
                'default' => false,
                'on' => esc_html__('فعال', 'shokrino'),
                'off' => esc_html__('غیرفعال', 'shokrino')
            ),
            array(
                'id' => 'show_back_to_top_shokrino',
                'type' => 'switch',
                'title' => esc_html__( 'فعال سازی کلید بازگشت به بالا', 'shokrino' ),
                'subtitle' => esc_html__( 'با قرار دادن این گزینه روی فعال ، یک کلید در گوشه پایین سمت راست سایت به کاربر نمایش داده میشود تا با کلید روی آن به قسمت بالای سایت هدایت میشود', 'shokrino' ),
                'default' => false,
                'on' => esc_html__('فعال', 'shokrino'),
                'off' => esc_html__('غیرفعال', 'shokrino')
            ),
            array(
                'id' => 'optimize_images',
                'type' => 'switch',
                'title' => esc_html__( 'فعال سازی قابلیت بهینه سازی سایز تصاویر', 'shokrino' ),
                'subtitle' => esc_html__( 'امکان افت کیفیت تصاویر وجود دارد در این صورت غیرفعال نمایید', 'shokrino' ),
                'default' => false,
                'on' => esc_html__('فعال', 'shokrino'),
                'off' => esc_html__('غیرفعال', 'shokrino')
            ),
            array(
                'id' => 'container-option',
                'type' => 'slider',
                'title' => 'عرض محتوای کلی سایت (container)',
                'subtitle' => 'در حالت پیشفرض 1220px است',
                'desc' => 'محدوده دربرگیرنده محتوای سایت را تنظیم کنید',
                'default' => 1220,
                'min' => 1000,
                'step' => 1,
                'max' => 1500,
                'display_value' => 'text',
            ),
            array(
                'id' => 'logo-option',
                'type' => 'media',
                'title' => esc_html__('تصویر لوگو', 'shokrino' ),
                'subtitle' => esc_html__('آپلود تصویر: با فرمت های JPG ، PNG و SVG', 'shokrino' ),
                'description' => esc_html__('لطفا لوگو خود را با ابعاد 57 * 160 پیکسل بارگذاری کنید. (درصورت استفاده از ابعاد دیگر به هم ریخته خواهد شد)', 'shokrino' ),
                'url' => true,
                'default' => array(
                    'url' => SHKRN_ASSETS . '/img/logo.png'
                ),
            ),
            array(
                'id' => 'logo-option-dark',
                'type' => 'media',
                'title' => esc_html__('تصویر لوگو در حالت دارک', 'shokrino' ),
                'subtitle' => esc_html__('آپلود تصویر: با فرمت های JPG ، PNG و SVG', 'shokrino' ),
                'description' => esc_html__('تصویری که در حالت دارک مود باید نمایش داده شود را انتخاب کنید.', 'shokrino' ),
                'url' => true,
                'default' => array(
                    'url' => SHKRN_ASSETS . '/img/logo-dark.png'
                ),
            ),
            array(
                'id' => 'favicon-option',
                'type' => 'media',
                'title' => esc_html__('لوگو فاویکون', 'shokrino' ),
                'subtitle' => esc_html__('آپلود تصویر: با فرمت های JPG ، PNG و SVG', 'shokrino' ),
                'description' => esc_html__('آیکون کوچک کنار تب مرورگر سایت شما (ابعاد پیشنهادی: 110 * 110 پیکسل )', 'shokrino' ),
                'url' => true,
                'default' => array(
                    'url' => SHKRN_ASSETS . '/img/favicon.png'
                ),
            ),
        )
    ));


# Header Settings
$img_header_one = get_template_directory_uri() . '/inc/core/admin-redux/templates/panel/img/img_header_one.png';
$img_header_two = get_template_directory_uri() . '/inc/core/admin-redux/templates/panel/img/img_header_two.png';
$img_header_three = get_template_directory_uri() . '/inc/core/admin-redux/templates/panel/img/img_header_three.png';
Redux::set_section(
    $opt_name,
    array(
        'title' => esc_html__('تنظیمات هدر (سربرگ)', 'shokrino'),
        'id' => 'shokrino_header_options',
        'icon' => 'el-icon-wrench',
        'fields' => array(
            array(
                'id'       => 'opt-raw_header_one',
                'type'     => 'raw',
                'content'  => '<img src="'. $img_header_one .'">
						   <style></style>',
                'required' => array(
                    array(
                        'header_style',
                        '=',
                        'header_one'
                    )
                )
            ),
            array(
                'id'       => 'opt-raw_header_two',
                'type'     => 'raw',
                'content'  => '<img src="'. $img_header_two .'">
						   <style></style>',
                'required' => array(
                    array(
                        'header_style',
                        '=',
                        'header_two'
                    )
                )
            ),
            array(
                'id'       => 'opt-raw_header_three',
                'type'     => 'raw',
                'content'  => '<img src="'. $img_header_three .'">
						   <style></style>',
                'required' => array(
                    array(
                        'header_style',
                        '=',
                        'header_three'
                    )
                )
            ),
            array(
                'id' => 'header_style',
                'type' => 'select',
                'title' => esc_html__('نوع هدر (سربرگ)', 'shokrino'),
                'subtitle' => esc_html__('از بین هدر های قالب ، استایل مورد نظر را انتخاب کنید', 'shokrino'),
                'default' => 'header_one',
                'options' => array(
                    'header_one' => esc_html__('هدر جعبه ای', 'shokrino'),
                    'header_two' => esc_html__('هدر تمام عرض', 'shokrino'),
                    'header_three' => esc_html__('هدر تمام عرض دوم', 'shokrino'),
                    'header_el' => esc_html__('هدر المنتور پرو', 'shokrino'),
                ),
                'select2' => array('allowClear' => false)
            ),
            array(
                'id'       => 'elementor-selector-header',
                'type'     => 'select',
                'title'    => esc_html__('قالب المنتور را انتخاب کنید', 'shokrino'),
                'desc'     => esc_html__('قبل از انتخاب قالب بایستی از بخش "قالب ها" در المنتور پرو ، یک قالب هدر بسازید و سپس در این بخش انتخاب کنید', 'shokrino'),
                'options'  => get_elementor_templates_shkrn(),
                'required' => array(
                    array(
                        'header_style',
                        '=',
                        'header_el'
                    )
                )
            ),
            array(
                'id' => 'show_cart',
                'type' => 'switch',
                'title' => esc_html__( 'نمایش کلید سبد خرید', 'shokrino' ),
                'subtitle' => esc_html__( 'کلید سبد خرید در هدر فعال باشد یا حذف شود؟', 'shokrino' ),
                'default' => true,
                'on' => esc_html__('نمایش', 'shokrino'),
                'off' => esc_html__('مخفی', 'shokrino')
            ),
            array(
                'id' => 'show_search',
                'type' => 'switch',
                'title' => esc_html__( 'نمایش کلید جستجو', 'shokrino' ),
                'subtitle' => esc_html__( 'کلید سرچ در هدر فعال باشد یا حذف شود؟', 'shokrino' ),
                'default' => true,
                'on' => esc_html__('نمایش', 'shokrino'),
                'off' => esc_html__('مخفی', 'shokrino')
            ),
            array(
                'id' => 'header_has_users_info ',
                'type' => 'switch',
                'title' => esc_html__( 'نمایش نام و نقش کاربر', 'shokrino' ),
                'subtitle' => esc_html__( 'نام کاربر به همراه نقش کاربری آن بعد از ورود به اکانت در هدر درج میشود.', 'shokrino' ),
                'default' => true,
                'on' => esc_html__('نمایش', 'shokrino'),
                'off' => esc_html__('مخفی', 'shokrino')
            ),
            array(
                'id' => 'header_button_type',
                'type' => 'select',
                'title' => esc_html__('نوع دکمه هدر', 'shokrino'),
                'subtitle' => esc_html__('میتوانید نوع استفاده از کلید هدر را مشخص کنید', 'shokrino'),
                'default' => 'header_button_link',
                'options' => array(
                    'header_button_link' => esc_html__('متصل به لینک', 'shokrino'),
                    'header_button_digits' => esc_html__('متصل به فرم دیجیتس', 'shokrino'),
                    'header_button_popup_elementor' => esc_html__('متصل به پاپ آپ المنتور', 'shokrino'),
                ),
                'select2' => array('allowClear' => false)
            ),
            array(
                'id' => 'header_button_custom_link',
                'type' => 'text',
                'title' => esc_html__('لینک کلید "ورود و ثبت نام"', 'shokrino'),
                'subtitle' => esc_html__( 'در حالت پیشفرض به حساب کاربری ووکامرس متصل است', 'shokrino' ),
                'required' => array(
                    array(
                        'header_button_type',
                        '=',
                        'header_button_link'
                    )
                )
            ),
            array(
                'id' => 'header_button_custom_popup',
                'type' => 'text',
                'title' => esc_html__('لینک کلید پاپ آپ المنتور', 'shokrino'),
                'desc' => esc_html__( 'ابتدا در یک برگه بصورت تستی یک المان دکمه قرار دهید و پاپ آپ را به آن متصل کنید ، سپس انتشار را بزنید و به همان برگه مراجعه کنید تا تست کنید که کلید به درستی پاپ آپ را باز میکند ، اگر پاپ آپ به درستی کار میکرد کافیست روی همان المان دکمه کلیک راست کرده و copy link را بزنید و آن لینک را در این بخش قرار دهید ، دقت کنید که لینک باید با elementor-action# شروع شود و هرچیزی که قبل از # وجود دارد باید حذف شود بعد از وارد کردن لینک تغییرات را ذخیره کنید . سپس میتوانید آن دکمه تستی را نیز حذف کنید', 'shokrino' ),
                'required' => array(
                    array(
                        'header_button_type',
                        '=',
                        'header_button_popup_elementor'
                    )
                )
            ),
            array(
                'id' => 'header_button_custom_text',
                'type' => 'text',
                'title' => esc_html__('متن دکمه "ورود و ثبت نام"', 'shokrino'),
                'subtitle' => esc_html__( 'اگر نمی خواهید تغییر کند خالی بگذارید', 'shokrino' ),
                'required' => array(
                    array(
                        'header_button_type',
                        '!=',
                        'header_button_digits'
                    )
                )
            ),
            array(
                'id'      => 'panel-header-sort',
                'type'    => 'sorter',
                'title'   => 'آپشن های منوی پنل کاربری در هدر',
                'subtitle'    => 'میتوانید المان های این منو را اضافه و یا کم کنید و حتی ترتیب نمایششان را تغییر دهید',
                'desc'    => 'آیتم هایی که داخل باکس enable قرار میدهید نمایش داده میشود و آیتم های disable نمایش داده نمیشود',
                'options' => array(
                    'enabled'  => array(
                        'show-user-panel' => 'مشاهده پنل کاربری',
                        'woo-wallet' => 'موجودی کیف پول',
                        'downloads' => 'دانلود فایل ها',
                        'orders' => 'مشاهده سفارشات',
                        'notifs' => 'اعلان های شما',
                        'tickets' => 'تیکت های شما',
                        'edit-profile' => 'ویرایش پروفایل',
                        'logout' => 'خروج از حساب',
                    ),
                    'disabled' => array(
                    )
                ),
            ),
        ),
    ));

# Footer Settings
$img_footer_one = get_template_directory_uri() . '/inc/core/admin-redux/templates/panel/img/img_footer_one.png';
$img_footer_two = get_template_directory_uri() . '/inc/core/admin-redux/templates/panel/img/img_footer_two.png';
$img_footer_three = get_template_directory_uri() . '/inc/core/admin-redux/templates/panel/img/img_footer_three.png';
$img_abzarak = get_template_directory_uri() . '/inc/core/admin-redux/templates/panel/img/abzarak-ha.png';
Redux::setSection($opt_name, array(
    'title' => esc_html__('تنظیمات فوتر (پابرگ)', 'shokrino'),
    'id' => 'footer_settings',
    'icon' => 'el-icon-photo',
    'fields' => array(
        array(
            'id'       => 'opt-raw_footer_one',
            'type'     => 'raw',
            'content'  => '<img src="'. $img_footer_one .'">
						   <style></style>',
            'required' => array(
                array(
                    'footer_style',
                    '=',
                    'footer_one'
                )
            )
        ),
        array(
            'id'       => 'opt-raw_footer_two',
            'type'     => 'raw',
            'content'  => '<img src="'. $img_footer_two .'">
						   <style></style>',
            'required' => array(
                array(
                    'footer_style',
                    '=',
                    'footer_two'
                )
            )
        ),
        array(
            'id'       => 'opt-raw_footer_three',
            'type'     => 'raw',
            'content'  => '<img src="'. $img_footer_three .'">
                           رنگ زمینه فوتر همان رنگ انتخابی شما در بخش (تنظیمات اصلی) خواهد بود.
						   <style></style>',
            'required' => array(
                array(
                    'footer_style',
                    '=',
                    'footer_three'
                )
            )
        ),
        array(
            'id' => 'footer_style',
            'type' => 'select',
            'title' => esc_html__('نوع فوتر (پابرگ)', 'shokrino'),
            'subtitle' => esc_html__('از بین فوتر های قالب ، استایل مورد نظر را انتخاب کنید', 'shokrino'),
            'default' => 'footer_one',
            'options' => array(
                'footer_one' => esc_html__('فوتر جعبه ای', 'shokrino'),
                'footer_two' => esc_html__('فوتر تمام عرض', 'shokrino'),
                'footer_three' => esc_html__('فوتر تمام عرض دوم', 'shokrino'),
                'footer_el' => esc_html__('فوتر المنتور پرو', 'shokrino'),
            ),
            'select2' => array('allowClear' => false)
        ),
        array(
            'id'       => 'elementor-selector-footer',
            'type'     => 'select',
            'title'    => esc_html__('قالب المنتور را انتخاب کنید', 'shokrino'),
            'desc'     => esc_html__('قبل از انتخاب قالب بایستی از بخش "قالب ها" در المنتور پرو ، یک قالب فوتر بسازید و سپس در این بخش انتخاب کنید', 'shokrino'),
            'options'  => get_elementor_templates_shkrn(),
            'required' => array(
                array(
                    'footer_style',
                    '=',
                    'footer_el'
                )
            )
        ),
        array(
            'id' => 'show_social',
            'type' => 'switch',
            'title' => esc_html__( 'نمایش کلیدهای شبکه های اجتماعی', 'shokrino' ),
            'subtitle' => esc_html__( 'شبکه های اجتماعی در فوتر فعال باشد یا حذف شود؟', 'shokrino' ),
            'default' => true,
            'on' => esc_html__('نمایش', 'shokrino'),
            'off' => esc_html__('مخفی', 'shokrino')
        ),
        array(
            'id' => 'show_footer_menu',
            'type' => 'switch',
            'title' => esc_html__( 'نمایش منوی فوتر', 'shokrino' ),
            'subtitle' => esc_html__( 'فهرست در فوتر فعال باشد یا حذف شود؟', 'shokrino' ),
            'default' => false,
            'on' => esc_html__('نمایش', 'shokrino'),
            'off' => esc_html__('مخفی', 'shokrino')
        ),
        array(
            'id'       => 'opt-raw_footer_info',
            'type'     => 'raw',
            'content'  => '<h4>برای تغییر و یا اضافه کردن محتوای درباره ما ، ارتباط با ما و موارد دیگر به فوتر از قسمت "نمایش > ابزارک ها" استفاده کنید.</h4>
						   <img src="'. $img_abzarak .'">
						   <style>
						   #shokrino_options-opt-raw_footer_info {
								line-height: 30px;
								color: white;
								background-image: linear-gradient(-158deg,#5b62e5,#8f94fb);
								padding: 0px 25px 15px;
								border-radius: 22px;
								box-shadow: -2px 4px 30px 5px #8b90f8;
								margin: 30px;
							}
							#shokrino_options-opt-raw_footer_info img {
								width: 63% !important;
								display: block;
								margin: -6px auto 12px !important;
								border-radius: 25px;
							}
							</style>'
        ),
        array(
            'id' => 'footer_copyright',
            'type' => 'editor',
            'title' => esc_html__('متن کپی رایت', 'shokrino'),
            'desc' => esc_html__('متنی که میخواهید در قسمت کپی رایت فوتر قرار گیرد اینجا وارد کنید.', 'shokrino'),
        ),
    )
));

# Notifbar
Redux::setSection($opt_name, array(
    'title' => esc_html__('نوار اعلان', 'shokrino'),
    'id' => 'notification-bar',
    'icon' => 'el el-cog',
    'fields' => array(
        array(
            'id' => 'notification-bar-show',
            'title' => esc_html__('نوار اعلان', 'shokrino'),
            'subtitle' => esc_html__('با فعال سازی این قابلیت ، یک نوار بالای هدر نمایش داده میشود با پیام دلخواه شما', 'shokrino'),
            'type' => 'switch',
            'default' => false,
            'on' => esc_html__('فعال', 'shokrino'),
            'off' => esc_html__('خاموش', 'shokrino')
        ),
        array(
            'id' => 'notification-bar-btn-show',
            'title' => esc_html__('دکمه کال تو اکشن', 'shokrino'),
            'subtitle' => esc_html__('با فعال سازی این گزینه یک کلید در نوار اعلان با متن و لینک دلخواه شما نمایش داده میشود', 'shokrino'),
            'type' => 'switch',
            'default' => false,
            'on' => esc_html__('فعال', 'shokrino'),
            'off' => esc_html__('خاموش', 'shokrino'),
            'required' => array(
                array(
                    'notification-bar-show',
                    '=',
                    'true',
                )
            )
        ),
        array(
            'id'          => 'notification-bar-btn-txt',
            'type'        => 'text',
            'title'       => esc_html__('عنوان دکمه', 'shokrino'),
            'default' => 'کلیک کنید',
            'required' => array(
                array(
                    'notification-bar-show',
                    '=',
                    'true',
                ),
                array(
                    'notification-bar-btn-show',
                    '=',
                    'true',
                )
            )
        ),
        array(
            'id'          => 'notification-bar-btn-link',
            'type'        => 'text',
            'title'       => esc_html__('لینک دکمه', 'shokrino'),
            'required' => array(
                array(
                    'notification-bar-show',
                    '=',
                    'true',
                ),
                array(
                    'notification-bar-btn-show',
                    '=',
                    'true',
                )
            )
        ),
        array(
            'id' => 'notification-bar-countdown-show',
            'title' => esc_html__('شمارنده معکوس', 'shokrino'),
            'subtitle' => esc_html__('میتوانید یک شمارنده معکوس قرار دهید که تا زمان دلخواه شما بصورت معکوس بشمارد و به کاربر نمایش دهد', 'shokrino'),
            'type' => 'switch',
            'default' => false,
            'on' => esc_html__('فعال', 'shokrino'),
            'off' => esc_html__('خاموش', 'shokrino'),
            'required' => array(
                array(
                    'notification-bar-show',
                    '=',
                    'true',
                ),
            )
        ),
        array(
            'id'          => 'notif-bar-date',
            'type'        => 'date',
            'title'       => esc_html__('انتخاب تاریخ', 'shokrino'),
            'subtitle'    => esc_html__('تاریخی که شمارنده تا آن میشمارد را به میلادی انتخاب کنید', 'shokrino'),
            'placeholder' => 'برای باز شدن تقویم اینجا کلیک کنید',
            'required' => array(
                array(
                    'notification-bar-show',
                    '=',
                    'true',
                ),
                array(
                    'notification-bar-countdown-show',
                    '=',
                    'true',
                )
            )
        ),
        array(
            'id'          => 'notif-bar-time',
            'type'        => 'text',
            'title'       => esc_html__('انتخاب ساعت', 'shokrino'),
            'subtitle'    => esc_html__('ساعتی که شمارنده تا آن زمان میشمارد را وارد کنید (برای مثال 22:30:50)', 'shokrino'),
            'desc'    => esc_html__('میتوانید خالی بگذارید تا بصورت پیشفرض ساعت 24:00:00 درنظر گرفته شود', 'shokrino'),
            'placeholder' => 'وارد کردن ساعت اختیاری است',
            'required' => array(
                array(
                    'notification-bar-show',
                    '=',
                    'true',
                ),
                array(
                    'notification-bar-countdown-show',
                    '=',
                    'true',
                )
            )
        ),
        array(
            'id' => 'text-notif-bar',
            'type' => 'textarea',
            'title' => 'متن نوار اعلان',
            'subtitle' => 'دقت کنید که طول متن بیش از حد نباشد تا در یک خط نمایش داده شود',
            'default' => 'این یک اعلان تستی برای پیشنمایش در پوسته آموزشی شکرینو است',
            'required' => array(
                array(
                    'notification-bar-show',
                    '=',
                    'true',
                ),
            )
        ),
        array(
            'id' => 'text-notif-bar-have-link',
            'title' => esc_html__('متن لینک دار', 'shokrino'),
            'subtitle' => esc_html__('آیا به متن لینک دلخواه شما اضافه شود؟', 'shokrino'),
            'type' => 'switch',
            'default' => false,
            'on' => esc_html__('بله', 'shokrino'),
            'off' => esc_html__('خیر', 'shokrino'),
            'required' => array(
                array(
                    'notification-bar-show',
                    '=',
                    'true',
                )
            )
        ),

        array(
            'id' => 'notif-bar-bg-color',
            'type' => 'color',
            'title' => esc_html__('رنگ زمینه نوار اعلان', 'shokrino'),
            'validate' => 'color',
            'transparent' => false,
            'default' => '#00b4ff',
            'required' => array(
                array(
                    'notification-bar-show',
                    '=',
                    'true',
                )
            )
        ),
    )));

# Typography
Redux::setSection($opt_name, array(
    'title' => esc_html__('فونت و تایپوگرافی', 'shokrino'),
    'id' => 'typography',
    'icon' => 'el-icon-fontsize',
    'fields' => array(
        array(
            'id'       => 'opt-raw1',
            'type'     => 'raw',
            'content'  => '<h4>برای شخصی سازی فونت های سایت و افزودن فونت های سفارشی لطفا ویدیو تهیه شده توسط تیم شکرینو را مشاهده کنید تا بصورت قدم به قدم و کامل و با جزئیات فونت های دلخواه خود را با المنتور به قالب اضافه کنید.</h4>
						   <style>
						   #shokrino_options-opt-raw1 h4 {
								line-height: 30px;
								color: white;
								background-image: linear-gradient(-158deg,#5b62e5,#8f94fb);
								padding: 15px 25px;
								border-radius: 22px;
								box-shadow: -2px 4px 30px 5px #8b90f8;
							}
							</style>'
        ),
        array(
            'id' => 'font_style',
            'type' => 'select',
            'title' => esc_html__('فونت سایت', 'shokrino'),
            'desc' => esc_html__('کدام فونت برای سایت شما اجرا شود؟', 'shokrino'),
            'default' => 'yekanbakh',
            'options' => array(
                'yekanbakh' => __('فونت پیشفرض قالب', 'shokrino' ),
                'custom' => __('فونت دلخواه از المنتور', 'shokrino' ),
            ),
            'select2' => array('allowClear' => false)
        ),
        array(
            'id' => 'font_family',
            'type' => 'text',
            'title' => esc_html__('نام فونت آپلود شده در المنتور را وارد کنید', 'shokrino'),
            'desc' => esc_html__( 'اگر نمی خواهید تغییر کند خالی بگذارید', 'shokrino' ),
            'required' => array(
                array(
                    'font_style',
                    '=',
                    'custom'
                )
            )
        ),
    )
));

# Blog Settings
$img_blog_page = get_template_directory_uri() . '/inc/core/admin-redux/templates/panel/img/blog-page.png';
$img_single_page = get_template_directory_uri() . '/inc/core/admin-redux/templates/panel/img/single-page.png';
Redux::setSection($opt_name, array(
    'title' => esc_html__('بلاگ', 'shokrino'),
    'id' => 'blog_setting',
    'icon' => 'el-icon-pencil',
    'fields' => array(
        array(
            'id'       => 'opt-raw_blog',
            'type'     => 'raw',
            'content'  => '<h3>تنظیمات صفحه بلاگ (همه نوشته ها)</h3>
						   <img src="'. $img_blog_page .'">
						   <style>
						   #shokrino_options-opt-raw_blog img {
								width: 75% !important;
								display: block;
								margin: auto;
							}
						   #shokrino_options-opt-raw_blog h3 {
								margin: 0;
							}
							#shokrino_options-opt-raw_blog {
								border: none !important;
								padding-bottom: 0;
							}
							</style>',
        ),
        array(
            'id' => 'show_sidebar',
            'type' => 'button_set',
            'title' => esc_html__('سایدبار صفحه بلاگ', 'shokrino'),
            'subtitle' => esc_html__('ستون کناری صفحه آرشیو پست نمایش داده شود؟', 'shokrino'),
            'default' => 'yes',
            'options' => array(
                'yes' => esc_html__('سایدبار داشته باشد', 'shokrino'),
                'no' => esc_html__('بدون سایدبار', 'shokrino'),
            )
        ),
        array(
            'id' => 'posts_columns',
            'type' => 'select',
            'title' => esc_html__('تعداد ستون پست ها', 'shokrino'),
            'subtitle' => esc_html__('در هر سطر چه تعداد پست نمایش داد شود؟', 'shokrino'),
            'desc' => esc_html__('در صورت انتخاب چهار ستون یا بیشتر سایدبار را غیرفعال کنید تا فضای کافی باقی بماند', 'shokrino'),
            'options' => array(
                '1' => esc_html__('یک ستون', 'shokrino'),
                '2' => esc_html__('دو ستون', 'shokrino'),
                '3' => esc_html__('سه ستون', 'shokrino'),
                '4' => esc_html__('چهار ستون', 'shokrino'),
                '5' => esc_html__('پنج ستون', 'shokrino'),
                '6' => esc_html__('شش ستون', 'shokrino'),
            ),
            'default' => '3',
            'select2' => array('allowClear' => false)
        ),
        array(
            'id' => 'blog_desc_text',
            'type' => 'button_set',
            'title' => esc_html__('نمایش یا مخفی کردن خلاصه نوشته زیر هر پست', 'shokrino'),
            'desc' => esc_html__('می توانید در صفحه همه پست ها مقداری از خلاصه متن نوشته را زیر پست نمایش دهید.', 'shokrino'),
            'default' => "true",
            'options' => array(
                'true' => __('نمایش', 'shokrino' ),
                'false' => __('مخفی', 'shokrino' ),
            ),
        ),

        array(
            'id' => 'blog_btn_more',
            'type' => 'button_set',
            'title' => esc_html__('نمایش یا مخفی کردن کلید "بیشتر بخوانید"', 'shokrino'),
            'desc' => esc_html__('می توانید در صفحه همه پست ها کلید بیشتر بخوانید را مخفی کنید.', 'shokrino'),
            'default' => "true",
            'options' => array(
                'true' => __('نمایش', 'shokrino' ),
                'false' => __('مخفی', 'shokrino' ),
            ),
        ),
        array(
            'id' => 'blog_btn_more_text',
            'type' => 'text',
            'title' => esc_html__('متن کلید "بیشتر بخوانید"', 'shokrino'),
            'desc' => esc_html__('متنی که میخواهید بجای "بیشتر بخوانید" در صفحه بلاگ نمایش داده شود.', 'shokrino'),
            'required' => array(
                array(
                    'blog_btn_more',
                    '=',
                    'true',
                )
            )
        ),
        array(
            'id'       => 'opt-raw_single',
            'type'     => 'raw',
            'content'  => '<h3>تنظیمات صفحه پست (محتوای نوشته)</h3>
						   <img src="'. $img_single_page .'">
						   <style>
						   #shokrino_options-opt-raw_single img {
								width: 75% !important;
								display: block;
								margin: auto;
							}
						   #shokrino_options-opt-raw_single h3 {
								margin: 0;
							}
							#shokrino_options-opt-raw_single {
								border: none !important;
								padding-bottom: 0;
							}
							</style>',
        ),
        array(
            'id' => 'show_sidebar_single',
            'type' => 'button_set',
            'title' => esc_html__('سایدبار صفحه پست', 'shokrino'),
            'subtitle' => esc_html__('ستون کناری صفحه پست تکی نمایش داده شود؟', 'shokrino'),
            'default' => 'yes',
            'options' => array(
                'yes' => esc_html__('سایدبار داشته باشد', 'shokrino'),
                'no' => esc_html__('بدون سایدبار', 'shokrino'),
            )
        ),
        array(
            'id' => 'content_table_show',
            'type' => 'button_set',
            'title' => esc_html__('نمایش یا مخفی کردن فهرست مطالب', 'shokrino'),
            'desc' => esc_html__('می توانید لیست تیتر های پست (آنچه در این پست میخوانید) را غیرفعال کنید.', 'shokrino'),
            'default' => "true",
            'options' => array(
                'true' => __('نمایش', 'shokrino' ),
                'false' => __('مخفی', 'shokrino' ),
            ),
        ),
        array(
            'id' => 'content_table_text',
            'type' => 'text',
            'title' => esc_html__('متن بالای فهرست مطالب', 'shokrino'),
            'desc' => esc_html__('متنی که میخواهید بجای "آنچه در این پست میخوانید" در صفحه پست ها نمایش داده شود.', 'shokrino'),
            'required' => array(
                array(
                    'content_table_show',
                    '=',
                    true
                )
            ),
            'default' => esc_html__('آنچه در این پست میخوانید')
        ),
        array(
            'id' => 'blog_show_breadcrumb',
            'type' => 'button_set',
            'title' => esc_html__('نمایش یا مخفی کردن مسیر کاربر', 'shokrino'),
            'desc' => esc_html__('می توانید مسیر پست که بالای پست نمایش داده میشود را مخفی کنید.', 'shokrino'),
            'default' => "true",
            'options' => array(
                'true' => __('نمایش', 'shokrino' ),
                'false' => __('مخفی', 'shokrino' ),
            ),
        ),
        array(
            'id' => 'blog_related_posts',
            'type' => 'button_set',
            'title' => esc_html__('نمایش یا مخفی کردن پست های مرتبط', 'shokrino'),
            'desc' => esc_html__('می توانید در صفحه پست قسمت پست های مرتبط را مخفی کنید.', 'shokrino'),
            'default' => "true",
            'options' => array(
                'true' => __('نمایش', 'shokrino' ),
                'false' => __('مخفی', 'shokrino' ),
            ),
        ),
        array(
            'id' => 'blog_show_comments',
            'type' => 'button_set',
            'title' => esc_html__('نمایش یا مخفی کردن نظرات پست', 'shokrino'),
            'desc' => esc_html__('می توانید در صفحه پست قسمت دیدگاه ها را مخفی کنید.', 'shokrino'),
            'default' => "true",
            'options' => array(
                'true' => __('نمایش', 'shokrino' ),
                'false' => __('مخفی', 'shokrino' ),
            ),
        ),
        array(
            'id' => 'blog_who_can_comment',
            'type' => 'button_set',
            'title' => esc_html__('اجازه کامنت گذاری کاربران', 'shokrino'),
            'desc' => esc_html__('میتوانید مشخص کنید که کاربرانی که وارد حساب کاربری خود نشده اند، امکان کامنت گذاری نداشته باشند.', 'shokrino'),
            'default' => "all",
            'options' => array(
                'all' => __('همه', 'shokrino' ),
                'only_logged_in' => __('فقط کاربران لاگین شده', 'shokrino' ),
            ),
            'required' => array(
                array(
                    'blog_show_comments',
                    '=',
                    true
                )
            ),
        ),
    )
));

#Social Accounts
$img_social = get_template_directory_uri() . '/inc/core/admin-redux/templates/panel/img/social-img.png';
Redux::setSection($opt_name, array(
    'title' => esc_html__('شبکه های اجتماعی شما', 'shokrino'),
    'id' => 'social_settings',
    'icon' => 'el el-link',
    'desc' => esc_html__('لینک شبکه های اجتماعی خود را وارد کنید تا در فوتر و پیشخوان حساب کاربری و صفحه پست ها نمایش داده شود. (درصورت خالی گذاشتن هر کدام از لینک ها کلید مخفی خواهد شد)', 'shokrino'),
    'fields' => array(
        array(
            'id'       => 'opt-raw_social',
            'type'     => 'raw',
            'content'  => '<img src="'. $img_social .'">
						   <style>
						   #shokrino_options-opt-raw_social img {
								margin: auto;
								width: 65% !important;
								display: block;
							}
							</style>'
        ),
        array(
            'id' => 'instagram_btn_link',
            'type' => 'text',
            'title' => esc_html__('اینستاگرام', 'shokrino'),
            'subtitle' => esc_html__('لینک با //:Https شروع شود', 'shokrino'),
        ),
        array(
            'id' => 'telegram_btn_link',
            'type' => 'text',
            'title' => esc_html__('تلگرام', 'shokrino'),
            'subtitle' => esc_html__('لینک با //:Https شروع شود', 'shokrino'),
        ),
        array(
            'id' => 'whatsapp_btn_link',
            'type' => 'text',
            'title' => esc_html__('واتس اپ', 'shokrino'),
            'subtitle' => esc_html__('لینک با //:Https شروع شود', 'shokrino'),
        ),
        array(
            'id' => 'linkedin_btn_link',
            'type' => 'text',
            'title' => esc_html__('لینکداین', 'shokrino'),
            'subtitle' => esc_html__('لینک با //:Https شروع شود', 'shokrino'),
        ),
        array(
            'id' => 'youtube_btn_link',
            'type' => 'text',
            'title' => esc_html__('یوتیوب', 'shokrino'),
            'subtitle' => esc_html__('لینک با //:Https شروع شود', 'shokrino'),
        ),
        array(
            'id' => 'twitter_btn_link',
            'type' => 'text',
            'title' => esc_html__('توییتر', 'shokrino'),
            'subtitle' => esc_html__('لینک با //:Https شروع شود', 'shokrino'),
        ),
        array(
            'id' => 'facebook_btn_link',
            'type' => 'text',
            'title' => esc_html__('فیس بوک', 'shokrino'),
            'subtitle' => esc_html__('لینک با //:Https شروع شود', 'shokrino'),
        ),
        array(
            'id' => 'aparat_btn_link',
            'type' => 'text',
            'title' => esc_html__('آپارات', 'shokrino'),
            'subtitle' => esc_html__('لینک با //:Https شروع شود', 'shokrino'),
        ),
        array(
            'id' => 'eita_btn_link',
            'type' => 'text',
            'title' => esc_html__('پیامرسان ایرانی ایتا', 'shokrino'),
            'subtitle' => esc_html__('لینک با //:Https شروع شود', 'shokrino'),
        ),
        array(
            'id' => 'igap_btn_link',
            'type' => 'text',
            'title' => esc_html__('پیامرسان ایرانی آیگپ', 'shokrino'),
            'subtitle' => esc_html__('لینک با //:Https شروع شود', 'shokrino'),
        ),
        array(
            'id' => 'sorush_btn_link',
            'type' => 'text',
            'title' => esc_html__('پیامرسان ایرانی سروش', 'shokrino'),
            'subtitle' => esc_html__('لینک با //:Https شروع شود', 'shokrino'),
        ),
    )

));


# My Account Settings
$img_panel = get_template_directory_uri() . '/inc/core/admin-redux/templates/panel/img/notification_myaccount.png';
$img_peyvand_yekta = get_template_directory_uri() . '/inc/core/admin-redux/templates/panel/img/peyvand-yekta.png';
Redux::setSection($opt_name, array(
    'title' => esc_html__('تنظیمات پنل کاربری', 'shokrino'),
    'id' => 'account_settings',
    'icon' => 'el el-user',
    'fields' => array(
        array(
            'id' => 'logout_url',
            'type' => 'text',
            'title' => 'لینک هدایت کاربر بعد از خروج',
            'subtitle' => 'کاربر سایت بعد از خروج از حساب کاربری به این لینک هدایت میشود',
        ),
        array(
            'id' => 'date_show',
            'type' => 'switch',
            'title' => 'نمایش تاریخ در هدر',
            'subtitle' => 'تاریخ شمسی در هدر پنل کاربری نمایش داده شود یا غیرفعال باشد',
            'default' => true,
            'on' => esc_html__('نمایش', 'shokrino'),
            'off' => esc_html__('مخفی', 'shokrino')
        ),
        array(
            'id' => 'search_show',
            'type' => 'switch',
            'title' => 'نمایش جستجو در هدر',
            'subtitle' => 'باکس جستجو در هدر پنل کاربری نمایش داده شود یا غیرفعال باشد',
            'default' => true,
            'on' => esc_html__('نمایش', 'shokrino'),
            'off' => esc_html__('مخفی', 'shokrino')
        ),
        array(
            'id' => 'home_show',
            'type' => 'switch',
            'title' => 'نمایش کلید خانه',
            'subtitle' => 'باکس کلید صفحه اصلی سایت در هدر پنل کاربری نمایش داده شود یا غیرفعال باشد',
            'default' => true,
            'on' => esc_html__('نمایش', 'shokrino'),
            'off' => esc_html__('مخفی', 'shokrino')
        ),
        array(
            'id' => 'notifications_header_show',
            'type' => 'switch',
            'title' => 'نمایش کلید اعلان ها',
            'subtitle' => 'باکس کلید اعلان ها در هدر پنل کاربری نمایش داده شود یا غیرفعال باشد',
            'default' => true,
            'on' => esc_html__('نمایش', 'shokrino'),
            'off' => esc_html__('مخفی', 'shokrino')
        ),
        array(
            'id' => 'remove_orders_woo',
            'type' => 'switch',
            'title' => 'حذف منوی سفارش ها',
            'subtitle' => 'با روشن کردن این گزینه منوی سفارش ها دیگر در پنل کاربری نمایش داده نمیشود',
            'default' => false,
            'on' => esc_html__('حذف شود', 'shokrino'),
            'off' => esc_html__('پیشفرض', 'shokrino')
        ),
        array(
            'id' => 'remove_bought_products_woo',
            'type' => 'switch',
            'title' => 'حذف منوی دوره های خریداری شده',
            'subtitle' => 'با روشن کردن این گزینه منوی دوره های خریداری شده دیگر در پنل کاربری نمایش داده نمیشود',
            'default' => false,
            'on' => esc_html__('حذف شود', 'shokrino'),
            'off' => esc_html__('پیشفرض', 'shokrino')
        ),
        array(
            'id' => 'remove_downloads_woo',
            'type' => 'switch',
            'title' => 'حذف منوی دانلود ها',
            'subtitle' => 'با روشن کردن این گزینه منوی دانلود ها دیگر در پنل کاربری نمایش داده نمیشود',
            'default' => false,
            'on' => esc_html__('حذف شود', 'shokrino'),
            'off' => esc_html__('پیشفرض', 'shokrino')
        ),
        array(
            'id' => 'remove_addresses_woo',
            'type' => 'switch',
            'title' => 'حذف منوی آدرس',
            'subtitle' => 'با روشن کردن این گزینه منوی آدرس دیگر در پنل کاربری نمایش داده نمیشود',
            'default' => false,
            'on' => esc_html__('حذف شود', 'shokrino'),
            'off' => esc_html__('پیشفرض', 'shokrino')
        ),
        array(
            'id' => 'notification_panel',
            'type' => 'switch',
            'title' => esc_html__('پیام مدیر سایت', 'shokrino'),
            'subtitle' => esc_html__('نمایش یا مخفی کردن پیام مدیر در صفحه حساب کاربری ووکامرس برای کاربران', 'shokrino'),
            'default' => false,
            'on' => esc_html__('نمایش', 'shokrino'),
            'off' => esc_html__('مخفی', 'shokrino')
        ),
        array(
            'id' => 'notification_panel_text',
            'type' => 'editor',
            'title' => esc_html__('محتوای پیام خود را وارد کنید تا به کاربر نمایش داده شود', 'shokrino'),
            'args'   => array(
                'textarea_rows'    => 10,
                'media_buttons' => false,
            ),
            'required' => array(
                array(
                    'notification_panel',
                    '=',
                    'true'
                ),
            )
        ),
        array(
            'id' => 'notifications_on_dashboard_enable',
            'type' => 'switch',
            'title' => esc_html__('نمایش اعلان ها', 'shokrino'),
            'subtitle' => esc_html__('نمایش یا مخفی کردن اعلانات عمومی و خصوصی در پیشخوان پنل کاربری', 'shokrino'),
            'default' => false,
            'on' => esc_html__('نمایش', 'shokrino'),
            'off' => esc_html__('مخفی', 'shokrino')
        ),
        array(
            'id' => 'orders_show',
            'type' => 'switch',
            'title' => 'نمایش آخرین سفارشات در پیشخوان',
            'subtitle' => 'با فعال سازی این گزینه، لیست دو سفارش اخیر کاربر در پیشخوان پنل کاربری نمایش داده میشوند',
            'default' => false,
            'on' => esc_html__('نمایش', 'shokrino'),
            'off' => esc_html__('مخفی', 'shokrino')
        ),
        array(
            'id' => 'tickets_show',
            'type' => 'switch',
            'title' => 'نمایش آخرین تیکت ها در پیشخوان',
            'subtitle' => 'با فعال سازی این گزینه، لیست دو تیکت اخیر کاربر در پیشخوان پنل کاربری نمایش داده میشوند',
            'default' => false,
            'on' => esc_html__('نمایش', 'shokrino'),
            'off' => esc_html__('مخفی', 'shokrino')
        ),
        array(
            'id' => 'show_coupon_panel',
            'type' => 'switch',
            'title' => esc_html__('نمایش کدتخفیف', 'shokrino'),
            'subtitle' => esc_html__('با فعال کردن این گزینه میتوانید یک کدتخفیف را در باکس متن بنویسید تا به کاربران نمایش داده شود', 'shokrino'),
            'default' => false,
            'on' => esc_html__('نمایش', 'shokrino'),
            'off' => esc_html__('مخفی', 'shokrino')
        ),
        array(
            'id' => 'coupon_panel_text',
            'type' => 'text',
            'title' => esc_html__('کدتخفیف', 'shokrino'),
            'subtitle' => esc_html__('کد تخفیفی را که میخواهید به کاربر نمایش داده شود در باکس زیر بنویسید', 'shokrino'),
            'required' => array(
                array(
                    'show_coupon_panel',
                    '=',
                    'true'
                ),
            )
        ),
    )

));

# SMS Settings

Redux::setSection($opt_name, array(
    'id' => 'sms_settings',
    'title' => 'تنظیمات ورود پیامکی',
    'icon' => 'el el-user',
    'fields' => array(
        array(
            'id' => 'mobile_login',
            'type' => 'switch',
            'title' => 'ورود و عضویت با پیامک',
            'subtitle' => 'کاربران فقط با شماره موبایل و کد تایید میتوانند وارد شوند یا ثبت نام کنند.',
            'default' => false,
            'on' => esc_html__('نمایش', 'shokrino'),
            'off' => esc_html__('مخفی', 'shokrino')
        ),
        array(
            'id' => 'panel-sms',
            'type' => 'select',
            'title' => 'پنل پیامکی',
            'subtitle' => 'شرکت پنل پیامکی را انتخاب نمایید',
            'default' => 'farazsms',
            'options' => array(
                'farazsms' => 'فراز اس ام اس',
                'modirpayamak' => 'مدیر پیامک',
                'maxsms' => 'مکس اس ام اس',
                'panelsmspro' => 'پنل اس ام اس پرو',
                'rangine' => 'رنگینه',
                'kavehnegar' => 'کاوه نگار',
                'farapayamak' => 'فرا پیامک',
                'melipayamak' => 'ملی پیامک',
                'smsir' => 'sms.ir',
            ),
            'required' => array(
                array('mobile_login', '=', true),
            ),
        ),
        array(
            'id' => 'username_sms',
            'type' => 'text',
            'title' => 'نام کاربری',
            'subtitle' => 'نام کاربری پنل پیامکی شما',
            'required' => array(
                array('panel-sms', '!=', 'kavehnegar'),
                array('panel-sms', '!=', 'smsir'),
                array('mobile_login', '=', true),
            ),
        ),
        array(
            'id' => 'password_sms',
            'type' => 'text',
            'title' => 'رمز عبور',
            'subtitle' => 'رمز عبور پنل پیامکی شما',
            'required' => array(
                array('panel-sms', '!=', 'kavehnegar'),
                array('panel-sms', '!=', 'smsir'),
                array('mobile_login', '=', true),
            ),
        ),
        array(
            'id' => 'sender_sms',
            'type' => 'text',
            'title' => 'شماره خط ارسال کننده',
            'required' => array(
                array('panel-sms', '!=', 'kavehnegar'),
                array('panel-sms', '!=', 'melipayamak'),
                array('panel-sms', '!=', 'farapayamak'),
                array('panel-sms', '!=', 'smsir'),
                array('mobile_login', '=', true),
            ),
        ),
        array(
            'id' => 'api_sms',
            'type' => 'text',
            'title' => 'توکن API',
            'subtitle' => 'api مورد نظر را از پنل پیامکی خود دریافت کنید',
            'required' => array(array('panel-sms', '!=', 'farazsms'),
                array('panel-sms', '!=', 'maxsms'),
                array('panel-sms', '!=', 'modirpayamak'),
                array('panel-sms', '!=', 'panelsmspro'),
                array('panel-sms', '!=', 'rangine'),
                array('panel-sms', '!=', 'melipayamak'),
                array('panel-sms', '!=', 'farapayamak'),
                array('mobile_login', '=', true),
            ),
        ),
        array(
            'id' => 'secret_sms',
            'type' => 'text',
            'title' => 'کد امنیتی',
            'subtitle' => 'کد secret در پنل پیامکی شما',
            'required' => array(
                array('panel-sms', '!=', 'farazsms'),
                array('panel-sms', '!=', 'maxsms'),
                array('panel-sms', '!=', 'modirpayamak'),
                array('panel-sms', '!=', 'panelsmspro'),
                array('panel-sms', '!=', 'rangine'),
                array('panel-sms', '!=', 'kavehnegar'),
                array('panel-sms', '!=', 'melipayamak'),
                array('panel-sms', '!=', 'farapayamak'),
                array('mobile_login', '=', true),
            ),
        ),
        array(
            'id' => 'templateid_sms',
            'type' => 'text',
            'title' => 'کد قالب',
            'subtitle' => 'کد قالبی که در پنل پیامکی برای ارسال سریع تعیین شده است را وارد نمایید',
            'required' => array(
                array('panel-sms', '!=', 'farazsms'),
                array('panel-sms', '!=', 'maxsms'),
                array('panel-sms', '!=', 'modirpayamak'),
                array('panel-sms', '!=', 'panelsmspro'),
                array('panel-sms', '!=', 'rangine'),
                array('panel-sms', '!=', 'kavehnegar'),
                array('panel-sms', '!=', 'melipayamak'),
                array('panel-sms', '!=', 'farapayamak'),
                array('mobile_login', '=', true),
            ),
        ),
        array(
            'id' => 'pattern_sms',
            'type' => 'text',
            'title' => 'کد پترن',
            'subtitle' => 'کد پترنی که برای ارسال سریع تعیین کرده اید وارد نمایید',
            'required' => array(
                array('mobile_login', '=', true),
            ),
        ),
        array(
            'id' => 'variable_sms',
            'type' => 'text',
            'title' => 'متغیر پترن',
            'subtitle' => 'نام متغیری که در پترن استفاده نموده اید را وارد نمایید (فقط یک متغیر برای نمایش کد تایید کافی است)',
            'required' => array(
                array('panel-sms', '!=', 'kavehnegar'),
                array('panel-sms', '!=', 'melipayamak'),
                array('panel-sms', '!=', 'farapayamak'),
                array('panel-sms', '!=', 'smsir'),
                array('mobile_login', '=', true),
            ),
        ),
        array(
            'id' => 'time_sms',
            'type' => 'text',
            'title' => 'زمان انقضای کد',
            'subtitle' => 'مدت زمان را به ثانیه وارد کنید. بعد از اتمام این زمان کد تایید ارسال شده دیگر معتبر نخواهد بود و بایستی کاربر مجددا روی کلید ثبت کلیک کند',
            'default' => 60,
            'required' => array(
                array('mobile_login', '=', true),
            ),
        ),
        array(
            'id' => 'can_skip_name',
            'type' => 'switch',
            'title' => 'دریافت نام و نام خانوادگی',
            'subtitle' => 'آیا دریافت نام و نام خانوادگی بعد از ثبت نام اجباری باشد؟',
            'default' => false,
            'on' => esc_html__('بله', 'shokrino'),
            'off' => esc_html__('خیر', 'shokrino')
        ),
    ),
));

# Courses Settings
$img_shop_page = get_template_directory_uri() . '/inc/core/admin-redux/templates/panel/img/shop-page.png';
$img_course_page = get_template_directory_uri() . '/inc/core/admin-redux/templates/panel/img/course-page.png';
$img_download_box = get_template_directory_uri() . '/inc/core/admin-redux/templates/panel/img/download-box.png';
$img_fixed_addtocart = get_template_directory_uri() . '/inc/core/admin-redux/templates/panel/img/fixed-addtocart.jpg';
Redux::setSection($opt_name, array(
    'title' => esc_html__('تنظیمات محصولات', 'shokrino'),
    'id' => 'courses',
    'icon' => 'el-icon-shopping-cart',
    'fields' => array(
        array(
            'id'       => 'opt-raw_shop',
            'type'     => 'raw',
            'content'  => '<h3>تنظیمات صفحه فروشگاه (همه محصولات)</h3>
						   <img src="'. $img_shop_page .'">
						   <style>
						   #shokrino_options-opt-raw_shop img {
								width: 75% !important;
								display: block;
								margin: auto;
							}
						   #shokrino_options-opt-raw_shop h3 {
								margin: 0;
							}
							#shokrino_options-opt-raw_shop {
								border: none !important;
								padding-bottom: 0;
							}
							</style>',
        ),
        array(
            'id' => 'show_sidebar_shop',
            'type' => 'button_set',
            'title' => esc_html__('سایدبار صفحه فروشگاه', 'shokrino'),
            'subtitle' => esc_html__('ستون کناری صفحه آرشیو محصولات نمایش داده شود؟', 'shokrino'),
            'default' => 'yes',
            'options' => array(
                'yes' => esc_html__('سایدبار داشته باشد', 'shokrino'),
                'no' => esc_html__('بدون سایدبار', 'shokrino'),
            )
        ),
        array(
            'id' => 'courses_columns',
            'type' => 'select',
            'title' => esc_html__('تعداد ستون محصولات', 'shokrino'),
            'subtitle' => esc_html__('در هر سطر چه تعداد محصول نمایش داد شود؟', 'shokrino'),
            'desc' => esc_html__('در صورت انتخاب چهار ستون سایدبار را غیرفعال کنید تا فضای کافی باقی بماند', 'shokrino'),
            'options' => array(
                '2' => esc_html__('دو ستون', 'shokrino'),
                '3' => esc_html__('سه ستون', 'shokrino'),
                '4' => esc_html__('چهار ستون', 'shokrino'),
            ),
            'default' => '3',
            'select2' => array('allowClear' => false)
        ),
        array(
            'id' => 'shop_per_page',
            'type' => 'text',
            'title' => esc_html__('تعداد محصولات', 'shokrino'),
            'subtitle' => esc_html__('حداکثر تعداد نمایش محصولات در هر صفحه آرشیو محصولات', 'shokrino'),
            'default' => '12',
            'min' => '1',
            'max' => '24',
        ),
        array(
            'id' => 'course_sale_tag',
            'title' => esc_html__('برچسب درصد تخفیف نمایش داده شود؟', 'shokrino'),
            'subtitle' => esc_html__('درصورت فعال بودن این گزینه اگر محصولی تخفیف دار وجود داشت برچسب درصد تخفیف نمایش داده خواهد شد', 'shokrino'),
            'type' => 'switch',
            'default' => true,
            'on' => esc_html__('بله', 'shokrino'),
            'off' => esc_html__('خیر', 'shokrino')
        ),
        array(
            'id' => 'courses_rating',
            'title' => esc_html__('امتیاز ستاره ای آرشیو محصولات', 'shokrino'),
            'subtitle' => esc_html__('با این گزینه می توانید امتیاز محصول در آرشیو محصولات را مخفی کنید یا نمایش دهید.', 'shokrino'),
            'type' => 'switch',
            'default' => true,
            'on' => esc_html__('نمایش', 'shokrino'),
            'off' => esc_html__('مخفی', 'shokrino')
        ),
        array(
            'id' => 'courses_teacher',
            'title' => esc_html__('نام مدرس در آرشیو دوره ها', 'shokrino'),
            'subtitle' => esc_html__('با این گزینه می توانید نام مدرس دوره را در آرشیو دوره ها مخفی کنید یا نمایش دهید.', 'shokrino'),
            'type' => 'switch',
            'default' => true,
            'on' => esc_html__('نمایش', 'shokrino'),
            'off' => esc_html__('مخفی', 'shokrino')
        ),
        array(
            'id' => 'courses_customer',
            'title' => esc_html__('نمایش تعداد دانشجویان دوره', 'shokrino'),
            'subtitle' => esc_html__('با این گزینه می توانید تعداد دانشجویان دوره را در آرشیو دوره ها مخفی کنید یا نمایش دهید.', 'shokrino'),
            'type' => 'switch',
            'default' => true,
            'on' => esc_html__('نمایش', 'shokrino'),
            'off' => esc_html__('مخفی', 'shokrino')
        ),
        array(
            'id' => 'courses_addtocart',
            'title' => esc_html__('نمایش قیمت محصول', 'shokrino'),
            'subtitle' => esc_html__('با این گزینه می توانید دکمه افزودن به سبد خرید را در آرشیو محصولات مخفی کنید یا نمایش دهید.', 'shokrino'),
            'type' => 'switch',
            'default' => true,
            'on' => esc_html__('نمایش', 'shokrino'),
            'off' => esc_html__('مخفی', 'shokrino')
        ),
        array(
            'id' => 'courses_price',
            'title' => esc_html__('نمایش قیمت محصول', 'shokrino'),
            'subtitle' => esc_html__('با این گزینه می توانید قیمت محصول را در آرشیو محصولات مخفی کنید یا نمایش دهید.', 'shokrino'),
            'type' => 'switch',
            'default' => true,
            'on' => esc_html__('نمایش', 'shokrino'),
            'off' => esc_html__('مخفی', 'shokrino')
        ),
        array(
            'id'   =>'divider_shop',
            'type' => 'divide'
        ),
        array(
            'id'       => 'opt-raw_course_shop',
            'type'     => 'raw',
            'content'  => '<h3>تنظیمات صفحه دوره</h3>
						   <img src="'. $img_course_page .'">
						   <style>
						   #shokrino_options-opt-raw_course_shop img {
								width: 75% !important;
								display: block;
								margin: auto;
							}
						   #shokrino_options-opt-raw_course_shop h3 {
								margin: 0;
							}
							#shokrino_options-opt-raw_course_shop {
								border: none !important;
								padding-bottom: 0;
							}
							</style>',
        ),
        array(
            'id' => 'add_to_cart_text',
            'type' => 'text',
            'default' => esc_html__('ثبت نام در دوره', 'shokrino'),
            'title' => esc_html__('متن دکمه "ثبت نام در دوره"', 'shokrino'),
            'desc' => esc_html__('برای سفارشی سازی متن دکمه "افزودن به سبد خرید" در صفحه محصول در این قسمت متن را وارد کنید. (درصورت خالی بودن بصورت پیشفرض "ثبت نام در دوره" استفاده میشود)', 'shokrino'),
        ),
        array(
            'id' => 'course_student_text',
            'type' => 'text',
            'default' => esc_html__('دانشجوی دوره هستید!', 'shokrino'),
            'title' => esc_html__('متن دکمه "دانشجوی دوره هستید"', 'shokrino'),
            'subtitle' => esc_html__('این متن زمانی که کاربر دوره را خریداری کرده است جایگزین ثبت نام میشود', 'shokrino'),
            'desc' => esc_html__('برای سفارشی سازی متن دکمه "دانشجوی دوره هستید" در صفحه محصول در این قسمت متن را وارد کنید. (درصورت خالی بودن بصورت پیشفرض "دانشجوی دوره هستید!" استفاده میشود)', 'shokrino'),
        ),
        array(
            'id' => 'course_downloads',
            'title' => esc_html__('باکس فایل های دانلودی دوره', 'shokrino'),
            'subtitle' => esc_html__('نمایش و مخفی کردن بخش دریافت فایل های دوره (درصورت فعال بودن این بخش فقط به دانشجویان (خریداران) دوره نمایش داده خواهد شد)', 'shokrino'),
            'type' => 'switch',
            'default' => true,
            'on' => esc_html__('نمایش', 'shokrino'),
            'off' => esc_html__('مخفی', 'shokrino')
        ),
        array(
            'id'       => 'opt-raw_download_box',
            'type'     => 'raw',
            'content'  => '<h4>برای نمایش لینک های دانلود در این باکس در قسمت ویرایش محصول در کنار قسمت مشخص کردن قیمت محصول ، محصول دانلودی را انتخاب کرده و لینک ها را وارد کنید.</h4>
						   <img src="'. $img_download_box .'">
						   <style>
						   </style>',
            'required' => array(
                array(
                    'course_downloads',
                    '=',
                    1,
                )
            )
        ),
        array(
            'id' => 'course_students',
            'title' => esc_html__('تعداد دانشجویان دوره', 'shokrino'),
            'subtitle' => esc_html__('نمایش یا مخفی کردن تعداد دانشجویان دوره در ساید بار صفحه محصول', 'shokrino'),
            'type' => 'switch',
            'default' => true,
            'on' => esc_html__('نمایش', 'shokrino'),
            'off' => esc_html__('مخفی', 'shokrino')
        ),
        array(
            'id' => 'course_students_text',
            'type' => 'text',
            'default' => 'نفر، با این دوره مهارت کسب کرده اند',
            'title' => esc_html__('متن تعداد دانشجویان', 'shokrino'),
            'desc' => esc_html__('برای سفارشی سازی متن زیر تعداد دانشجویان در صفحه محصول در این قسمت متن را وارد کنید. (درصورت خالی بودن بصورت پیشفرض "نفر، با این دوره مهارت کسب کرده اند" استفاده میشود)', 'shokrino'),
            'required' => array(
                array(
                    'course_students',
                    '=',
                    1,
                )
            )
        ),
        array(
            'id' => 'course_detail_reviews',
            'title' => esc_html__('نمایش امتیاز دانشجویان', 'shokrino'),
            'subtitle' => esc_html__('نمایش و مخفی کردن باکس امتیازات دانشجویان بصورت عددی و ستاره ای در ساید بار', 'shokrino'),
            'type' => 'switch',
            'default' => true,
            'on' => esc_html__('نمایش', 'shokrino'),
            'off' => esc_html__('مخفی', 'shokrino')
        ),
        array(
            'id' => 'product-review-label',
            'title' => esc_html__('نمایش لیبل بالای نظرات محصول', 'shokrino'),
            'subtitle' => esc_html__('نمایش و مخفی کردن عنوان مدرس دوره یا خریدار محصول از بالای نظرات', 'shokrino'),
            'type' => 'switch',
            'default' => true,
            'on' => esc_html__('نمایش', 'shokrino'),
            'off' => esc_html__('مخفی', 'shokrino')
        ),
        array(
            'id' => 'fixed_addtocart',
            'title' => esc_html__('دکمه ثبت نام شناور', 'shokrino'),
            'subtitle' => esc_html__('نمایش و مخفی کردن کلید ثبت نام شناور در صفحه محصول (فقط در حالت موبایل و تبلت ، به کاربرانی که دوره را خریداری نکرده اند نمایش داده میشود)', 'shokrino'),
            'type' => 'switch',
            'default' => true,
            'on' => esc_html__('نمایش', 'shokrino'),
            'off' => esc_html__('مخفی', 'shokrino')
        ),
        array(
            'id'       => 'opt-raw_fixed_addtocart',
            'type'     => 'raw',
            'content'  => '<img src="'. $img_fixed_addtocart .'">',
            'required' => array(
                array(
                    'fixed_addtocart',
                    '=',
                    1,
                )
            )
        ),
        array(
            'id' => 'fixed_add_to_cart_text',
            'type' => 'text',
            'default' => 'همین الان ثبت نام کن',
            'title' => esc_html__('متن دکمه  ثبت نام شناور', 'shokrino'),
            'desc' => esc_html__('برای سفارشی سازی متن دکمه ثبت نام شناور در صفحه محصول در این قسمت متن را وارد کنید. (درصورت خالی بودن بصورت پیشفرض "همین الان ثبت نام کن" استفاده میشود)', 'shokrino'),
            'required' => array(
                array(
                    'fixed_addtocart',
                    '=',
                    1,
                )
            )
        ),
    )) );

#Cart and Checkout Page
$img_checkout = get_template_directory_uri() . '/inc/core/admin-redux/templates/panel/img/course-checkout.png';
Redux::setSection($opt_name, array(
    'title' => esc_html__('تنظیمات صفحه پرداخت', 'shokrino'),
    'id' => 'checkout',
    'icon' => 'el el-usd',
    'fields' => array(
        array(
            'id'       => 'opt-raw_course_checkout',
            'type'     => 'raw',
            'content'  => '<img src="'. $img_checkout .'">
						   <style>
						   #shokrino_options-opt-raw_course_shop img {
								width: 75% !important;
								display: block;
								margin: auto;
							}
						   #shokrino_options-opt-raw_course_shop h3 {
								margin: 0;
							}
							#shokrino_options-opt-raw_course_shop {
								border: none !important;
								padding-bottom: 0;
							}
							</style>',
        ),
        array(
            'id' => 'proccesing_orders_to_completed_wc',
            'type' => 'button_set',
            'title' => esc_html__('تکمیل شدن خودکار سفارش', 'shokrino'),
            'desc' => esc_html__('سفارشات درحال انجام بصورت خودکار به تکمیل شده تغییر پیدا میکنند و نیازی نیست شما آن ها را دستی تغییر دهید', 'shokrino'),
            'default' => "true",
            'options' => array(
                'true' => __('فعال', 'shokrino' ),
                'false' => __('غیر فعال', 'shokrino' ),
            ),
        ),
        array(
            'id' => 'cart-continue_text',
            'type' => 'text',
            'default' => 'پرداخت سفارش',
            'title' => esc_html__('متن دکمه انتهای صفحه پرداخت', 'shokrino'),
            'desc' => esc_html__('برای سفارشی سازی متن دکمه پرداخت سفارش در صفحه پرداخت اینجا متن مورد نظر را وارد کنید.', 'shokrino'),
        ),
        array(
            'id' => 'name-field',
            'title' => esc_html__('فیلد نام', 'shokrino'),
            'subtitle' => esc_html__('نمایش و مخفی کردن فیلد نام کاربر در فرم پرداخت', 'shokrino'),
            'type' => 'switch',
            'default' => true,
            'on' => esc_html__('نمایش', 'shokrino'),
            'off' => esc_html__('مخفی', 'shokrino')
        ),
        array(
            'id' => 'lastname-field',
            'title' => esc_html__('فیلد نام خانوادگی', 'shokrino'),
            'subtitle' => esc_html__('نمایش و مخفی کردن فیلد نام خانوادگی کاربر در فرم پرداخت', 'shokrino'),
            'type' => 'switch',
            'default' => true,
            'on' => esc_html__('نمایش', 'shokrino'),
            'off' => esc_html__('مخفی', 'shokrino')
        ),
        array(
            'id' => 'company-field',
            'title' => esc_html__('فیلد نام شرکت', 'shokrino'),
            'subtitle' => esc_html__('نمایش و مخفی کردن فیلد نام شرکت  در فرم پرداخت', 'shokrino'),
            'type' => 'switch',
            'default' => false,
            'on' => esc_html__('نمایش', 'shokrino'),
            'off' => esc_html__('مخفی', 'shokrino')
        ),
        array(
            'id' => 'address-field',
            'title' => esc_html__('فیلد آدرس', 'shokrino'),
            'subtitle' => esc_html__('نمایش و مخفی کردن فیلد آدرس در فرم پرداخت', 'shokrino'),
            'type' => 'switch',
            'default' => false,
            'on' => esc_html__('نمایش', 'shokrino'),
            'off' => esc_html__('مخفی', 'shokrino')
        ),
        array(
            'id' => 'address2-field',
            'title' => esc_html__('فیلد ادامه آدرس', 'shokrino'),
            'subtitle' => esc_html__('نمایش و مخفی کردن فیلد ادامه آدرس در فرم پرداخت', 'shokrino'),
            'type' => 'switch',
            'default' => false,
            'on' => esc_html__('نمایش', 'shokrino'),
            'off' => esc_html__('مخفی', 'shokrino')
        ),
        array(
            'id' => 'country-field',
            'title' => esc_html__('فیلد کشور / منطقه', 'shokrino'),
            'subtitle' => esc_html__('نمایش و مخفی کردن فیلد کشور در فرم پرداخت', 'shokrino'),
            'type' => 'switch',
            'default' => false,
            'on' => esc_html__('نمایش', 'shokrino'),
            'off' => esc_html__('مخفی', 'shokrino')
        ),
        array(
            'id' => 'state-field',
            'title' => esc_html__('فیلد استان', 'shokrino'),
            'subtitle' => esc_html__('نمایش و مخفی کردن فیلد استان در فرم پرداخت', 'shokrino'),
            'type' => 'switch',
            'default' => false,
            'on' => esc_html__('نمایش', 'shokrino'),
            'off' => esc_html__('مخفی', 'shokrino')
        ),
        array(
            'id' => 'city-field',
            'title' => esc_html__('فیلد شهر', 'shokrino'),
            'subtitle' => esc_html__('نمایش و مخفی کردن فیلد شهر در فرم پرداخت', 'shokrino'),
            'type' => 'switch',
            'default' => false,
            'on' => esc_html__('نمایش', 'shokrino'),
            'off' => esc_html__('مخفی', 'shokrino')
        ),
        array(
            'id' => 'postcode-field',
            'title' => esc_html__('فیلد کدپستی', 'shokrino'),
            'subtitle' => esc_html__('نمایش و مخفی کردن فیلد کدپستی در فرم پرداخت', 'shokrino'),
            'type' => 'switch',
            'default' => false,
            'on' => esc_html__('نمایش', 'shokrino'),
            'off' => esc_html__('مخفی', 'shokrino')
        ),
        array(
            'id' => 'phone-field',
            'title' => esc_html__('فیلد تلفن', 'shokrino'),
            'subtitle' => esc_html__('نمایش و مخفی کردن فیلد شماره تماس کاربر در فرم پرداخت', 'shokrino'),
            'type' => 'switch',
            'default' => true,
            'on' => esc_html__('نمایش', 'shokrino'),
            'off' => esc_html__('مخفی', 'shokrino')
        ),
        array(
            'id' => 'email-field',
            'title' => esc_html__('فیلد ایمیل', 'shokrino'),
            'subtitle' => esc_html__('نمایش و مخفی کردن فیلد ایمیل کاربر در فرم پرداخت', 'shokrino'),
            'type' => 'switch',
            'default' => true,
            'on' => esc_html__('نمایش', 'shokrino'),
            'off' => esc_html__('مخفی', 'shokrino')
        ),
        array(
            'id' => 'additional-desc-field',
            'title' => esc_html__('فیلد توضیحات سفارش', 'shokrino'),
            'subtitle' => esc_html__('نمایش و مخفی کردن فیلد توضیحات تکمیلی سفارش در انتهای فرم پرداخت', 'shokrino'),
            'type' => 'switch',
            'default' => true,
            'on' => esc_html__('نمایش', 'shokrino'),
            'off' => esc_html__('مخفی', 'shokrino')
        ),
    )) );

Redux::setSection($opt_name, array(
    'title' => esc_html__('حالت تعمیر (آفلاین)', 'shokrino'),
    'desc' => esc_html__('دقت داشته باشید که این یک حالت تعمیر ساده است و استایلی ندارد در صورت نیاز به امکانات بیشتر از افزونه های حالت تعمیر دیگر استفاده کنید', 'shokrino'),
    'id' => 'maintenace',
    'icon' => 'el el-cog',
    'fields' => array(
        array(
            'id' => 'maintenace-mode',
            'title' => esc_html__('حالت تعمیر', 'shokrino'),
            'subtitle' => esc_html__('اگر میخواهید سایت برای کاربران عادی نمایش داده نشود و فقط برای مدیر قابل نمایش باشد این گزینه را فعال کنید', 'shokrino'),
            'type' => 'switch',
            'default' => false,
            'on' => esc_html__('فعال', 'shokrino'),
            'off' => esc_html__('خاموش', 'shokrino')
        ),
        array(
            'id' => 'maintenace-logo-show',
            'title' => esc_html__('لوگو سایت', 'shokrino'),
            'subtitle' => esc_html__('لوگو سایت (که در تنظیمات اصلی انتخاب کرده اید) در صفحه حالت آفلاین نمایش داده میشود', 'shokrino'),
            'type' => 'switch',
            'default' => true,
            'on' => esc_html__('فعال', 'shokrino'),
            'off' => esc_html__('خاموش', 'shokrino'),
            'required' => array(
                array(
                    'maintenace-mode',
                    '=',
                    'true',
                )
            )
        ),
        array(
            'id' => 'maintenace-mode-date-show',
            'title' => esc_html__('شمارنده معکوس', 'shokrino'),
            'subtitle' => esc_html__('میتوانید یک شمارنده معکوس قرار دهید که چه روزی و چه ساعنی سایت مجددا فعال میشود', 'shokrino'),
            'type' => 'switch',
            'default' => false,
            'on' => esc_html__('فعال', 'shokrino'),
            'off' => esc_html__('خاموش', 'shokrino'),
            'required' => array(
                array(
                    'maintenace-mode',
                    '=',
                    'true',
                )
            )
        ),
        array(
            'id'          => 'maintenace-mode-date',
            'type'        => 'date',
            'title'       => esc_html__('انتخاب تاریخ', 'shokrino'),
            'subtitle'    => esc_html__('تاریخی که شمارنده تا آن میشمارد را به میلادی انتخاب کنید', 'shokrino'),
            'placeholder' => 'برای باز شدن تقویم اینجا کلیک کنید',
            'required' => array(
                array(
                    'maintenace-mode',
                    '=',
                    'true',
                ),
                array(
                    'maintenace-mode-date-show',
                    '=',
                    'true',
                )
            )
        ),
        array(
            'id'          => 'maintenace-mode-time',
            'type'        => 'text',
            'title'       => esc_html__('انتخاب ساعت', 'shokrino'),
            'subtitle'    => esc_html__('ساعتی که شمارنده تا آن زمان میشمارد را وارد کنید (برای مثال 22:30:50)', 'shokrino'),
            'desc'    => esc_html__('میتوانید خالی بگذارید تا بصورت پیشفرض ساعت 24:00:00 درنظر گرفته شود', 'shokrino'),
            'placeholder' => 'وارد کردن ساعت اختیاری است',
            'required' => array(
                array(
                    'maintenace-mode',
                    '=',
                    'true',
                ),
                array(
                    'maintenace-mode-date-show',
                    '=',
                    'true',
                )
            )
        ),
        array(
            'id' => 'title-page-maintenace-mode',
            'type' => 'text',
            'title' => 'عنوان صفحه',
            'subtitle' => 'این عنوان در تب مروگر نمایش داده میشود',
            'default' => 'درحال بروزرسانی',
            'required' => array(
                array(
                    'maintenace-mode',
                    '=',
                    'true',
                )
            )
        ),
        array(
            'id' => 'heading-maintenace-mode',
            'type' => 'text',
            'title' => 'عنوان حالت آفلاین',
            'default' => 'سایت درحال بروزرسانی است',
            'required' => array(
                array(
                    'maintenace-mode',
                    '=',
                    'true',
                )
            )
        ),
        array(
            'id' => 'text-maintenace-mode',
            'type' => 'text',
            'title' => 'متن حالت آفلاین',
            'default' => 'لطفا بعدا مراجعه کنید',
            'required' => array(
                array(
                    'maintenace-mode',
                    '=',
                    'true',
                )
            )
        ),
        array(
            'id' => 'offline-bg-color',
            'type' => 'color',
            'title' => esc_html__('رنگ پس زمینه صفحه آفلاین', 'shokrino'),
            'validate' => 'color',
            'transparent' => false,
            'default' => '#0096ff',
            'required' => array(
                array(
                    'maintenace-mode',
                    '=',
                    'true',
                )
            )
        ),
        array(
            'id' => 'offline-box-bg-color',
            'type' => 'color',
            'title' => esc_html__('رنگ زمینه باکس داخلی صفحه آفلاین', 'shokrino'),
            'validate' => 'color',
            'transparent' => false,
            'default' => '#ffffff',
            'required' => array(
                array(
                    'maintenace-mode',
                    '=',
                    'true',
                )
            )
        ),
        array(
            'id' => 'offline-txt-bg-color',
            'type' => 'color',
            'title' => esc_html__('رنگ متن های باکس داخلی صفحه آفلاین', 'shokrino'),
            'validate' => 'color',
            'transparent' => false,
            'default' => '#294559',
            'required' => array(
                array(
                    'maintenace-mode',
                    '=',
                    'true',
                )
            )
        ),
        array(
            'id' => 'offline-count-down-bg-color',
            'type' => 'color',
            'title' => esc_html__('رنگ زمینه باکس شمارنده صفحه آفلاین', 'shokrino'),
            'validate' => 'color',
            'transparent' => false,
            'default' => '#0096ff',
            'required' => array(
                array(
                    'maintenace-mode',
                    '=',
                    'true',
                ),
                array(
                    'maintenace-mode-date-show',
                    '=',
                    'true',
                )
            )
        ),
        array(
            'id' => 'offline-count-down-txt-color',
            'type' => 'color',
            'title' => esc_html__('رنگ متن های باکس شمارنده صفحه آفلاین', 'shokrino'),
            'validate' => 'color',
            'transparent' => false,
            'default' => '#ffffff',
            'required' => array(
                array(
                    'maintenace-mode',
                    '=',
                    'true',
                ),
                array(
                    'maintenace-mode-date-show',
                    '=',
                    'true',
                )
            )
        ),
    )) );

/////////////////////////////////////////////////////////////////////////////////////////////////////////////



// -> START Pro Fields.
if ( class_exists( 'Redux_Pro' ) ) {
    Redux::set_section(
        $opt_name,
        array(
            'title' => esc_html__( 'Redux Pro Fields', 'shokrino' ),
            'id'    => 'redux-pro-fields',
            'icon'  => 'el el-redux',
            'class' => 'pro_highlight',
            'desc'  => esc_html__( 'For full documentation on this field, visit: ', 'shokrino' ) . '<a href="https://devs.redux.io/premium/" target="_blank">https://devs.redux.io/premium/</a>',
        )
    );

    require_once Redux_Core::$dir . '../sample/sections/pro-fields/accordion.php';
    require_once Redux_Core::$dir . '../sample/sections/pro-fields/color-scheme.php';
    require_once Redux_Core::$dir . '../sample/sections/pro-fields/custom-fonts.php';
    require_once Redux_Core::$dir . '../sample/sections/pro-fields/date-time-picker.php';
    require_once Redux_Core::$dir . '../sample/sections/pro-fields/google-maps.php';
    require_once Redux_Core::$dir . '../sample/sections/pro-fields/icon-select.php';
    require_once Redux_Core::$dir . '../sample/sections/pro-fields/js-button.php';
    require_once Redux_Core::$dir . '../sample/sections/pro-fields/multi-media.php';
    require_once Redux_Core::$dir . '../sample/sections/pro-fields/repeater.php';
    require_once Redux_Core::$dir . '../sample/sections/pro-fields/social-profiles.php';
    require_once Redux_Core::$dir . '../sample/sections/pro-fields/taxonomy.php';
    require_once Redux_Core::$dir . '../sample/sections/pro-fields/users.php';
    require_once Redux_Core::$dir . '../sample/sections/pro-fields/widget-areas.php';
}

/**
 * Metaboxes
 */

require_once SHKRN_CORE . '/admin-redux/metaboxes.php';

/**
 * Raw README
 */
/*if ( file_exists( $dir . '/../README.md' ) ) {
	$section = array(
		'icon'   => 'el el-list-alt',
		'title'  => esc_html__( 'Documentation', 'shokrino' ),
		'fields' => array(
			array(
				'id'           => 'opt-raw-documentation',
				'type'         => 'raw',
				'markdown'     => true,
				'content_path' => dirname( __FILE__ ) . '/../README.md', // FULL PATH, not relative please.
			),
		),
	);

	Redux::set_section( $opt_name, $section );
}*/
///////////////////////////////////////////////////////// Only Show In Customizer ///////////////////////////////////////////////////////////////
/*Redux::set_section(
	$opt_name,
	array(
		'icon'            => 'el el-list-alt',
		'title'           => esc_html__( 'Customizer Only', 'shokrino' ),
		'desc'            => '<p class="description">' . esc_html__( 'This Section should be visible only in Customizer', 'shokrino' ) . '</p>',
		'customizer_only' => true,
		'fields'          => array(
			array(
				'id'              => 'opt-customizer-only',
				'type'            => 'select',
				'title'           => esc_html__( 'Customizer Only Option', 'shokrino' ),
				'subtitle'        => esc_html__( 'The subtitle is NOT visible in customizer', 'shokrino' ),
				'desc'            => esc_html__( 'The field desc is NOT visible in customizer.', 'shokrino' ),
				'customizer_only' => true,
				'options'         => array(
					'1' => esc_html__( 'Opt 1', 'shokrino' ),
					'2' => esc_html__( 'Opt 2', 'shokrino' ),
					'3' => esc_html__( 'Opt 3', 'shokrino' ),
				),
				'default'         => '2',
			),
		),
	)
);*/
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*
 * <--- END SECTIONS
 */

/*
 * YOU MUST PREFIX THE FUNCTIONS BELOW AND ACTION FUNCTION CALLS OR OTHER CONFIGS MAY OVERRIDE YOUR CODE.
 */

/*
 * --> Action hook examples.
 */

// Function to test the compiler hook and demo CSS output.
// Above 10 is a priority, but 2 in necessary to include the dynamically generated CSS to be sent to the function.
// add_filter('redux/options/' . $opt_name . '/compiler', 'compiler_action', 10, 3);
//
// Change the arguments after they've been declared, but before the panel is created.
// add_filter('redux/options/' . $opt_name . '/args', 'change_arguments' );
//
// Change the default value of a field after it's been set, but before it's been useds.
// add_filter('redux/options/' . $opt_name . '/defaults', 'change_defaults' );
//
// Dynamically add a section. Can be also used to modify sections/fields.
// add_filter('redux/options/' . $opt_name . '/sections', 'dynamic_section');
// .
if ( ! function_exists( 'compiler_action' ) ) {
    /**
     * This is a test function that will let you see when the compiler hook occurs.
     * It only runs if a field's value has changed and compiler=>true is set.
     *
     * @param array  $options        Options values.
     * @param string $css            Compiler selector CSS values  compiler => array( CSS SELECTORS ).
     * @param array  $changed_values Any values changed since last save.
     */
    function compiler_action( array $options, string $css, array $changed_values ) {
        echo '<h1>The compiler hook has run!</h1>';
        echo '<pre>';
        // phpcs:ignore WordPress.PHP.DevelopmentFunctions
        print_r( $changed_values ); // Values that have changed since the last save.
        // echo '<br/>';
        // print_r($options); //Option values.
        // echo '<br/>';
        // print_r($css); // Compiler selector CSS values  compiler => array( CSS SELECTORS ).
        echo '</pre>';
    }
}

if ( ! function_exists( 'redux_validate_callback_function' ) ) {
    /**
     * Custom function for the callback validation referenced above
     *
     * @param array $field          Field array.
     * @param mixed $value          New value.
     * @param mixed $existing_value Existing value.
     *
     * @return array
     */
    function redux_validate_callback_function( array $field, $value, $existing_value ): array {
        $error   = false;
        $warning = false;

        // Do your validation.
        if ( 1 === (int) $value ) {
            $error = true;
            $value = $existing_value;
        } elseif ( 2 === (int) $value ) {
            $warning = true;
            $value   = $existing_value;
        }

        $return['value'] = $value;

        if ( true === $error ) {
            $field['msg']    = 'your custom error message';
            $return['error'] = $field;
        }

        if ( true === $warning ) {
            $field['msg']      = 'your custom warning message';
            $return['warning'] = $field;
        }

        return $return;
    }
}


if ( ! function_exists( 'dynamic_section' ) ) {
    /**
     * Custom function for filtering the sections array. Good for child themes to override or add to the sections.
     * Simply include this function in the child themes functions.php file.
     * NOTE: the defined constants for URLs, and directories will NOT be available at this point in a child theme,
     * so you must use get_template_directory_uri() if you want to use any of the built-in icons.
     *
     * @param array $sections Section array.
     *
     * @return array
     */
    function dynamic_section( array $sections ): array {
        $sections[] = array(
            'title'  => esc_html__( 'Section via hook', 'shokrino' ),
            'desc'   => '<p class="description">' . esc_html__( 'This is a section created by adding a filter to the sections array. Can be used by child themes to add/remove sections from the options.', 'shokrino' ) . '</p>',
            'icon'   => 'el el-paper-clip',

            // Leave this as a blank section, no options just some intro text set above.
            'fields' => array(),
        );

        return $sections;
    }
}

if ( ! function_exists( 'change_arguments' ) ) {
    /**
     * Filter hook for filtering the args.
     * Good for child themes to override or add to the args array. Can also be used in other functions.
     *
     * @param array $args Global arguments array.
     *
     * @return array
     */
    function change_arguments( array $args ): array {
        $args['dev_mode'] = true;

        return $args;
    }
}

if ( ! function_exists( 'change_defaults' ) ) {
    /**
     * Filter hook for filtering the default value of any given field. Very useful in development mode.
     *
     * @param array $defaults Default value array.
     *
     * @return array
     */
    function change_defaults( array $defaults ): array {
        $defaults['str_replace'] = esc_html__( 'Testing filter hook!', 'shokrino' );

        return $defaults;
    }
}

if ( ! function_exists( 'redux_custom_sanitize' ) ) {
    /**
     * Function to be used if the field santize argument.
     * Return value MUST be the formatted or cleaned text to display.
     *
     * @param string $value Value to evaluate or clean.  Required.
     *
     * @return string
     */
    function redux_custom_sanitize( string $value ): string {
        $return = '';

        foreach ( explode( ' ', $value ) as $w ) {
            foreach ( str_split( $w ) as $k => $v ) {
                if ( ( $k + 1 ) % 2 !== 0 && ctype_alpha( $v ) ) {
                    $return .= mb_strtoupper( $v );
                } else {
                    $return .= $v;
                }
            }

            $return .= ' ';
        }

        return $return;
    }
}
